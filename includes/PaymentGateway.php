<?php
// includes/PaymentGateway.php

class PaymentGateway {
    
    // Simulasi pembuatan transaksi
    public function createTransaction($orderData) {
        // Di Real World: Panggil API Midtrans/Xendit disini
        // Return payment token & redirect URL
        
        $transaction_id = 'TRX-' . time() . rand(100,999);
        
        return [
            'success' => true,
            'transaction_id' => $transaction_id,
            'amount' => $this->getPriceByPackage($orderData['package']),
            'status' => 'pending',
            'payment_url' => 'payment-dummy.php?trx=' . $transaction_id // URL dummy
        ];
    }

    private function getPriceByPackage($package) {
        switch($package) {
            case 'premium': return 599000;
            case 'platinum': return 1299000;
            default: return 299000;
        }
    }
    
    // Verifikasi pembayaran (Simulasi auto-success)
    public function verifyPayment($transaction_id) {
        return [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s')
        ];
    }
}
?>
