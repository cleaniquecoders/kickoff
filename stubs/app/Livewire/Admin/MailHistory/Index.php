<?php

namespace App\Livewire\Admin\MailHistory;

use CleaniqueCoders\MailHistory\Models\MailHistory;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin view over the outbound mail audit log recorded by
 * cleaniquecoders/mailhistory (issue #196). Read-only: it lists sent mail
 * and shows the per-message delivery event timeline.
 */
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public int $perPage = 10;

    public ?string $viewingUuid = null;

    public bool $showDetail = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function view(string $uuid): void
    {
        $this->viewingUuid = $uuid;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingUuid = null;
    }

    /**
     * Pull a single header value (To, Subject, From, …) out of the stored
     * Symfony header array, where each entry is a `"Name: value"` string.
     */
    public static function header(Model $mail, string $name): ?string
    {
        $headers = is_array($mail->headers) ? $mail->headers : [];
        $prefix = strtolower($name).':';

        foreach ($headers as $line) {
            if (! is_string($line)) {
                continue;
            }

            if (str_starts_with(strtolower($line), $prefix)) {
                return trim(substr($line, strlen($prefix)));
            }
        }

        return null;
    }

    /**
     * Best-effort list of attachment filenames parsed from the stored raw MIME
     * body (the package keeps the full message body but no separate attachment
     * records). Returns a de-duplicated list of filenames.
     *
     * @return array<int, string>
     */
    public static function attachments(Model $mail): array
    {
        $body = (string) ($mail->body ?? '');

        if ($body === '' || stripos($body, 'attachment') === false) {
            return [];
        }

        // Match `Content-Disposition: attachment; filename="..."` (filename may
        // be quoted or bare, and can appear on the next line after a fold).
        preg_match_all(
            '/Content-Disposition:\s*attachment;[^\r\n]*?filename\*?=(?:"([^"]+)"|([^;\r\n]+))/i',
            $body,
            $matches,
            PREG_SET_ORDER
        );

        $names = [];

        foreach ($matches as $m) {
            $name = trim($m[1] !== '' ? $m[1] : ($m[2] ?? ''));

            if ($name !== '') {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    public function render()
    {
        $model = config('mailhistory.model', MailHistory::class);

        $query = $model::query()
            ->when($this->search, function ($query) {
                // Recipient + subject live inside the JSON headers payload.
                $query->where('headers', 'like', '%'.$this->search.'%');
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->latest();

        $mails = $query->paginate($this->perPage);

        $viewing = $this->viewingUuid
            ? $model::with('events')->where('uuid', $this->viewingUuid)->first()
            : null;

        $statuses = ['Sending', 'Sent', 'Delivered', 'Opened', 'Clicked', 'Bounced', 'Complained', 'Failed'];

        return view('livewire.admin.mail-history.index', [
            'mails' => $mails,
            'viewing' => $viewing,
            'statuses' => $statuses,
        ]);
    }
}
