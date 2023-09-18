<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\AmortizationRequestDTO;
use App\DTOs\AmortizationsResponseDTO;
use App\DTOs\BatchStatusResponseDTO;
use App\DTOs\ErrorResponseDTO;
use App\Http\Controllers\Controller;
use App\Services\AmortizationService;
use Illuminate\Http\JsonResponse;
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

    /**
     * Handles the request to pay all due amortizations.
     * 
     * @return JsonResponse
     */
    public function payAllDueAmortizations(): JsonResponse
    {
        try {
            Log::info("### Entered /pay-all-due-amortizations ###");
            $batchId = $this->amortizationService->payAllDueAmortizations();

            if (is_null($batchId)) {
                return response()->json(new AmortizationsResponseDTO($batchId, 'No due amortizations to process.'));
            }
            return response()->json(new AmortizationsResponseDTO($batchId, 'Payment process initiated for all due amortizations.'));
        } catch (\Exception $e) {
            return response()->json([
                new ErrorResponseDTO('An error occurred while processing the amortizations: ' . $e->getMessage())
            ], 500);
        }
    }

    /**
     * Handles the request to check the status of a batch.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkBatchStatus(Request $request): JsonResponse
    {
        try {
            Log::info("### Entered /check-batch-status ###");

            // check if the batch_id exists, since it's required.
            $request->validate([
                'batch_id' => 'required|exists:job_batches,id',
            ]);

            $batchId = $request->input('batch_id');
            $batch = Bus::findBatch($batchId);

            if (!$batch) {
                return response()->json(new ErrorResponseDTO('Batch not found.'), 404);
            }
            return response()->json(new BatchStatusResponseDTO($batch));
        } catch (\Exception $e) {
            return response()->json([
                new ErrorResponseDTO("An error occurred while checking the batch status: " . $e->getMessage()),
            ], 500);
        }
    }

    /**
     * Handles the request to fetch all amortizations.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            Log::info("### Entered /index ###");
            $amortizationRequestDTO = new AmortizationRequestDTO($request->all());
            $amortizations = $this->amortizationService->getAllAmortizations($amortizationRequestDTO);
            return response()->json($amortizations);
        } catch (\Exception $e) {
            return response()->json([
                new ErrorResponseDTO('An error occurred while fetching the amortizations: ' . $e->getMessage())
            ], 500);
        }
    }
}