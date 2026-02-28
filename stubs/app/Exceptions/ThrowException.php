<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Concerns\InteractsWithExceptions;
use Exception;

class ThrowException extends Exception
{
    use InteractsWithExceptions;
}
