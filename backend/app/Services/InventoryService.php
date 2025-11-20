<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update inventory when order is placed (reserve stock only)
     */
    public function updateInventoryOnOrder(Order $order)
    {
        try {
            $order->load('products', 'customBouquets');
            
            // Log transactions for regular products
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                
                // Only log the order - don't decrease stock yet
                $this->logInventoryTransaction($product, $quantity, 'ordered', $order->id);
                
                Log::info("Order placed - stock reserved for order {$order->id}", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_ordered' => $quantity,
                    'current_stock' => $product->stock
                ]);
            }
            
            // Log transactions for custom bouquets components
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
                    $product = Product::where('name', $component['name'])
                        ->where('category', $component['category'])
                        ->first();
                    
                    // If not found by category, try just by name
                    if (!$product) {
                        $product = Product::where('name', $component['name'])->first();
                    }
                    
                    if ($product) {
                        // Create 'ordered' transaction for this component
                        $this->logInventoryTransaction($product, $component['quantity'], 'ordered', $order->id);
                        
                        Log::info("Custom bouquet component ordered - stock reserved for order {$order->id}", [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity_ordered' => $component['quantity'],
                            'current_stock' => $product->stock,
                            'bouquet_id' => $bouquet->id
                        ]);
                    } else {
                        Log::warning("Product not found for custom bouquet component", [
                            'component_name' => $component['name'],
                            'category' => $component['category'],
                            'order_id' => $order->id,
                            'bouquet_id' => $bouquet->id
                        ]);
                    }
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to log order for inventory tracking {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Update inventory when order is delivered (no stock change yet)
     */
    public function updateInventoryOnDelivery(Order $order)
    {
        try {
            $order->load('products');
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                
                // Log delivery - no stock change yet
                $this->logInventoryTransaction($product, $quantity, 'delivered', $order->id);
                
                Log::info("Order delivered - waiting for customer confirmation {$order->id}", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_delivered' => $quantity
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to log delivery for order {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            return false;
        }
    }
    
    /**
     * Update inventory when order is received by customer (ACTUAL STOCK DECREASE)
     */
    public function updateInventoryOnReceived(Order $order)
    {
        try {
            $order->load('products');
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;

                // If this purchased item corresponds to a CatalogProduct with compositions,
                // consume the component materials instead of the finished product stock.
                $catalog = \App\Models\CatalogProduct::where('name', $product->name)
                    ->where('category', $product->category)
                    ->first();

                if ($catalog && $catalog->compositions && $catalog->compositions()->count() > 0) {
                    foreach ($catalog->compositions as $comp) {
                        $component = \App\Models\Product::find($comp->component_id);
                        if (!$component) { continue; }
                        // Idempotency: if we already logged a consumption for this order+product, skip
                        $already = \App\Models\InventoryTransaction::where('order_id', $order->id)
                            ->where('product_id', $component->id)
                            ->where('type', 'consumed')
                            ->exists();
                        if ($already) { continue; }
                        $consumeQty = (int) $comp->quantity * (int) $quantity; // total consumption
                        // Decrease component stock and track consumed
                        $component->decrement('stock', $consumeQty);
                        $component->increment('qty_consumed', $consumeQty);
                        $this->logInventoryTransaction($component, $consumeQty, 'consumed', $order->id);
                    }

                    Log::info("Customer received order {$order->id} - components consumed via composition", [
                        'catalog_product_id' => $catalog->id,
                        'product_name' => $product->name,
                        'order_quantity' => $quantity
                    ]);
                } else {
                    // Fallback: treat as standalone product sale
                    $alreadySold = \App\Models\InventoryTransaction::where('order_id', $order->id)
                        ->where('product_id', $product->id)
                        ->where('type', 'sold')
                        ->exists();
                    if (!$alreadySold) {
                        $product->decrement('stock', $quantity);
                        $product->increment('qty_sold', $quantity);
                        $this->logInventoryTransaction($product, $quantity, 'sold', $order->id);
                    }

                    Log::info("Customer received order {$order->id} - inventory updated (standalone)", [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity_sold' => $quantity,
                        'remaining_stock' => $product->fresh()->stock
                    ]);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update inventory for received order {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            return false;
        }
    }

    /**
     * Check for low stock and generate alerts
     */
    public function checkLowStock()
    {
        $lowStockProducts = Product::whereColumn('stock', '<=', 'reorder_min')
            ->where('reorder_min', '>', 0)
            ->get();
            
        $alerts = [];
        
        foreach ($lowStockProducts as $product) {
            $needed = $product->reorder_max - $product->stock;
            $alerts[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->stock,
                'reorder_min' => $product->reorder_min,
                'reorder_max' => $product->reorder_max,
                'qty_to_purchase' => max(0, $needed),
                'category' => $product->category
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Get products that need restocking
     */
    public function getRestockRecommendations()
    {
        return Product::whereColumn('stock', '<=', 'reorder_min')
            ->where('reorder_min', '>', 0)
            ->select([
                'id', 'name', 'category', 'stock', 'reorder_min', 'reorder_max',
                'price', 'cost_price'
            ])
            ->get()
            ->map(function ($product) {
                $needed = $product->reorder_max - $product->stock;
                $product->qty_to_purchase = max(0, $needed);
                $product->estimated_cost = $product->cost_price ? $product->cost_price * $product->qty_to_purchase : 0;
                return $product;
            });
    }
    
    /**
     * Log inventory transactions
     */
    private function logInventoryTransaction(Product $product, int $quantity, string $type, int $orderId)
    {
        // For 'ordered' type, stock hasn't changed yet, so before and after are the same
        if ($type === 'ordered' || $type === 'delivered') {
            $stockBefore = $product->stock;
            $stockAfter = $product->stock;
        } else {
            // For other types, calculate based on whether stock was decreased or increased
            // If type is 'sold' or 'consumed', stock was decreased
            // If type is 'returned' or 'restocked', stock was increased
            if (in_array($type, ['sold', 'consumed', 'completed'])) {
                $stockBefore = $product->stock + $quantity; // Stock before deduction
                $stockAfter = $product->stock; // Stock after deduction
            } else {
                $stockBefore = $product->stock - $quantity; // Stock before addition
                $stockAfter = $product->stock; // Stock after addition
            }
        }
        
        InventoryTransaction::create([
            'product_id' => $product->id,
            'order_id' => $orderId,
            'quantity' => $quantity,
            'type' => $type,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'created_by' => auth()->id() ?? null
        ]);
    }
    
    /**
     * Deduct materials/components for a product based on its composition
     */
    public static function deductMaterialsForProduct($product, $quantity, $orderId = null)
    {
        try {
            $product->load('compositions');
            
            $compositions = $product->compositions;
            
            // Fallback: if no product-level compositions, try catalog product compositions by name
            if (!$compositions || $compositions->isEmpty()) {
                $catalog = \App\Models\CatalogProduct::where('name', $product->name)
                    ->where('status', true)
                    ->where('is_approved', true)
                    ->with('compositions')
                    ->first();
                if ($catalog && $catalog->compositions && $catalog->compositions()->count() > 0) {
                    $compositions = $catalog->compositions;
                }
            }
            
            if (!$compositions || $compositions->isEmpty()) {
                // No compositions anywhere - this is a finished product (like teddy bear, cake, balloon)
                // Deduct from stock and update qty_sold for finished products
                $product->stock = max(0, $product->stock - $quantity);
                $product->qty_sold = ($product->qty_sold ?? 0) + $quantity;
                $product->save();
                
                // Log the transaction for finished product sales
                InventoryTransaction::create([
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'type' => 'sold',
                    'stock_before' => $product->stock + $quantity,
                    'stock_after' => $product->stock,
                    'created_by' => auth()->id()
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Finished product stock deducted successfully',
                    'components' => [[
                        'product_id' => $product->id,
                        'stock_after' => $product->stock,
                        'unit' => 'pcs',
                    ]]
                ];
            }
            
            $insufficientMaterials = [];
            
            // Deduct each component material (transaction for consistency)
            $componentsResults = [];
            foreach ($compositions as $composition) {
                $component = Product::find($composition->component_id);
                
                if (!$component) {
                    continue;
                }
                
                $requiredQuantity = $composition->quantity * $quantity;
                $availableStock = $component->stock;
                
                if ($availableStock < $requiredQuantity) {
                    $insufficientMaterials[] = [
                        'component' => $component->name,
                        'required' => $requiredQuantity,
                        'available' => $availableStock,
                        'shortage' => $requiredQuantity - $availableStock
                    ];
                } else {
                    // Deduct the material atomically and log in a single DB transaction
                    $componentFresh = DB::transaction(function () use ($component, $availableStock, $requiredQuantity, $orderId, $composition) {
                        $deductQty = min((int)$requiredQuantity, (int)$availableStock);
                        Product::where('id', $component->id)->decrement('stock', $deductQty);
                        Product::where('id', $component->id)->increment('qty_consumed', $deductQty);
                        $fresh = Product::find($component->id);
                        InventoryTransaction::create([
                            'order_id' => $orderId,
                            'product_id' => $component->id,
                            'quantity' => $deductQty,
                            'type' => 'consumed',
                            'stock_before' => $availableStock,
                            'stock_after' => $fresh->stock,
                            'created_by' => auth()->id()
                        ]);
                        return $fresh;
                    });
                    $componentsResults[] = [
                        'product_id' => $component->id,
                        'stock_after' => $componentFresh->stock,
                        'unit' => $composition->unit,
                    ];
                    Log::info("Material deducted for product composition", [
                        'product' => $product->name,
                        'component' => $component->name,
                        'quantity_deducted' => $requiredQuantity,
                        'remaining_stock' => $componentFresh->stock
                    ]);
                }
            }
            
            if (!empty($insufficientMaterials)) {
                return [
                    'success' => false,
                    'message' => 'Insufficient materials to fulfill order',
                    'insufficient_materials' => $insufficientMaterials
                ];
            }
            
            return [
                'success' => true,
                'message' => 'All materials deducted successfully',
                'components' => $componentsResults
            ];
            
        } catch (\Exception $e) {
            Log::error("Failed to deduct materials for product {$product->name}", [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
            
            return [
                'success' => false,
                'message' => 'Error deducting materials: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Restore inventory when order is cancelled
     */
    public function restoreInventoryOnCancellation(Order $order)
    {
        try {
            $order->load('products');
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                
                // Restore stock
                $product->increment('stock', $quantity);
                
                // Decrease qty_sold
                $product->decrement('qty_sold', $quantity);
                
                // Log inventory transaction
                $this->logInventoryTransaction($product, $quantity, 'returned', $order->id);
                
                Log::info("Inventory restored for cancelled order {$order->id}", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_restored' => $quantity,
                    'new_stock' => $product->fresh()->stock
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to restore inventory for cancelled order {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            return false;
        }
    }
}