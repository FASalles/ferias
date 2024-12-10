<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\VacationRequest;
use Carbon\Carbon;

class VacationRequest extends Component
{
    public $selectedDays = [];
    public $remainingDays = 30;
    public $vacationRequests = [];
    public $user;

    public function mount()
    {
        // Recuperar usuário logado
        $this->user = auth()->user();

        // Se o usuário for um gestor, recupera todos os pedidos de férias
        if ($this->user->isGestor()) {
            $this->vacationRequests = VacationRequest::all();
        }
    }

    public function selectDay($day)
    {
        $key = "day-{$day}";

        if (in_array($key, $this->selectedDays)) {
            $this->selectedDays = array_diff($this->selectedDays, [$key]);
            $this->remainingDays++;
        } else {
            $this->selectedDays[] = $key;
            $this->remainingDays--;
        }
    }

    public function submitRequest()
    {
        if ($this->remainingDays == 0) {
            // Salvar pedido de férias no banco de dados
            VacationRequest::create([
                'user_id' => $this->user->id,
                'days' => implode(",", $this->selectedDays),
                'status' => 'pendente', // Status inicial de pendente
            ]);
            session()->flash('message', 'Pedido de férias enviado com sucesso!');
        }
    }

    public function acceptRequest($requestId)
    {
        $request = VacationRequest::find($requestId);
        $request->status = 'aceito';
        $request->save();

        session()->flash('message', 'Pedido de férias aceito!');
        $this->vacationRequests = VacationRequest::all();
    }

    public function rejectRequest($requestId)
    {
        $request = VacationRequest::find($requestId);
        $request->status = 'recusado';
        $request->save();

        session()->flash('message', 'Pedido de férias recusado!');
        $this->vacationRequests = VacationRequest::all();
    }

    public function render()
    {
        return view('livewire.vacation-request');
    }
}
