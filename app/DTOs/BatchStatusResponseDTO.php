<?php

namespace App\DTOs;

class BatchStatusResponseDTO
{
    public $id;
    public $name;
    public $totalJobs;
    public $pendingJobs;
    public $processedJobs;
    public $failedJobs;
    public $cancelled;
    public $finished;

    public function __construct($batch)
    {
        $this->id = $batch->id;
        $this->name = $batch->name;
        $this->totalJobs = $batch->totalJobs;
        $this->pendingJobs = $batch->pendingJobs;
        $this->processedJobs = $batch->processedJobs;
        $this->failedJobs = $batch->failedJobs;
        $this->cancelled = $batch->cancelled();
        $this->finished = $batch->finished();
    }
}