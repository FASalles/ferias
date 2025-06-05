<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\User;
use App\Models\VacationRequest;
use MBarlow\Megaphone\Types\Important;

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

    public $showSearch = false;
    public $query = '';
    public $searchResults = [];

    public bool $showHolidays = false;

    private array $holidays = [
        '0-1', // 1º de janeiro (mês 0 = janeiro, dia 1)
        // Adicione mais feriados no formato "mêsIndex-dia"
    ];

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
            $this->activeFilter = null;
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

        $this->selectedDays = [];
        $this->vacationRequestSent = false;
        $this->loadUserVacations();
        $this->remainingDays = 5;

        session()->flash('message', 'Todos os dias de férias foram deletados com sucesso.');
        session()->flash('type', 'success');
    }

    public function toggleSearch()
    {
        $this->showSearch = !$this->showSearch;
        if (!$this->showSearch) {
            $this->query = '';
            $this->searchResults = [];
        }
    }

    public function updatedQuery()
    {
        if (strlen($this->query) > 2) {
            $this->searchResults = User::where('name', 'like', '%' . $this->query . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function toggleHolidays()
    {
        $this->showHolidays = !$this->showHolidays;
    }

    /**
     * Verifica se o dia informado é feriado (só se $showHolidays for true).
     * @param int $monthIndex (0-11)
     * @param int $day (1-31)
     * @return bool
     */
    public function isHoliday(int $monthIndex, int $day): bool
    {
        if (!$this->showHolidays) {
            return false;
        }
        $key = "{$monthIndex}-{$day}";
        return in_array($key, $this->holidays);
    }

    public function updatedShowHolidays($value)
{
    $feriados = [];

    if ($value) {
        foreach ($feriados as $dia) {
            if (!in_array($dia, $this->selectedDays)) {
                $this->selectedDays[] = $dia;
            }
        }
    } else {
        $this->selectedDays = array_filter(
            $this->selectedDays,
            fn($day) => !in_array($day, $feriados)
        );
    }

    // Força o Livewire a detectar a mudança no array
    $this->selectedDays = array_values($this->selectedDays);
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
                $reservedDaysQuery->whereRaw('0 = 1');
                break;
        }

        $reservedDaysRaw = $reservedDaysQuery->get();

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
            'showSearch' => $this->showSearch,
            'query' => $this->query,
            'searchResults' => $this->searchResults,
            'holidays' => $this->showHolidays ? $this->holidays : [],
            'showHolidays' => $this->showHolidays,
            'isHolidayCallback' => function($monthIndex, $day) {
                return $this->isHoliday($monthIndex, $day);
            },
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
        }

        $userId = auth()->id();

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

    public function notificarAdmins()
    {
        $admins = User::whereJsonContains('roles', 'admin')->get();

        if ($admins->isEmpty()) {
            session()->flash('message', 'Nenhum administrador encontrado.');
            session()->flash('type', 'error');
            return;
        }

        foreach ($admins as $admin) {
            $admin->notify(new Important(
                title: 'Solicitação de férias',
                body: '❶ – Você recebeu uma notificação de novo pedido de férias!'
            ));
        }
    }

    public function sendVacationRequestAndNotify()
    {
        $this->sendVacationRequest();

        if ($this->vacationRequestSent) {
            $this->notificarAdmins();
        }
    }
}
