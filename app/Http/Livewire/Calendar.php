<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class Calendar extends Component
{
    public $year = 2025;
    public $months = [];
    public $userName = '';

    public function mount()
    {
        $this->generateCalendar($this->year);
    }

    private function generateCalendar($year)
    {
        $this->months = collect(range(1, 12))->map(function ($month) use ($year) {
            $date = Carbon::createFromDate($year, $month, 1);
            $daysInMonth = $date->daysInMonth;
            $firstDayOfWeek = $date->dayOfWeek;

            return [
                'name' => $date->format('F'),
                'days' => range(1, $daysInMonth),
                'firstDayOfWeek' => $firstDayOfWeek,
            ];
        });
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
