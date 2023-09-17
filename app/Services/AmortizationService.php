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

            // Store the batch id or send it to the front end
            // so that you can check its status later

            Log::info("Batch {$batchId} dispatched");
            return $batchId;
        } catch (\Exception $e) {
            Log::error('An error occurred while dispatching the batch: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAllAmortizations($perPage, $sortBy, $sortOrder)
    {
        $amortizations = DB::table('amortizations')
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return $amortizations;
    }

    // To use pagination for the collection
    public function paginate(Collection $collection, $perPage, $pageName = 'page', $fragment = null)
    {
        $currentPage = Paginator::resolveCurrentPage($pageName);

        return new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
                'fragment' => $fragment,
            ]
        );
    }
}