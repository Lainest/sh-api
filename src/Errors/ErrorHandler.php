<?php

class ErrorHandler
{
    public static function handleException(Throwable $exception): void
    {
        new Response(
            [
                "code" => $exception->getCode(),
                "message" => $exception->getMessage(),
                "file" => $exception->getFile(),
                "line" => $exception->getLine()
            ],
            500
        );
    }
}
