<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class Calendar extends Component
{
    public $year = 2025;
    public $startMonth = 0;  // Índice do primeiro mês a ser exibido
    public $monthsPerRow = 4; // Quantos meses mostrar por linha
    public $selectedDays = []; // Array de dias selecionados (para manter vários dias clicados)
    public $remainingDays = 30; // Total de dias a serem selecionados

    public function render()
    {
        // Nomes dos meses em português
        $months = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        // Inicial dos dias da semana em português
        $daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        // Gerando os meses exibidos
        $monthsData = [];
        for ($i = $this->startMonth; $i < $this->startMonth + $this->monthsPerRow; $i++) {
            $monthIndex = $i % 12;
            $firstDay = Carbon::create($this->year, $monthIndex + 1, 1);
            $daysInMonth = $firstDay->daysInMonth;
            $startDay = $firstDay->dayOfWeek;

            $monthsData[] = [
                'name' => $months[$monthIndex], // Nome do mês em português
                'days' => $this->generateDays($startDay, $daysInMonth),
                'daysOfWeek' => $daysOfWeek, // Dias da semana em português
                'monthIndex' => $monthIndex
            ];
        }

        return view('livewire.calendar', compact('monthsData'));
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
        $days = array_fill(0, $startDay, null); // Preenche os dias em branco antes do primeiro dia
        $days = array_merge($days, range(1, $daysInMonth)); // Adiciona os dias do mês

        // Preenche os dias restantes do mês com null para completar a semana
        while (count($days) % 7 != 0) {
            $days[] = null;
        }

        return $days;
    }

    public function selectDay($day, $monthIndex)
    {
        // Usando um identificador único para cada dia: "monthIndex-day"
        $key = "{$monthIndex}-{$day}";

        // Verifica se o dia já está na lista de selecionados
        if (in_array($key, $this->selectedDays)) {
            // Se o dia já está selecionado, remove da lista e incrementa o contador de dias restantes
            $this->selectedDays = array_diff($this->selectedDays, [$key]);
            $this->remainingDays++;
        } else {
            // Se o dia não está na lista, adiciona e decrementa o contador de dias restantes
            $this->selectedDays[] = $key;
            $this->remainingDays--;
        }
    }
}
