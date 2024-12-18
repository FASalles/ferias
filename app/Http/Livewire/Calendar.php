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
    public $remainingDays = 30;
    public $savedDays = [];

    // Atributos para controlar os checkboxes
    public $showDisiVacations = false;
    public $showPeVacations = false;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Recupera os dias já salvos no banco para o usuário autenticado
        $this->savedDays = VacationRequest::where('user_id', auth()->id())
                        ->get()
                        ->flatMap(function ($vacation) {
                            return json_decode($vacation->days, true);
                        })
                        ->toArray();

        // Calcula os dias restantes considerando os dias já salvos
        $this->remainingDays = max(30 - count($this->savedDays), 0);
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

        // Filtra as reservas de acordo com a seleção de "DISI" e "PE"
        $reservedDays = VacationRequest::query()
            ->when($this->showDisiVacations, function ($query) {
                return $query->where('team', 'DISI');
            })
            ->when($this->showPeVacations, function ($query) {
                return $query->where('team', 'PE');
            })
            ->get()
            ->flatMap(function ($request) {
                return collect(json_decode($request->days))->mapWithKeys(function ($day) use ($request) {
                    return [$day => $request->name];
                });
            })->toArray();

        return view('livewire.calendar', [
            'monthsData' => $monthsData,
            'reservedDays' => $reservedDays,
            'selectedDays' => $this->selectedDays,
            'savedDays' => $this->savedDays,
            'remainingDays' => $this->remainingDays
        ])->layout('layouts.app');
    }

    // Lógica para navegação entre os meses
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

    // Método de geração dos dias para cada mês
    private function generateDays($startDay, $daysInMonth)
    {
        $days = array_fill(0, $startDay, null);
        $days = array_merge($days, range(1, $daysInMonth));

        while (count($days) % 7 != 0) {
            $days[] = null;
        }

        return $days;
    }

    // Método para seleção de dias
    public function selectDay($day, $monthIndex)
    {
        $key = "{$monthIndex}-{$day}";

        // Impede desmarcar dias que já foram salvos no banco
        if (in_array($key, $this->savedDays)) {
            return;
        }

        // Verifica se o dia já está selecionado e permite desmarcá-lo
        if (in_array($key, $this->selectedDays)) {
            $this->selectedDays = array_diff($this->selectedDays, [$key]);
            $this->remainingDays++;
        } else {
            // Verifica se a soma dos dias salvos e selecionados ultrapassa 30
            if (count($this->savedDays) + count($this->selectedDays) >= 30) {
                session()->flash('message', 'Você não pode selecionar mais de 30 dias no total.');
                return;
            }

            // Seleciona o novo dia
            $this->selectedDays[] = $key;
            $this->remainingDays--;
        }
    }


    // Método para enviar a solicitação de férias
    public function sendVacationRequest()
    {
        if (auth()->check()) {
            VacationRequest::create([
                'user_id' => auth()->id(),
                'name' => auth()->user()->name,
                'status' => 'pending',  // O status pode ser 'pending' por padrão
                'team' => auth()->user()->team,  // Adicionando o campo 'team' do usuário
                'role' => auth()->user()->roles,  // Adicionando o campo 'role' do usuário
                'days' => json_encode($this->selectedDays), // Armazena os dias selecionados como JSON
            ]);

            // Reseta os dias selecionados após a solicitação ser enviada
            $this->selectedDays = [];
            $this->remainingDays = 30;

            session()->flash('message', 'Solicitação de férias enviada com sucesso!');
        } else {
            session()->flash('message', 'Você precisa estar logado para enviar a solicitação de férias.');
        }
    }

    // Funções para alternar a visibilidade das férias de DISI e PE
    public function toggleDisiVacations()
    {
        $this->showDisiVacations = !$this->showDisiVacations;
    }

    public function togglePeVacations()
    {
        $this->showPeVacations = !$this->showPeVacations;
    }
}
