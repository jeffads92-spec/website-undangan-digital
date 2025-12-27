<?php
// includes/EmailNotifier.php
// Pastikan PHPMailer sudah terinstall via Composer atau include manual jika ada
// require 'vendor/autoload.php'; 

class EmailNotifier {
    
    private $fromEmail = "noreply@weddinginvite.com";
    private $fromName = "Wedding Invitation System";

    public function sendRSVPConfirmation($to_email, $guest_name, $wedding_details) {
        $subject = "Konfirmasi Kehadiran - Pernikahan " . $wedding_details['couple'];
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
                .header { background: #8B4513; color: white; padding: 15px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; }
                .footer { text-align: center; font-size: 12px; color: #888; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Terima Kasih!</h2>
                </div>
                <div class='content'>
                    <p>Halo <strong>$guest_name</strong>,</p>
                    <p>Kami telah menerima konfirmasi kehadiran Anda.</p>
                    <hr>
                    <p><strong>Detail Acara:</strong></p>
                    <p>Mempelai: {$wedding_details['couple']}</p>
                    <p>Tanggal: {$wedding_details['date']}</p>
                    <p>Lokasi: {$wedding_details['location']}</p>
                    <hr>
                    <p>Simpan QR Code Anda (jika ada) untuk akses masuk.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Wedding Invite System</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($to_email, $subject, $message);
    }

    // Fungsi wrapper mail() PHP standar (Untuk production gunakan SMTP Library)
    private function sendEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>" . "\r\n";

        // Menggunakan mail() bawaan PHP
        // Pastikan setting SMTP di php.ini sudah benar atau gunakan PHPMailer
        return mail($to, $subject, $message, $headers);
    }
}
?>
