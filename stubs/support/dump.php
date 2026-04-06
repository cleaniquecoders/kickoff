<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;

if (! function_exists('dumpSql')) {
    function dumpSql(Builder $builder): string
    {
        $sql = $builder->toSql();
        $bindings = $builder->getBindings();

        foreach ($bindings as $binding) {
            $value = match (true) {
                is_null($binding) => 'NULL',
                is_bool($binding) => $binding ? 'TRUE' : 'FALSE',
                is_numeric($binding) => (string) $binding,
                default => "'".addslashes((string) $binding)."'",
            };
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }
}

if (! function_exists('logDumpSql')) {
    function logDumpSql(Builder $query): void
    {
        logger()->debug(dumpSql($query));
    }
}
