<?php

namespace App\Services;

use App\Jobs\ProcessAmortizationPaymentJob;
use App\Models\Amortization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AmortizationService
{
    public function payAllDueAmortizations()
    {
        $currentDate = Carbon::now();
        Log::info("--- Entered the Service ---");
        Log::info("Current Date: $currentDate");

        $anyAmortizationsProcessed = false;

        Amortization::where('schedule_date', '<=', $currentDate)
            ->where('state', '!=', 'paid')
            ->chunk(100, function ($amortizations) use ($currentDate, &$anyAmortizationsProcessed) {
                $anyAmortizationsProcessed = true;
                foreach ($amortizations as $amortization) {
                    Log::info("Processing amortization = $amortization");
                    ProcessAmortizationPaymentJob::dispatch($amortization, $currentDate);
                }
            });
        if (!$anyAmortizationsProcessed) {
            Log::info("No amortizations to process.");
        }
    }
}