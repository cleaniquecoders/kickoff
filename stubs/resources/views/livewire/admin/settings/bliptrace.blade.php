<div>
    {{-- Full-page Livewire component: pin the route so the trail survives wire updates. --}}
    <x-breadcrumbs class="mb-6" for="admin.settings.bliptrace" />

    <div>
        <flux:heading size="xl" level="1">{{ __('Error tracking') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Send unhandled exceptions to Bliptrace. Leave a field blank to use the BLIPTRACE_* environment value, or the SDK default.') }}</flux:text>
    </div>

    <flux:separator variant="subtle" class="my-6" />

    @php
        $inherited = fn (string $field) => $sources[$field] === 'ui' ? '' : (string) $effective[$field];
        $sourceLabel = ['ui' => __('saved here'), 'env' => __('from env'), 'default' => __('default')];
    @endphp

    <div class="max-w-3xl space-y-6">
        {{-- Live status + test event: reflects what the SDK is using right now. --}}
        <x-card>
            <x-card.header>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center">
                        <x-lucide-bug class="h-6 w-6 text-brand-500 me-3" />
                        <flux:heading size="lg">{{ __('Status') }}</flux:heading>
                    </div>
                    @if ($effective['enabled'])
                        <flux:badge color="green">{{ __('Capturing') }}</flux:badge>
                    @else
                        <flux:badge color="zinc">{{ __('Off') }}</flux:badge>
                    @endif
                </div>
            </x-card.header>
            <x-card.body class="space-y-4">
                <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div class="min-w-0">
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Ingest key') }}</dt>
                        <dd class="truncate font-mono text-zinc-900 dark:text-zinc-100">{{ $maskedKey ?? __('Not set') }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Endpoint') }}</dt>
                        <dd class="truncate text-zinc-900 dark:text-zinc-100">{{ $effective['endpoint'] }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Environment') }}</dt>
                        <dd class="truncate text-zinc-900 dark:text-zinc-100">{{ $effective['environment'] }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Release') }}</dt>
                        <dd class="truncate text-zinc-900 dark:text-zinc-100">{{ $effective['release'] ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($testResult)
                    <flux:callout :variant="$testResult['ok'] ? 'success' : 'danger'" :icon="$testResult['ok'] ? 'check' : 'circle-alert'" :heading="$testResult['message']" />
                @endif
            </x-card.body>
            <x-card.footer>
                <div class="flex flex-wrap items-center gap-3">
                    <flux:button wire:click="sendTestEvent" icon="zap" class="cursor-pointer">
                        {{ __('Send test event') }}
                    </flux:button>
                    <flux:text size="sm">{{ __('Uses the saved settings, not unsaved edits below.') }}</flux:text>
                </div>
            </x-card.footer>
        </x-card>

        <form wire:submit="save" class="space-y-6">
            <x-card>
                <x-card.header>
                    <flux:heading size="lg">{{ __('Capture') }}</flux:heading>
                </x-card.header>
                <x-card.body class="space-y-6">
                    <flux:field>
                        <div class="flex items-center justify-between gap-2">
                            <flux:label>{{ __('Error tracking') }}</flux:label>
                            @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources['enabled']])
                        </div>
                        <flux:radio.group wire:model="enabled" variant="segmented" class="w-full sm:w-auto">
                            <flux:radio value="" :label="__('Auto')" />
                            <flux:radio value="on" :label="__('On')" />
                            <flux:radio value="off" :label="__('Off')" />
                        </flux:radio.group>
                        <flux:description>{{ __('Auto follows BLIPTRACE_ENABLED and captures only when an ingest key is set. Nothing is sent without a key.') }}</flux:description>
                        <flux:error name="enabled" />
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center justify-between gap-2">
                            <flux:label>{{ __('Ingest key') }}</flux:label>
                            @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources['key']])
                        </div>
                        <flux:input wire:model="key" type="password" autocomplete="new-password"
                            :placeholder="$maskedKey ? $maskedKey.' ('.$sourceLabel[$sources['key']].')' : __('Paste the project ingest key')" />
                        <flux:description>{{ __('Stored encrypted and never shown again. Leave blank to keep the current key.') }}</flux:description>
                        <flux:error name="key" />
                    </flux:field>

                    @if ($keyStored)
                        <flux:checkbox wire:model="clearKey" :label="__('Remove the saved key and fall back to BLIPTRACE_KEY')" />
                    @endif

                    <flux:field>
                        <div class="flex items-center justify-between gap-2">
                            <flux:label>{{ __('Endpoint') }}</flux:label>
                            @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources['endpoint']])
                        </div>
                        <flux:input wire:model="endpoint" type="url" :placeholder="$inherited('endpoint')" />
                        <flux:description>{{ __('Ingest API base URL; events post to {endpoint}/v1/ingest.') }}</flux:description>
                        <flux:error name="endpoint" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <div class="flex items-center justify-between gap-2">
                                <flux:label>{{ __('Environment') }}</flux:label>
                                @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources['environment']])
                            </div>
                            <flux:input wire:model="environment" :placeholder="$inherited('environment')" />
                            <flux:error name="environment" />
                        </flux:field>

                        <flux:field>
                            <div class="flex items-center justify-between gap-2">
                                <flux:label>{{ __('Release') }}</flux:label>
                                @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources['release']])
                            </div>
                            <flux:input wire:model="release" :placeholder="$inherited('release') ?: 'my-app@1.0.0'" />
                            <flux:error name="release" />
                        </flux:field>
                    </div>
                </x-card.body>
            </x-card>

            <x-card>
                <x-card.header>
                    <flux:heading size="lg">{{ __('Delivery & payload') }}</flux:heading>
                </x-card.header>
                <x-card.body class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @foreach ([
                            ['sampleRate', 'sample_rate', __('Sample rate'), __('Share of events sent, from 0 (none) to 1 (all).')],
                            ['timeout', 'timeout', __('Timeout (seconds)'), __('Per send; kept short so a slow ingest never slows the app.')],
                            ['maxRetries', 'max_retries', __('Max retries'), __('Retries on timeouts, 429 and 5xx.')],
                            ['retryBaseMs', 'retry_base_ms', __('Retry base delay (ms)'), __('Doubles per attempt, with jitter.')],
                            ['retryCapMs', 'retry_cap_ms', __('Retry delay cap (ms)'), __('Upper bound for any single wait.')],
                            ['maxFrames', 'max_frames', __('Max stack frames'), __('Frames sent per event.')],
                            ['maxMessage', 'max_message', __('Max message length'), __('Characters of the exception message sent.')],
                        ] as [$property, $field, $label, $help])
                            <flux:field wire:key="bliptrace-{{ $field }}">
                                <div class="flex items-center justify-between gap-2">
                                    <flux:label>{{ $label }}</flux:label>
                                    @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources[$field]])
                                </div>
                                <flux:input wire:model="{{ $property }}" type="number" :step="$field === 'sample_rate' ? '0.01' : '1'" :min="$field === 'sample_rate' ? '0' : null" :max="$field === 'sample_rate' ? '1' : null" :inputmode="$field === 'sample_rate' ? 'decimal' : 'numeric'" :placeholder="$inherited($field)" />
                                <flux:description>{{ $help }}</flux:description>
                                <flux:error :name="$property" />
                            </flux:field>
                        @endforeach
                    </div>

                    <flux:field>
                        <div class="flex items-center justify-between gap-2">
                            <flux:label>{{ __('Ignored exceptions') }}</flux:label>
                            @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources['ignore_exceptions']])
                        </div>
                        <flux:textarea wire:model="ignoreExceptions" rows="5" class="font-mono text-xs"
                            :placeholder="implode(PHP_EOL, $sources['ignore_exceptions'] === 'ui' ? [] : $effective['ignore_exceptions'])" />
                        <flux:description>{{ __('One fully-qualified class per line. Filling this replaces the default list.') }}</flux:description>
                        <flux:error name="ignoreExceptions" />
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center justify-between gap-2">
                            <flux:label>{{ __('Scrubbed request keys') }}</flux:label>
                            @include('livewire.admin.settings.partials.bliptrace-source', ['source' => $sources['scrub_keys']])
                        </div>
                        <flux:textarea wire:model="scrubKeys" rows="5" class="font-mono text-xs"
                            :placeholder="implode(PHP_EOL, $sources['scrub_keys'] === 'ui' ? [] : $effective['scrub_keys'])" />
                        <flux:description>{{ __('Redacted before an event leaves the app, one per line. Filling this replaces the default list.') }}</flux:description>
                        <flux:error name="scrubKeys" />
                    </flux:field>
                </x-card.body>
                <x-card.footer>
                    <flux:button type="submit" variant="primary" icon="check" class="cursor-pointer">
                        {{ __('Save changes') }}
                    </flux:button>
                </x-card.footer>
            </x-card>
        </form>
    </div>
</div>
