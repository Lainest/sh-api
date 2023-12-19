<?php

class Response
{
    public function __construct(private array $message, private int $status_code)
    {
        $this->jsonResponse();
    }

    private function jsonResponse(): void
    {
        http_response_code($this->status_code);
        echo json_encode($this->message);
    }
}
