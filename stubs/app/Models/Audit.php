<?php

declare(strict_types=1);

namespace App\Models;

use CleaniqueCoders\Traitify\Concerns\InteractsWithUuid;

class Audit extends \OwenIt\Auditing\Models\Audit
{
    use InteractsWithUuid;

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
