<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\SalesOrder;
use App\Services\NumberingService;

class BackfillSalesOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-orders:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sales orders for existing orders that do not have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sales orders backfill...');

        // Get all orders that don't have a sales order
        $orders = Order::whereDoesntHave('salesOrder')->get();
        
        if ($orders->isEmpty()) {
            $this->info('No orders found without sales orders. All orders already have sales orders.');
            return 0;
        }

        $this->info("Found {$orders->count()} orders without sales orders.");

        $numberingService = new NumberingService();
        $created = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        foreach ($orders as $order) {
            try {
                // Calculate totals
                $productsSubtotal = $order->products->sum(function($product) {
                    return $product->pivot->quantity * $product->price;
                });
                
                $customBouquetsSubtotal = $order->customBouquets->sum(function($bouquet) {
                    $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($bouquet->pivot->quantity, 1));
                    return $unitPrice * $bouquet->pivot->quantity;
                });
                
                $subtotal = $productsSubtotal + $customBouquetsSubtotal;
                $shippingFee = $order->delivery ? ($order->delivery->shipping_fee ?? 0) : 0;
                
                // If shipping_fee is 0 or null, calculate it from the difference
                if ($shippingFee == 0 && $order->total_price > $subtotal) {
                    $shippingFee = $order->total_price - $subtotal;
                }
                
                $totalAmount = $subtotal + $shippingFee;

                // Generate SO number
                $soNumber = $numberingService->generateSalesOrderNumber();

                // Create sales order
                SalesOrder::create([
                    'so_number' => $soNumber,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $totalAmount,
                    'status' => $order->order_status === 'approved' || $order->order_status === 'completed' ? 'confirmed' : 'draft',
                    'notes' => $order->type === 'walk-in' ? 'Walk-in order' : 'Online order',
                    'confirmed_at' => ($order->order_status === 'approved' || $order->order_status === 'completed') ? $order->approved_at ?? now() : null,
                ]);

                $created++;
            } catch (\Exception $e) {
                $this->error("Failed to create sales order for order {$order->id}: {$e->getMessage()}");
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Sales orders backfill completed!");
        $this->info("Created: {$created}");
        $this->info("Skipped: {$skipped}");

        return 0;
    }
}
