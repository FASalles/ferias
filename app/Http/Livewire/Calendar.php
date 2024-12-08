<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class Calendar extends Component
{
    public $year = 2025;
    public $startMonth = 0;
    public $monthsPerRow = 4;
    public $selectedDays = [];
    public $remainingDays = 30;

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

    return view('livewire.calendar', compact('monthsData'))
        ->layout('layouts.app'); // Aqui estamos referenciando o layout simplificado
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

        if (in_array($key, $this->selectedDays)) {
            $this->selectedDays = array_diff($this->selectedDays, [$key]);
            $this->remainingDays++;
        } else {
            $this->selectedDays[] = $key;
            $this->remainingDays--;
        }
    }

    public function sendVacationRequest()
    {
        // Simula o envio da solicitação de férias. Aqui, você pode adicionar a lógica para processar a solicitação.
        session()->flash('message', 'Solicitação de férias enviada com sucesso!');
    }
}
