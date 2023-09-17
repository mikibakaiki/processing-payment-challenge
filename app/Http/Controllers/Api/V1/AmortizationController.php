<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AmortizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
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
        try {
            Log::info("### Entered the Controller ###");
            $batchId = $this->amortizationService->payAllDueAmortizations();

            if ($batchId) {
                return response()->json([
                    'message' => 'Payment process initiated for all due amortizations.',
                    'batchId' => $batchId
                ], 200);
            }

            return response()->json([
                'message' => 'No amortizations to process.',
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing the amortizations.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkBatchStatus(Request $request)
    {
        try {
            $request->validate([
                'batch_id' => 'required|exists:job_batches,id',
            ]);

            $batchId = $request->input('batch_id');

            $batch = Bus::findBatch($batchId);

            return response()->json([
                'id' => $batch->id,
                'name' => $batch->name,
                'totalJobs' => $batch->totalJobs,
                'pendingJobs' => $batch->pendingJobs,
                'processedJobs' => $batch->processedJobs,
                'failedJobs' => $batch->failedJobs,
                'cancelled' => $batch->cancelled(),
                'finished' => $batch->finished(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while checking the batch status.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15); // default 15 items per page
            $sortBy = $request->get('sort_by', 'id'); // default sort by id
            $sortOrder = $request->get('sort_order', 'asc'); // default sort order is ascending

            $amortizations = $this->amortizationService->getAllAmortizations($perPage, $sortBy, $sortOrder);

            return response()->json($amortizations);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the amortizations.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}