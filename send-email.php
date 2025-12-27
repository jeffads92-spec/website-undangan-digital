<?php
// File: send-email.php
require_once 'config.php';

class EmailNotifier {
    public function sendRSVPConfirmation($to_email, $guest_name, $wedding_details) {
        $subject = "Konfirmasi RSVP Berhasil";
        $message = "
        <h2>Terima kasih telah konfirmasi kehadiran!</h2>
        <p>Halo $guest_name,</p>
        <p>RSVP Anda untuk pernikahan telah berhasil dicatat.</p>
        
        <h3>Detail Acara:</h3>
        <p>Nama Pengantin: {$wedding_details['couple_names']}</p>
        <p>Tanggal: {$wedding_details['date']}</p>
        <p>Lokasi: {$wedding_details['location']}</p>
        
        <p>Sampai jumpa di acara kami!</p>
        ";
        
        return $this->sendEmail($to_email, $subject, $message);
    }
    
    private function sendEmail($to, $subject, $body) {
        // Implementasi PHPMailer atau mail() function
    }
}
?>
