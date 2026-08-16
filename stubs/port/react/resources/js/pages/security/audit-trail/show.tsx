import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/*'; // split per shadcn convention
import { ArrowLeft } from 'lucide-react';
import { useState } from 'react';

type Props = Record<string, never>;

export default function SecurityAuditTrailShow(props: Props) {


    return (
        <AppLayout>
            <Head title="SecurityAuditTrailShow" />
<div className="mb-6 flex items-center justify-between">
<div>
<h1 className="text-2xl font-semibold tracking-tight">
    <p className="text-sm text-muted-foreground">
        </div>
        <Button variant="ghost" className=""><ArrowLeft className="size-4" />
            Back
        </Button>
        </div>
        {/* TODO: port @include('security.audit-trail._detail', ['audit' => $audit]) */}
        </AppLayout>
    );
}