<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ActionException extends Exception
{
    public static function missingModelProperty(string $class): self
    {
        return new self("Missing model property in class $class");
    }
}
