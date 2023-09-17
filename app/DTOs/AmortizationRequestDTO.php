<?php

namespace App\DTOs;

class AmortizationRequestDTO
{
    public $perPage;
    public $sortField;
    public $sortOrder;

    public function __construct(array $parameters)
    {
        $this->perPage = $parameters['per_page'];
        $this->sortField = $parameters['sort_by'];
        $this->sortOrder = $parameters['order'];
    }
}