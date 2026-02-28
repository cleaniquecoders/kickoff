<?php

declare(strict_types=1);

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', config('audit.implementation'));

        $sub = 'Audit trail log';

        return view('security.audit-trail.index', compact('sub'));
    }

    public function show(Request $request, string $uuid): \Illuminate\View\View
    {
        $audit = config('audit.implementation')::whereUuid($uuid)->firstOrFail();

        $this->authorize('view', $audit);

        $sub = 'Audit trail details';

        return view('security.audit-trail.show', compact('audit', 'sub'));
    }
}
