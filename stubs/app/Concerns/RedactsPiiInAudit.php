<?php

namespace App\Concerns;

/**
 * Masks sensitive fields in audit trail old_values/new_values.
 *
 * Usage:
 *   use App\Concerns\RedactsPiiInAudit;
 *
 *   class Customer extends Base
 *   {
 *       use RedactsPiiInAudit;
 *
 *       protected array $auditRedactFields = ['phone', 'national_id'];
 *   }
 */
trait RedactsPiiInAudit
{
    /**
     * Transform audit data to redact sensitive fields.
     *
     * Called automatically by owen-it/laravel-auditing before persisting the audit record.
     */
    public function transformAudit(array $data): array
    {
        $sensitiveFields = $this->auditRedactFields ?? [];

        foreach (['old_values', 'new_values'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                foreach ($sensitiveFields as $field) {
                    if (isset($data[$key][$field]) && is_string($data[$key][$field])) {
                        $value = $data[$key][$field];
                        $length = mb_strlen($value);
                        $data[$key][$field] = $length > 4
                            ? str_repeat('*', $length - 4).mb_substr($value, -4)
                            : str_repeat('*', $length);
                    }
                }
            }
        }

        return $data;
    }
}
