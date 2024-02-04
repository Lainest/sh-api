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
        echo json_encode($this->htmlspecialchars_deep($this->message));
        die;
    }

    private function htmlspecialchars_deep($value)
    {
        if (is_array($value)) {
            return array_map($this->htmlspecialchars_deep(...), $value);
        } else {
            return htmlspecialchars($value, ENT_NOQUOTES, 'utf-8');
        }
    }
}
