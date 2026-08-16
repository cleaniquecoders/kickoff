import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { RadioGroup, RadioGroupItem } from '@/components/ui/*'; // split per shadcn convention
import { ComputerDesktop, Moon, Sun } from 'lucide-react';
import { useState } from 'react';

type Props = Record<string, never>;

export default function SettingsAppearance(props: Props) {


    return (
        <AppLayout>
            <Head title="SettingsAppearance" />
{/* TODO: port <?php */}
use Livewire\Component;
new class extends Component {
//
}; ?>
<section className="w-full">
{/* TODO: port @include('partials.settings-heading') */}
{/* TODO: port <x-settings.layout> */}
<RadioGroup>
    <RadioGroupItem value="light" />
    <RadioGroupItem value="dark" />
    <RadioGroupItem value="system" />
</RadioGroup>
</section>
        </AppLayout>
    );
}