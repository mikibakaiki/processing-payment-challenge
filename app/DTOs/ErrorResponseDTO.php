<?php

namespace App\DTOs;

class ErrorResponseDTO
{
    public $error;
    public $message;

    public function __construct($message = 'An unexpected error occured.')
    {
        $this->error = 'An unexpected error occurred.';
        $this->message = $message;
    }
}