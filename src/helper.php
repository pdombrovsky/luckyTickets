<?php

function validation(string $name, $value): string
{
    if ($value === null) {
        return "Parameter '$name' is expected. " . PHP_EOL;
    }

    if ($value === false) {
        return "'$name' must be non negative value with a maximum length of " . SIZE . " digits. " . PHP_EOL;
    }
    return '';
}

function getValue(string $name)
{
    return filter_input(
        INPUT_GET,
        $name,
        FILTER_VALIDATE_INT,
        [
            'options' =>
                [
                    'min_range' => 0,
                    'max_range' => UPPERLIMIT
                ]
        ]
    );
}

function send(int $code, string $message = '')
{
    header('Content-Type: text/plain', true);
    http_response_code($code);
    exit($message);
}