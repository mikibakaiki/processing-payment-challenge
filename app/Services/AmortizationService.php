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
        Amortization::where('schedule_date', '<=', $currentDate)
            ->where('state', '!=', 'paid')
            ->chunk(100, function ($amortizations) use ($currentDate) {
                foreach ($amortizations as $amortization) {
                    ProcessAmortizationPaymentJob::dispatch($amortization, $currentDate);
                }
            });
    }
}