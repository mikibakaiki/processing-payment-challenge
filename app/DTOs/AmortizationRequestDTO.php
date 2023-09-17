<?php

namespace App\DTOs;

class AmortizationRequestDTO
{
    public $perPage;
    public $sortField;
    public $sortOrder;

    public function __construct(array $parameters)
    {
        $this->perPage = $parameters['per_page'] ?? 10;
        $this->sortField = $parameters['sort_by'] ?? 'id';
        $this->sortOrder = $parameters['order'] ?? 'asc';
    }
}