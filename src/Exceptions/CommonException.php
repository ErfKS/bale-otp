<?php

namespace ErfanKatebSaber\BaleOtp\Exceptions;

use Throwable;
use Exception;

class CommonException extends Exception
{
    protected ?int $type = null;
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function Factory(int $type,int $code,string $message): static
    {
        $exception = new static($message, $code);
        $exception->type = $type;

        return $exception;
    }
}
