<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;

if (! function_exists('dumpSql')) {
    function dumpSql(Builder $builder): string
    {
        return array_reduce(
            $builder->getBindings(),
            fn ($sql, $binding) => preg_replace('/\?/', is_numeric($binding) ? $binding : "'".$binding."'", $sql, 1),
            $builder->toSql(),
        );
    }
}

if (! function_exists('logDumpSql')) {
    function logDumpSql(Builder $query): void
    {
        logger()->debug(dumpSql($query));
    }
}
