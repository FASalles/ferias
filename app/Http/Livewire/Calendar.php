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

    // Impede desmarcar dias que já foram salvos no banco
    if (in_array($key, $this->savedDays)) {
        return;
    }

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
        // Verifica se o usuário está autenticado
        if (auth()->check()) {
            
        
            
    
            // Cria uma nova solicitação de férias
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
    
            // Mensagem de sucesso
            session()->flash('message', 'Solicitação de férias enviada com sucesso!');
        } else {
            // Se o usuário não estiver autenticado, exibe uma mensagem de erro
            session()->flash('message', 'Você precisa estar logado para enviar a solicitação de férias.');
        }
    }
    

}
