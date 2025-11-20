<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\DeliveryReceipt;
use App\Models\Delivery;

class BackfillDeliveryReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery-receipts:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create delivery receipts for existing completed orders that have proof of delivery images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting delivery receipts backfill...');

        // Get all completed orders that have proof of delivery but no delivery receipt
        $orders = Order::where('order_status', 'completed')
            ->whereHas('delivery', function($query) {
                $query->whereNotNull('proof_of_delivery_image')
                      ->where('proof_of_delivery_image', '!=', '');
            })
            ->whereDoesntHave('deliveryReceipts')
            ->with('delivery')
            ->get();
        
        if ($orders->isEmpty()) {
            $this->info('No completed orders found with proof of delivery but without delivery receipts.');
            return 0;
        }

        $this->info("Found {$orders->count()} completed orders with proof of delivery but without delivery receipts.");

        $created = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        foreach ($orders as $order) {
            try {
                if (!$order->delivery || empty($order->delivery->proof_of_delivery_image)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Check if delivery receipt already exists
                $existingReceipt = DeliveryReceipt::where('order_id', $order->id)->first();
                if ($existingReceipt) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Create delivery receipt from delivery record
                DeliveryReceipt::create([
                    'order_id' => $order->id,
                    'image_path' => $order->delivery->proof_of_delivery_image,
                    'receiver_name' => $order->delivery->recipient_name ?? null,
                    'notes' => $order->delivery->delivery_notes ?? null,
                    'captured_by' => $order->delivery->driver_id ?? null,
                    'received_at' => $order->delivery->proof_of_delivery_taken_at ?? $order->delivery->updated_at ?? now(),
                ]);

                $created++;
            } catch (\Exception $e) {
                $this->error("Failed to create delivery receipt for order {$order->id}: {$e->getMessage()}");
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Delivery receipts backfill completed!");
        $this->info("Created: {$created}");
        $this->info("Skipped: {$skipped}");

        return 0;
    }
}
