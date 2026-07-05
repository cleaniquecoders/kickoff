<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Settings\G8DeskSettings;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Admin > Settings > g8desk Support.
 *
 * Manages the DB-stored g8desk support-widget configuration that powers the
 * native, SDK-free `<x-g8desk-support-widget />` component embedded in the
 * authenticated app layout. The widget secret is stored encrypted and is never
 * exposed to the browser — only the server-side HMAC signature is.
 */
#[Layout('components.layouts.app')]
class G8Desk extends Component
{
    public bool $enabled = false;

    public string $baseUrl = '';

    public string $publicKey = '';

    public string $widgetSecret = '';

    public function mount(): void
    {
        $this->authorize('manage.settings');

        $settings = app(G8DeskSettings::class);
        $this->enabled = $settings->enabled;
        $this->baseUrl = $settings->base_url;
        $this->publicKey = $settings->public_key;
        $this->widgetSecret = $settings->widget_secret;
    }

    public function save(): void
    {
        $this->authorize('manage.settings');

        // Livewire 4 needs inline rules. The keys/URL only matter when the widget
        // is switched on, so they're required (and the URL validated) only then.
        $rules = ['enabled' => ['boolean']];

        if ($this->enabled) {
            $rules['baseUrl'] = ['required', 'url'];
            $rules['publicKey'] = ['required', 'string'];
            $rules['widgetSecret'] = ['required', 'string'];
        }

        $this->validate($rules, [], [
            'baseUrl' => 'base URL',
            'publicKey' => 'public key',
            'widgetSecret' => 'widget secret',
        ]);

        $settings = app(G8DeskSettings::class);
        $settings->enabled = $this->enabled;
        $settings->base_url = $this->baseUrl;
        $settings->public_key = $this->publicKey;
        $settings->widget_secret = $this->widgetSecret;
        $settings->save();

        $this->dispatch('toast',
            type: 'success',
            message: __('g8desk support settings saved successfully!'),
            duration: 3000,
        );
    }

    public function render(): View
    {
        return view('livewire.admin.settings.g8desk');
    }
}
