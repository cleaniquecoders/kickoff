<?php

namespace App\Concerns;

/**
 * Provides field-level encryption for PII (Personally Identifiable Information).
 *
 * Usage:
 *   use App\Concerns\EncryptsPii;
 *
 *   class Customer extends Base
 *   {
 *       use EncryptsPii;
 *
 *       protected function piiFields(): array
 *       {
 *           return ['phone', 'address', 'national_id'];
 *       }
 *   }
 *
 * Note: Do NOT encrypt fields used in WHERE clauses or unique constraints
 * (e.g., email used for login) as encrypted values are non-deterministic.
 */
trait EncryptsPii
{
    public function initializeEncryptsPii(): void
    {
        foreach ($this->piiFields() as $field) {
            $this->mergeCasts([$field => 'encrypted']);
        }
    }

    /**
     * Return an array of column names containing PII that should be encrypted at rest.
     *
     * @return array<int, string>
     */
    abstract protected function piiFields(): array;
}
