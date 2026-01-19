<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Confirm extends Component
{
    public bool $displayingModal = false;

    public array $state = [
        'title' => '',
        'message' => '',
        'return' => [
            'component' => '',
            'args' => [],
        ],
    ];

    #[On('displayConfirmation')]
    public function display(string $title, string $message, string $component, string $listener, mixed ...$params): void
    {
        $this->state['title'] = $title;
        $this->state['message'] = $message;
        $this->state['return'] = [
            'component' => $component,
            'listener' => $listener,
            'params' => $params,
        ];

        $this->displayingModal = true;
    }

    public function confirm(): void
    {
        $this->dispatch(
            $this->state['return']['listener'],
            ...$this->state['return']['params'],
        )->to($this->state['return']['component']);

        $this->displayingModal = false;
    }

    public function cancel(): void
    {
        $this->state = [
            'title' => '',
            'message' => '',
            'return' => [
                'component' => '',
                'args' => [],
            ],
        ];
        $this->displayingModal = false;
    }

    public function render()
    {
        return view('livewire.confirm');
    }
}
