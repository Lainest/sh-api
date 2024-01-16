<?php
trait FilterTrait
{
    protected function text($value): string | bool
    {
        return filter_var($value);
    }

    protected function number($value): int | bool
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    protected function email($value): string | bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    protected function list($value)
    {
        return filter_var_array($value);
    }

    protected function datetime($value)
    {
        $format = "Y-m-d H:i:s";
        $date_time = DateTime::createFromFormat($format, $value);
        return $date_time && $date_time->format($format) === $value;
    }
}
