<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Alert extends Component
{
    public bool $displayingModal = false;

    public array $state = [
        'title' => '',
        'message' => '',
    ];

    #[On('displayAlert')]
    public function display(string $title, string $message): void
    {
        $this->state['title'] = $title;
        $this->state['message'] = $message;

        $this->displayingModal = true;
    }

    public function render()
    {
        return view('livewire.alert');
    }
}
