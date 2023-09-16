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
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessAmortization implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $amortization;


    public function __construct(Amortization $amortization)
    {
        $this->amortization = $amortization;
    }


    public function handle(): void
    {
        try {
            DB::transaction(function () {
                $project = $this->getProjectWithPromoter();
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
        $this->amortization->update(['state' => 'paid']);
    }

    private function updateAssociatedPaymentsAndSendNotifications($project)
    {
        Payment::where('amortization_id', $this->amortization->id)
            ->update(['state' => 'paid']);

        if ($this->amortization->schedule_date < now()) {
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

        if ($profiles->isEmpty()) {
            // No profiles were found. Throwing this exception.
            throw new ProfileNotFoundException('No profile was found.');
        }

        foreach ($profiles as $profile) {
            $profile->notify(new PaymentDelayedNotification());
        }

        $project->promoter->notify(new PaymentDelayedNotification());
    }

    private function sendInsufficientFundsNotification($project)
    {
        $project->promoter->notify(new InsufficientFundsNotification());
    }
}