<?php

namespace App\DTOs;

class AmortizationsResponseDTO
{
    public $batch_id;
    public $message;

    public function __construct($data = null, $message = '')
    {
        $this->batch_id = $data;
        $this->message = $message;
    }
}