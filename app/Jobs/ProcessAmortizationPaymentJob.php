<?php
namespace App\Jobs;


use App\Exceptions\ProfileNotFoundException;
use App\Exceptions\ProjectNotFoundException;
use App\Models\Amortization;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Project;
use App\Notifications\InsufficientFundsNotification;
use App\Notifications\PaymentDelayedNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAmortizationPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $amortization;
    protected $currentDate;

    public function __construct(Amortization $amortization, Carbon $currentDate)
    {
        $this->amortization = $amortization;
        $this->currentDate = $currentDate;
    }


    public function handle(): void
    {
        try {
            Log::info("Processing job", ['job' => $this]);
            DB::transaction(function () {
                $project = $this->getProjectWithPromoter();
                Log::info("Project: " . $project);
                $this->processAmortization($project);
            });
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getProjectWithPromoter()
    {
        try {
            $projectId = $this->amortization->project_id;
            return Project::with('promoter')->findOrFail($projectId);
        } catch (ModelNotFoundException $e) {
            throw new ProjectNotFoundException('Project #' . $projectId . ' was not found.');
        }
    }

    private function processAmortization($project)
    {
        if ($project->wallet_balance >= $this->amortization->amount) {
            $this->updateWalletBalanceAndAmortizationState($project);
            $this->updateAssociatedPaymentsAndSendNotifications($project);
        } else {
            $this->sendInsufficientFundsNotification($project);
        }
    }

    private function updateWalletBalanceAndAmortizationState($project)
    {
        $project->decrement('wallet_balance', $this->amortization->amount);
        Log::info("Project balance = $project->wallet_balance");
        $this->amortization->update(['state' => 'paid']);
        Log::
            info("Amortization # $this->amortization->id has state: $this->amortization->state");
    }

    private function updateAssociatedPaymentsAndSendNotifications($project)
    {
        Payment::where('amortization_id', $this->amortization->id)
            ->update(['state' => 'paid']);

        if ($this->amortization->schedule_date < $this->currentDate) {
            $this->sendPaymentDelayedNotifications($project);
        }
    }

    private function sendPaymentDelayedNotifications($project)
    {
        $profiles = Profile::whereIn('id', function ($query) {
            $query->select('profile_id')
                ->from('payments')
                ->where('amortization_id', $this->amortization->id);
        })->get();

        foreach ($profiles as $profile) {
            $profile->notify(new PaymentDelayedNotification(false));
            Log::info("Profile $profile was notified: PaymentDelayNotification");
        }

        $project->promoter->notify(new PaymentDelayedNotification(true));
        Log::info("Promoter $project->promoter was notified: PaymentDelayNotification");

    }

    private function sendInsufficientFundsNotification($project)
    {
        $project->promoter->notify(new InsufficientFundsNotification());
    }
}