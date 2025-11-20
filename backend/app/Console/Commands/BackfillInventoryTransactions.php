<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\InventoryTransaction;

class BackfillInventoryTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory-transactions:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create inventory transactions for existing orders that do not have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting inventory transactions backfill...');

        // Get all orders (including those with only custom bouquets)
        $allOrders = Order::with('products', 'customBouquets')->get();
        
        // Filter orders that need inventory transactions
        $orders = $allOrders->filter(function($order) {
            // Process orders with products OR custom bouquets
            $hasProducts = !$order->products->isEmpty();
            $hasCustomBouquets = !$order->customBouquets->isEmpty();
            
            if (!$hasProducts && !$hasCustomBouquets) {
                return false; // Skip orders with no products and no custom bouquets
            }
            
            // Check if 'ordered' transactions already exist
            $orderedCount = InventoryTransaction::where('order_id', $order->id)
                ->where('type', 'ordered')
                ->count();
            
            // If no 'ordered' transactions exist, this order needs backfilling
            return $orderedCount === 0;
        });
        
        $allOrdersToProcess = $orders;
        
        if ($allOrdersToProcess->isEmpty()) {
            $this->info('No orders found that need inventory transactions.');
            $this->info('Note: Orders with only custom bouquets create transactions when materials are deducted during approval.');
            return 0;
        }
        
        $orders = $allOrdersToProcess;

        $this->info("Found {$orders->count()} orders without inventory transactions.");

        $created = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        foreach ($orders as $order) {
            try {
                // Create 'ordered' transactions for regular products
                foreach ($order->products as $product) {
                    $quantity = $product->pivot->quantity;
                    
                    // Check if transaction already exists
                    $existing = InventoryTransaction::where('order_id', $order->id)
                        ->where('product_id', $product->id)
                        ->where('type', 'ordered')
                        ->first();
                    
                    if ($existing) {
                        continue;
                    }
                    
                    // Create transaction
                    InventoryTransaction::create([
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'quantity' => $quantity,
                        'type' => 'ordered',
                        'stock_before' => $product->stock, // For 'ordered', stock hasn't changed
                        'stock_after' => $product->stock,
                        'created_by' => $order->user_id ?? null, // Use order's user_id as fallback
                    ]);
                    
                    $created++;
                }
                
                // Create 'ordered' transactions for custom bouquets components
                foreach ($order->customBouquets as $bouquet) {
                    $bouquetQuantity = $bouquet->pivot->quantity ?? 1;
                    $customData = $bouquet->customization_data ?? [];
                    $freshFlowerQty = $customData['freshFlowerQuantity'] ?? $customData['fresh_flower_qty'] ?? 1;
                    $artificialFlowerQty = $customData['artificialFlowerQuantity'] ?? $customData['artificial_flower_qty'] ?? 1;
                    
                    // Get all component names from the bouquet
                    $components = [];
                    
                    // Wrapper
                    if ($bouquet->wrapper) {
                        $components[] = ['name' => $bouquet->wrapper, 'quantity' => $bouquetQuantity, 'category' => 'Wrapper'];
                    }
                    
                    // Focal flowers (fresh flowers)
                    foreach (['focal_flower_1', 'focal_flower_2', 'focal_flower_3'] as $flowerField) {
                        if ($bouquet->$flowerField) {
                            $components[] = [
                                'name' => $bouquet->$flowerField, 
                                'quantity' => $freshFlowerQty * $bouquetQuantity, 
                                'category' => 'Focal'
                            ];
                        }
                    }
                    
                    // Greenery
                    if ($bouquet->greenery) {
                        $components[] = ['name' => $bouquet->greenery, 'quantity' => $bouquetQuantity, 'category' => 'Greeneries'];
                    }
                    
                    // Filler (artificial flowers)
                    if ($bouquet->filler) {
                        $components[] = [
                            'name' => $bouquet->filler, 
                            'quantity' => $artificialFlowerQty * $bouquetQuantity, 
                            'category' => 'Fillers'
                        ];
                    }
                    
                    // Ribbon
                    if ($bouquet->ribbon) {
                        $components[] = ['name' => $bouquet->ribbon, 'quantity' => $bouquetQuantity, 'category' => 'Ribbons'];
                    }
                    
                    // Find products by name and create 'ordered' transactions
                    foreach ($components as $component) {
                        // Try to find product by name and category
                        $product = \App\Models\Product::where('name', $component['name'])
                            ->where('category', $component['category'])
                            ->first();
                        
                        // If not found by category, try just by name
                        if (!$product) {
                            $product = \App\Models\Product::where('name', $component['name'])->first();
                        }
                        
                        if ($product) {
                            // Check if transaction already exists
                            $existing = InventoryTransaction::where('order_id', $order->id)
                                ->where('product_id', $product->id)
                                ->where('type', 'ordered')
                                ->first();
                            
                            if ($existing) {
                                continue;
                            }
                            
                            // Create 'ordered' transaction for this component
                            InventoryTransaction::create([
                                'product_id' => $product->id,
                                'order_id' => $order->id,
                                'quantity' => $component['quantity'],
                                'type' => 'ordered',
                                'stock_before' => $product->stock, // For 'ordered', stock hasn't changed
                                'stock_after' => $product->stock,
                                'created_by' => $order->user_id ?? null,
                            ]);
                            
                            $created++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("Failed to create transactions for order {$order->id}: {$e->getMessage()}");
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Inventory transactions backfill completed!");
        $this->info("Created: {$created} transactions");
        $this->info("Skipped: {$skipped} orders");

        return 0;
    }
}
