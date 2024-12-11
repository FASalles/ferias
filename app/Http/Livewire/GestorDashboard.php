<?php

namespace App\Http\Livewire;

use Livewire\Component;

class GestorDashboard extends Component
{
    public $layout = 'layouts.app';  // Defina o layout aqui

    public function render()
    {
        return view('livewire.gestor-dashboard')
        ->layout('layouts.app'); // Aqui estamos referenciando o layout simplificado
    }
}
