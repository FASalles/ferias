<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\VacationRequest;

class Calendar extends Component
{
    public $year = 2025;
    public $startMonth = 0;
    public $monthsPerRow = 4;
    public $selectedDays = [];
    public $remainingDays = 5;
    public $savedDays = [];

    public $activeFilter = null; // 'all', 'disi', 'pe', 'my' ou null

    public $vacationRequestSent = false;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->loadUserVacations();
        $this->remainingDays = max(5 - count($this->savedDays), 0);
    }

    public function clearSelectedDays()
    {
        $this->selectedDays = [];
        $this->savedDays = [];
        $this->remainingDays = 5;
    }

    public function setFilter($filter)
    {
        if ($this->activeFilter === $filter) {
            $this->activeFilter = null; // desativa o filtro se clicar de novo
        } else {
            $this->activeFilter = $filter;
        }

        if ($this->activeFilter === 'my') {
            $this->loadUserVacations();
            $this->remainingDays = max(5 - count($this->savedDays), 0);
        }
    }

    private function loadUserVacations()
    {
        $this->savedDays = VacationRequest::where('user_id', auth()->id())
            ->get()
            ->flatMap(function ($vacation) {
                return json_decode($vacation->days, true);
            })
            ->toArray();
    }

    public function deleteUserVacationDays()
    {
        $userId = auth()->id();

        VacationRequest::where('user_id', $userId)->delete();

        // Atualizar visualmente
        $this->selectedDays = [];
        $this->vacationRequestSent = false;
        $this->loadUserVacations();

        // Recalcular os dias restantes
        $this->remainingDays = 5;

        session()->flash('message', 'Todos os dias de férias foram deletados com sucesso.');
        session()->flash('type', 'success');
    }

    public function render()
    {
        $months = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        $daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $monthsData = [];
        for ($i = $this->startMonth; $i < $this->startMonth + $this->monthsPerRow; $i++) {
            $monthIndex = $i % 12;
            $firstDay = Carbon::create($this->year, $monthIndex + 1, 1);
            $daysInMonth = $firstDay->daysInMonth;
            $startDay = $firstDay->dayOfWeek;

            $monthsData[] = [
                'name' => $months[$monthIndex],
                'days' => $this->generateDays($startDay, $daysInMonth),
                'daysOfWeek' => $daysOfWeek,
                'monthIndex' => $monthIndex
            ];
        }

        $reservedDaysQuery = VacationRequest::query();

        switch ($this->activeFilter) {
            case 'all':
                // mostra todas as férias (sem filtro por time)
                break;
            case 'disi':
                $reservedDaysQuery->where('team', 'DISI');
                break;
            case 'pe':
                $reservedDaysQuery->where('team', 'PE');
                break;
            case 'my':
                $reservedDaysQuery->where('user_id', auth()->id());
                break;
            default:
                // Nenhum filtro ativo, não mostra férias
                $reservedDaysQuery->whereRaw('0 = 1'); // consulta vazia
                break;
        }

        $reservedDaysRaw = $reservedDaysQuery->get();

        // Construir array chave: "monthIndex-day" => array de nomes que reservaram o dia
        $reservedDays = [];

        foreach ($reservedDaysRaw as $request) {
            $days = json_decode($request->days, true);
            if (is_array($days)) {
                foreach ($days as $dayKey) {
                    if (!isset($reservedDays[$dayKey])) {
                        $reservedDays[$dayKey] = [];
                    }
                    $reservedDays[$dayKey][] = $request->name;
                }
            }
        }

        return view('livewire.calendar', [
            'monthsData' => $monthsData,
            'reservedDays' => $reservedDays,
            'selectedDays' => $this->selectedDays,
            'savedDays' => $this->savedDays,
            'remainingDays' => $this->remainingDays,
            'vacationRequestSent' => $this->vacationRequestSent,
            'activeFilter' => $this->activeFilter,
        ])->layout('layouts.app');
    }

    public function nextMonths()
    {
        if ($this->startMonth + $this->monthsPerRow < 12) {
            $this->startMonth += $this->monthsPerRow;
        }
    }

    public function prevMonths()
    {
        if ($this->startMonth - $this->monthsPerRow >= 0) {
            $this->startMonth -= $this->monthsPerRow;
        }
    }

    private function generateDays($startDay, $daysInMonth)
    {
        $days = array_fill(0, $startDay, null);
        $days = array_merge($days, range(1, $daysInMonth));

        while (count($days) % 7 != 0) {
            $days[] = null;
        }

        return $days;
    }

    public function selectDay($day, $monthIndex)
    {
        $key = "{$monthIndex}-{$day}";

        if (in_array($key, $this->savedDays)) {
            return;
        }

        if (in_array($key, $this->selectedDays)) {
            $this->selectedDays = array_values(array_diff($this->selectedDays, [$key]));
            $this->remainingDays++;
        } else {
            if (count($this->selectedDays) >= 5) {
                session()->flash('message', 'Você não pode selecionar mais de 5 dias de férias.');
                session()->flash('type', 'warning');
                return;
            }

            $this->selectedDays[] = $key;
            $this->remainingDays--;
        }

        if (count($this->selectedDays) > 5) {
            $this->selectedDays = array_slice($this->selectedDays, 0, 5);
            $this->remainingDays = 0;
        }
    }

    public function sendVacationRequest()
    {
        if (!auth()->check()) {
            session()->flash('message', 'Você precisa estar logado para enviar a solicitação de férias.');
            session()->flash('type', 'error');
            return;

            $this->vacationRequestSent = true;

    // Emite um evento para o megaphone
    $this->emit('showMegaphoneNotification', 'Pedido de férias enviado com sucesso!');
        }

        $userId = auth()->id();

        // Buscar dias já reservados
        $existingDaysCount = VacationRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->flatMap(function ($vacation) {
                return json_decode($vacation->days, true);
            })
            ->unique()
            ->count();

        $newDaysCount = count($this->selectedDays);

        if ($existingDaysCount + $newDaysCount > 5) {
            session()->flash('message', 'Você não pode reservar mais que 5 dias de férias no total.');
            session()->flash('type', 'warning');
            return;
        }

        VacationRequest::create([
            'user_id' => $userId,
            'name' => auth()->user()->name,
            'status' => 'pending',
            'team' => auth()->user()->team,
            'role' => auth()->user()->roles,
            'days' => json_encode($this->selectedDays),
        ]);

        $this->selectedDays = [];
        $this->remainingDays = 5 - $existingDaysCount - $newDaysCount;
        $this->vacationRequestSent = true;

        session()->flash('message', 'Solicitação de férias enviada com sucesso!');
        session()->flash('type', 'success');

        $this->loadUserVacations();
    }
}
