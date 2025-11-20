<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Services\NumberingService;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Create an invoice for an order
     */
    public function createInvoice(Order $order): Invoice
    {
        // Check if invoice already exists
        if ($order->invoice) {
            return $order->invoice;
        }

        // Calculate totals - include both products and custom bouquets
        $productsSubtotal = $order->products->sum(function($product) {
            return $product->pivot->quantity * $product->price;
        });
        
        $customBouquetsSubtotal = $order->customBouquets->sum(function($bouquet) {
            $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($bouquet->pivot->quantity, 1));
            return $unitPrice * $bouquet->pivot->quantity;
        });
        
        $subtotal = $productsSubtotal + $customBouquetsSubtotal;

        $shippingFee = $order->delivery ? ($order->delivery->shipping_fee ?? 0) : 0;
        
        // If shipping_fee is 0 or null, calculate it from the difference between total_price and subtotal
        if ($shippingFee == 0 && $order->total_price > $subtotal) {
            $shippingFee = $order->total_price - $subtotal;
        }
        
        $totalAmount = $subtotal + $shippingFee;

        // Determine payment type and initial status
        $method = strtolower((string) $order->payment_method);
        $isCod = in_array($method, ['cod', 'cash', 'cash_on_delivery', 'cod_cash', 'walk-in', 'walkin']);
        $paymentType = $isCod ? 'cod' : 'online';
        $status = $paymentType === 'online' ? 'paid' : 'ready';

        // Generate invoice number
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'status' => $status,
            'payment_type' => $paymentType,
            'notes' => $order->notes,
        ]);

        // Create sales order if it doesn't exist
        if (!$order->salesOrder) {
            $this->createSalesOrder($order, $subtotal, $shippingFee, $totalAmount);
        }

        // If it's an online payment, create a payment record
        if ($paymentType === 'online') {
            $this->createOnlinePayment($invoice, $order);
        }

        return $invoice;
    }

    /**
     * Create a payment record for online payments
     */
    public function createOnlinePayment(Invoice $invoice, Order $order): Payment
    {
        $paymentNumber = 'PAY-' . date('Y') . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT);

        // Calculate correct total amount from order data
        $productsSubtotal = $order->products->sum(function($product) {
            return $product->pivot->quantity * $product->price;
        });
        
        $customBouquetsSubtotal = $order->customBouquets->sum(function($bouquet) {
            $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($bouquet->pivot->quantity, 1));
            return $unitPrice * $bouquet->pivot->quantity;
        });
        
        $calculatedSubtotal = $productsSubtotal + $customBouquetsSubtotal;
        $shippingFee = $order->delivery ? ($order->delivery->shipping_fee ?? 0) : 0;
        
        if ($shippingFee == 0 && $order->total_price > $calculatedSubtotal) {
            $shippingFee = $order->total_price - $calculatedSubtotal;
        }
        
        $correctTotalAmount = $calculatedSubtotal + $shippingFee;

        return Payment::create([
            'payment_number' => $paymentNumber,
            'invoice_id' => $invoice->id,
            'order_id' => $order->id,
            'mode_of_payment' => $this->mapPaymentMethod($order->payment_method),
            'amount' => $correctTotalAmount, // Use calculated total instead of stored invoice total
            'payment_date' => now(),
            'memo' => 'Online payment via ' . $order->payment_method,
            'status' => 'completed',
        ]);
    }

    /**
     * Register a manual payment for COD orders
     */
    public function registerPayment(Invoice $invoice, array $paymentData): Payment
    {
        $paymentNumber = 'PAY-' . date('Y') . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) . '-' . time();

        $payment = Payment::create([
            'payment_number' => $paymentNumber,
            'invoice_id' => $invoice->id,
            'order_id' => $invoice->order_id,
            'mode_of_payment' => $paymentData['mode_of_payment'],
            'amount' => $paymentData['amount'],
            'payment_date' => $paymentData['payment_date'],
            'memo' => $paymentData['memo'] ?? null,
            'status' => 'completed',
        ]);

        // Mark invoice as paid
        $invoice->markAsPaid();

        return $payment;
    }

    /**
     * Map order payment method to payment mode
     */
    private function mapPaymentMethod(string $paymentMethod): string
    {
        $mapping = [
            'cod' => 'cash',
            'gcash' => 'gcash',
            'paymaya' => 'gcash', // Map to gcash for e-wallets
            'bpi_debit_card' => 'card',
            'bpi_credit_card' => 'card',
            'bdo_debit_card' => 'card',
            'bdo_credit_card' => 'card',
            'metrobank_debit_card' => 'card',
            'metrobank_credit_card' => 'card',
            'security_bank_debit_card' => 'card',
            'security_bank_credit_card' => 'card',
            'other_debit_card' => 'card',
            'other_credit_card' => 'card',
        ];

        return $mapping[$paymentMethod] ?? 'cash';
    }

    /**
     * Create a sales order for an order
     */
    private function createSalesOrder(Order $order, float $subtotal, float $shippingFee, float $totalAmount): SalesOrder
    {
        $numberingService = new NumberingService();
        $soNumber = $numberingService->generateSalesOrderNumber();

        return SalesOrder::create([
            'so_number' => $soNumber,
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'status' => $order->order_status === 'approved' ? 'confirmed' : 'draft',
            'notes' => $order->type === 'walk-in' ? 'Walk-in order' : 'Online order',
            'confirmed_at' => $order->order_status === 'approved' ? now() : null,
        ]);
    }

    /**
     * Get available payment modes for dropdown
     */
    public function getPaymentModes(): array
    {
        return [
            'cash' => 'Cash',
            'gcash' => 'GCash',
            'bank' => 'Bank Transfer',
            'card' => 'Card Payment',
        ];
    }
}