<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AmortizationService;
use Illuminate\Support\Facades\Log;

class AmortizationController extends Controller
{
    protected $amortizationService;

    public function __construct(AmortizationService $amortizationService)
    {
        $this->amortizationService = $amortizationService;
    }

    public function payAllDueAmortizations()
    {
        Log::info("### Entered the Controller ###");
        $this->amortizationService->payAllDueAmortizations();

        return response()->json(['message' => 'Payment process initiated for all due amortizations.']);
    }
}