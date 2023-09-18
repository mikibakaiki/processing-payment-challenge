<?php

namespace App\Services;

use App\DTOs\AmortizationRequestDTO;
use App\Jobs\ProcessAmortizationPaymentJob;
use App\Mail\BatchFailedMail;
use App\Mail\BatchFinishedMail;
use App\Models\Amortization;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

            // Define chunk size to batch
            $chunkSize = 100;

            // Create a batch of jobs, that allows a job to fail and continues processing the remaining jobs.
            $batch = Bus::batch([])
                ->then(function (Batch $batch) {
                    // This will be executed when all jobs in the batch have completed successfully
                    Mail::to('admin@example.com')->send(new BatchFinishedMail($batch));
                })
                ->catch(function (Batch $batch) {
                    // This will be executed only when the whole batch fails
                    // This is because we are using allowFailures()
                    Mail::to('admin@example.com')->send(new BatchFailedMail($batch));
                })
                ->allowFailures()
                ->dispatch();

            $jobs = [];
            $jobCounter = 0;

            // Get all amortizations eligible: due date is <= than current data && state != paid
            // Using cursor uses less memory
            $amortizations = Amortization::where('schedule_date', '<=', $currentDate)
                ->where('state', '!=', 'paid')
                ->cursor();

            foreach ($amortizations as $amortization) {
                // Add a new Job to the jobs array
                $jobs[] = new ProcessAmortizationPaymentJob($amortization, $currentDate);
                $jobCounter++;

                // Add jobs to batch in chunks
                if ($jobCounter % $chunkSize === 0) {
                    $batch->add($jobs);
                    $jobs = [];
                }
            }

            // Add remaining jobs to batch
            if (!empty($jobs)) {
                $batch->add($jobs);
            }

            if ($jobCounter === 0) {
                Log::info("No amortizations to process.");
                return null;
            }

            $batchId = $batch->id;
            Log::info("Batch {$batchId} dispatched");

            // Return the batch id so that we can check its status later
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