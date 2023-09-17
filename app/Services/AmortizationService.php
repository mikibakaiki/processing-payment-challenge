<?php

namespace App\Services;

use App\Jobs\ProcessAmortizationPaymentJob;
use App\Models\Amortization;
use Bus;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AmortizationService
{
    public function payAllDueAmortizations(): ?string
    {
        try {
            $currentDate = Carbon::now();
            Log::info("--- Entered the Service ---");
            Log::info("Current Date: $currentDate");

            $amortizations = Amortization::where('schedule_date', '<=', $currentDate)
                ->where('state', '!=', 'paid')
                ->get();

            if ($amortizations->isEmpty()) {
                Log::info("No amortizations to process.");
                return null;
            }

            $jobs = $amortizations->map(function ($amortization) use ($currentDate) {
                return new ProcessAmortizationPaymentJob($amortization, $currentDate);
            })->toArray();

            $batch = Bus::batch($jobs)->allowFailures()->dispatch();

            $batchId = $batch->id;

            // Return the batch id so that you can check its status later
            Log::info("Batch {$batchId} dispatched");
            return $batchId;
        } catch (\Exception $e) {
            Log::error('An error occurred while dispatching the batch: ' . $e->getMessage());
            throw $e;
        }
    }


    public function getAllAmortizations(int $perPage = 10, string $sortField = 'id', string $sortOrder = 'asc'): LengthAwarePaginator
    {
        // Get all amortizations ordered by $sortField and $sortOrder, in pages of size $perPage
        return Amortization::orderBy($sortField, $sortOrder)->paginate($perPage);
    }
}