<?php

namespace App\Http\Livewire;

use Livewire\Component;

class TesteLivewire extends Component
{
    public $contador = 0;

    public function incrementar()
    {
        $this->contador++;
    }

    public function render()
    {
        return view('livewire.teste-livewire');
    }
}

