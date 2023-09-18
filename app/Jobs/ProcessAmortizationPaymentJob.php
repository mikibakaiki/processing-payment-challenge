<?php
namespace App\Jobs;

use App\Exceptions\ProjectNotFoundException;
use App\Models\Amortization;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Project;
use App\Notifications\InsufficientFundsNotification;
use App\Notifications\PaymentDelayedNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAmortizationPaymentJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $amortization;
    protected $currentDate;

    public function __construct(Amortization $amortization, Carbon $currentDate)
    {
        $this->amortization = $amortization;
        $this->currentDate = $currentDate;
    }


    /**
     * Handle the job.
     *
     * This is the main method of the job and will be called when the job is processed by the queue.
     */
    public function handle(): void
    {
        try {
            Log::info("Processing job for amortization #$this->amortization");
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

    /**
     * Get the project associated with the amortization and its promoter.
     *
     * @throws ProjectNotFoundException if the project is not found.
     * @return Project The project instance
     */
    private function getProjectWithPromoter(): Project
    {
        try {
            $projectId = $this->amortization->project_id;
            return Project::with('promoter')->findOrFail($projectId);
        } catch (ModelNotFoundException $e) {
            throw new ProjectNotFoundException('Project #' . $projectId . ' was not found.');
        }
    }

    /**
     * Process the amortization payment.
     *
     * This method checks if the project has sufficient funds to make the payment.
     * If so, it updates the wallet balance, amortization state, associated payments, and sends notifications.
     * Otherwise, it sends an insufficient funds notification.
     *
     * @param Project $project The project instance
     */
    private function processAmortization($project): void
    {
        if ($project->wallet_balance >= $this->amortization->amount) {
            $this->updateWalletBalanceAndAmortizationState($project);
            $this->updateAssociatedPaymentsAndSendNotifications($project);
        } else {
            $this->sendInsufficientFundsNotification($project);
        }
    }

    /**
     * Update the project's wallet balance and the amortization state.
     *
     * This method decrements the project's wallet balance by the amount of the amortization and updates
     * the amortization's state to 'paid'.
     *
     * @param Project $project The project instance
     */
    private function updateWalletBalanceAndAmortizationState($project): void
    {
        $project->decrement('wallet_balance', $this->amortization->amount);
        Log::info("Project balance = $project->wallet_balance");
        $this->amortization->update(['state' => 'paid']);
        Log::
            info("Amortization # $this->amortization->id has state: $this->amortization->state");
    }

    /**
     * Update the state of all payments associated with the amortization and send notifications.
     *
     * This method updates the state of all payments associated with the amortization to 'paid'.
     * If the schedule date of the amortization is earlier than the current date, it sends payment delayed notifications.
     *
     * @param Project $project The project instance
     */
    private function updateAssociatedPaymentsAndSendNotifications($project): void
    {
        Payment::where('amortization_id', $this->amortization->id)
            ->update(['state' => 'paid']);

        if ($this->amortization->schedule_date < $this->currentDate) {
            $this->sendPaymentDelayedNotifications($project);
        }
    }

    /**
     * Send payment delayed notifications to all profiles associated with the amortization and the project's promoter.
     *
     * This method sends a payment delayed notification to all profiles associated with the amortization and the project's promoter.
     *
     * @param Project $project The project instance
     */
    private function sendPaymentDelayedNotifications($project): void
    {
        // The subquery selects the 'profile_id' from the 'payments' table where the amortization_id correspondss to the current amortization id.
        // The main query will select all the profiles where their 'id' in is the list returned by the subquerry.
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

    /**
     * Send insufficient funds notification to the project's promoter.
     *
     * @param Project $project The project instance
     */
    private function sendInsufficientFundsNotification($project): void
    {
        $project->promoter->notify(new InsufficientFundsNotification());
    }
}