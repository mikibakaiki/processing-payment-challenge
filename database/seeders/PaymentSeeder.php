<?php

namespace Database\Seeders;

use App\Models\Amortization;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Promoter;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the paid amortizations
        $amortizations = Amortization::where('state', 'paid')->get();

        // Convert them into a payment - already happened, won't be processed by us - and assign it a Profile and Promoter, randomly.
        foreach ($amortizations as $amortization) {
            $profile = Profile::inRandomOrder()->first();
            $promoter = Promoter::inRandomOrder()->first();

            Payment::create([
                'amortization_id' => $amortization->id,
                'amount' => $amortization->amount,
                'state' => $amortization->state,
                'profile_id' => $profile->id,
                'promoter_id' => $promoter->id
            ]);
        }
    }
}