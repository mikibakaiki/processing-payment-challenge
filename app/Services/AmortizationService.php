<?php

namespace App\Services;

use App\DTOs\AmortizationRequestDTO;
use App\Jobs\ProcessAmortizationPaymentJob;
use App\Models\Amortization;
use Bus;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class AmortizationService
{
    /**
     * Pay all due amortizations and returns the batch ID.
     * 
     * @return ?string The batch ID or null if there are no amortizations to process.
     * @throws \Exception
     */
    public function payAllDueAmortizations(): ?string
    {
        try {
            $currentDate = Carbon::now();
            Log::info("Current Date: $currentDate");

            // Get all amortizations eligible: due date is <= than current data && state != paid
            $amortizations = Amortization::where('schedule_date', '<=', $currentDate)
                ->where('state', '!=', 'paid')
                ->get();

            if ($amortizations->isEmpty()) {
                Log::info("No amortizations to process.");
                return null;
            }

            // Iterate through the $amortizations collenction and create a job with each amortization and the currentDate
            // At the end, we transform the $amortizations collection into an array
            // At the end, we will have an array of ProcessAmortizationPaymentJob, each containing a different amortization
            $jobs = $amortizations->map(function ($amortization) use ($currentDate) {
                return new ProcessAmortizationPaymentJob($amortization, $currentDate);
            })->toArray();

            // Create a batch of jobs, that allows a job to fail and continues processing the remaining jobs.
            $batch = Bus::batch($jobs)->allowFailures()->dispatch();

            $batchId = $batch->id;

            // Return the batch id so that we can check its status later
            Log::info("Batch {$batchId} dispatched");
            return $batchId;
        } catch (\Exception $e) {
            Log::error('An error occurred while dispatching the batch: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Retrieves all amortizations based on the specified request and returns a paginated list.
     * 
     * @param AmortizationRequestDTO $amortizationRequest
     * @return LengthAwarePaginator
     */
    public function getAllAmortizations(AmortizationRequestDTO $amortizationRequest): LengthAwarePaginator
    {
        $perPage = $amortizationRequest->perPage;
        $sortField = $amortizationRequest->sortField;
        $sortOrder = $amortizationRequest->sortOrder;

        // Get all amortizations ordered by $sortField and $sortOrder, in pages of size $perPage
        return Amortization::orderBy($sortField, $sortOrder)->paginate($perPage);
    }
}