<?php
// index.php - Website utama undangan
require_once 'config.php';

// Get website data from database
$folder = basename(dirname(__FILE__));
$stmt = $pdo->prepare("SELECT * FROM customers WHERE website_folder = ?");
$stmt->execute([$folder]);
$website = $stmt->fetch();

if (!$website) {
    die("Website tidak ditemukan!");
}

// Get RSVP statistics
$rsvp_stats = $pdo->prepare("
    SELECT COUNT(*) as total,
    SUM(CASE WHEN attendance = 'yes' THEN 1 ELSE 0 END) as attending
    FROM rsvp WHERE website_id = ?
")->execute([$website['id']])->fetch();

// Get recent guestbook messages
$guestbook = $pdo->prepare("
    SELECT * FROM guestbook 
    WHERE website_id = ? AND approved = 1 
    ORDER BY created_at DESC LIMIT 10
")->execute([$website['id']])->fetchAll();

// Get gallery images
$gallery = $pdo->prepare("
    SELECT * FROM gallery 
    WHERE website_id = ? 
    ORDER BY display_order, created_at DESC
")->execute([$website['id']])->fetchAll();

// Get music
$music = $pdo->prepare("
    SELECT * FROM music 
    WHERE website_id = ? AND is_active = 1 
    LIMIT 1
")->execute([$website['id']])->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan <?= htmlspecialchars($website['groom_name']) ?> & <?= htmlspecialchars($website['bride_name']) ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display&family=Poppins&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #8B4513;
            --secondary: #D4AF37;
            --light: #FFF8DC;
            --dark: #5D4037;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f3e9;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }
        
        .wedding-section {
            width: 100%;
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .section-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }
        
        .section-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 2;
        }
        
        .section-content {
            position: relative;
            z-index: 3;
            color: white;
            text-align: center;
            padding: 40px 20px;
            max-width: 800px;
        }
        
        .couple-names {
            font-family: 'Great Vibes', cursive;
            font-size: 4rem;
            color: var(--secondary);
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .wedding-date {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
        
        .interactive-section {
            padding: 80px 20px;
            background: var(--light);
            color: var(--dark);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
        
        /* Countdown Timer */
        .countdown-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .countdown-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            min-width: 100px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .countdown-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            display: block;
        }
        
        .countdown-label {
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        /* RSVP Form */
        .rsvp-form-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }
        
        /* Gallery */
        .gallery-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .gallery-item {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 250px;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
        }
        
        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        /* Guest Book */
        .guestbook-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .guest-messages {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 30px;
            padding-right: 10px;
        }
        
        .message-item {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .message-author {
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .message-date {
            font-size: 0.8rem;
            color: #888;
            float: right;
        }
        
        /* Music Player */
        .music-player {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 50px;
            padding: 10px 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
        }
        
        .music-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.2rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .music-btn:hover {
            background: var(--light);
        }
        
        .song-info {
            font-size: 0.9rem;
            color: var(--dark);
        }
        
        /* Share Buttons */
        .share-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        
        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .share-btn:hover {
            transform: scale(1.1);
        }
        
        .whatsapp { background: #25D366; }
        .facebook { background: #1877F2; }
        .instagram { background: #E4405F; }
        
        /* QR Code */
        .qr-container {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 15px;
            max-width: 300px;
            margin: 40px auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
            padding: 10px;
            background: white;
            border-radius: 10px;
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        
        /* Navigation Dots */
        .nav-dots {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
        }
        
        .nav-dot {
            display: block;
            width: 12px;
            height: 12px;
            margin: 10px 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .nav-dot.active {
            background: white;
            transform: scale(1.3);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .couple-names {
                font-size: 3rem;
            }
            
            .countdown-timer {
                gap: 10px;
            }
            
            .countdown-item {
                min-width: 70px;
                padding: 15px;
            }
            
            .countdown-number {
                font-size: 1.8rem;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .music-player {
                bottom: 70px;
                left: 20px;
                right: auto;
            }
            
            .share-container {
                top: 70px;
                left: 20px;
                right: auto;
            }
            
            .nav-dots {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .couple-names {
                font-size: 2.5rem;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            
            .countdown-item {
                min-width: 60px;
                padding: 10px;
            }
            
            .rsvp-form-container {
                padding: 20px;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-in {
            animation: fadeIn 1s ease-out;
        }
    </style>
</head>
<body>
    <!-- Navigation Dots -->
    <div class="nav-dots">
        <div class="nav-dot active" data-section="cover"></div>
        <div class="nav-dot" data-section="countdown"></div>
        <div class="nav-dot" data-section="couple"></div>
        <div class="nav-dot" data-section="details"></div>
        <div class="nav-dot" data-section="rsvp"></div>
        <div class="nav-dot" data-section="gallery"></div>
        <div class="nav-dot" data-section="guestbook"></div>
        <div class="nav-dot" data-section="maps"></div>
        <div class="nav-dot" data-section="qr"></div>
    </div>
    
    <!-- Share Buttons -->
    <div class="share-container">
        <button class="share-btn whatsapp" onclick="shareWhatsApp()">
            <i class="bi bi-whatsapp"></i>
        </button>
        <button class="share-btn facebook" onclick="shareFacebook()">
            <i class="bi bi-facebook"></i>
        </button>
        <button class="share-btn instagram" onclick="shareInstagram()">
            <i class="bi bi-instagram"></i>
        </button>
    </div>
    
    <!-- Music Player -->
    <div class="music-player">
        <button class="music-btn" id="playBtn" onclick="toggleMusic()">
            <i class="bi bi-play-fill" id="playIcon"></i>
        </button>
        <div class="song-info">
            <div id="songTitle"><?= $music['song_title'] ?? 'Wedding Song' ?></div>
            <div id="songArtist"><?= $music['artist'] ?? 'Artist' ?></div>
        </div>
        <audio id="weddingMusic" loop>
            <?php if ($music): ?>
            <source src="<?= $music['file_path'] ?>" type="audio/mpeg">
            <?php else: ?>
            <source src="music/wedding-song.mp3" type="audio/mpeg">
            <?php endif; ?>
        </audio>
    </div>
    
    <!-- Section 1: Cover -->
    <section class="wedding-section" id="cover">
        <img src="images/sections/cover.jpg" alt="Cover" class="section-image" 
             onerror="this.src='https://via.placeholder.com/1200x800/8B4513/FFFFFF?text=Cover+Section'">
        <div class="section-overlay"></div>
        <div class="section-content animate-in">
            <h1 class="couple-names">
                <?= htmlspecialchars($website['groom_name']) ?> & <?= htmlspecialchars($website['bride_name']) ?>
            </h1>
            <div class="wedding-date">
                <?= date('d F Y', strtotime($website['wedding_date'])) ?>
            </div>
            <p>Kami mengundang Anda untuk berbagi kebahagiaan kami</p>
            <div style="margin-top: 30px;">
                <button class="btn-submit" style="max-width: 200px;" onclick="scrollToSection('rsvp')">
                    Konfirmasi Kehadiran
                </button>
            </div>
        </div>
    </section>
    
    <!-- Section 2: Countdown -->
    <section class="interactive-section" id="countdown">
        <div class="countdown-container animate-in">
            <h2 class="section-title">Menuju Hari Bahagia</h2>
            <div class="countdown-timer">
                <div class="countdown-item">
                    <span class="countdown-number" id="days">00</span>
                    <span class="countdown-label">Hari</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="hours">00</span>
                    <span class="countdown-label">Jam</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="minutes">00</span>
                    <span class="countdown-label">Menit</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="seconds">00</span>
                    <span class="countdown-label">Detik</span>
                </div>
            </div>
            <p>Tanggal: <?= date('l, d F Y', strtotime($website['wedding_date'])) ?></p>
            <button class="btn-submit" style="max-width: 200px; margin-top: 20px;" 
                    onclick="saveToCalendar()">
                <i class="bi bi-calendar-plus"></i> Simpan ke Kalender
            </button>
        </div>
    </section>
    
    <!-- Section 3: Couple Story -->
    <section class="wedding-section" id="couple">
        <img src="images/sections/couple.jpg" alt="Couple Story" class="section-image"
             onerror="this.src='https://via.placeholder.com/1200x800/C19A6B/FFFFFF?text=Couple+Story'">
        <div class="section-overlay"></div>
        <div class="section-content animate-in">
            <h2 class="section-title" style="color: white;">Our Love Story</h2>
            <p style="font-size: 1.2rem; max-width: 600px; margin: 0 auto;">
                "Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu 
                dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya."
                <br><small>(QS. Ar-Rum: 21)</small>
            </p>
        </div>
    </section>
    
    <!-- Section 4: Event Details -->
    <section class="wedding-section" id="details">
        <img src="images/sections/details.jpg" alt="Event Details" class="section-image"
             onerror="this.src='https://via.placeholder.com/1200x800/D4AF37/FFFFFF?text=Event+Details'">
        <div class="section-overlay"></div>
        <div class="section-content animate-in">
            <h2 class="section-title" style="color: white;">Detail Acara</h2>
            <div style="background: rgba(255,255,255,0.9); color: var(--dark); 
                      padding: 30px; border-radius: 15px; max-width: 500px; margin: 0 auto;">
                <h4>Akad Nikah</h4>
                <p><?= date('H:i', strtotime($website['wedding_date'])) ?> WIB</p>
                
                <h4 style="margin-top: 20px;">Resepsi Pernikahan</h4>
                <p><?= date('H:i', strtotime($website['wedding_date']) + 3600) ?> - Selesai</p>
                
                <h4 style="margin-top: 20px;">Lokasi</h4>
                <p><strong><?= htmlspecialchars($website['location_name']) ?></strong></p>
                <p><?= nl2br(htmlspecialchars($website['location_address'])) ?></p>
                
                <div style="margin-top: 30px;">
                    <button class="btn-submit" onclick="openMaps()" style="margin-right: 10px;">
                        <i class="bi bi-geo-alt"></i> Lihat di Maps
                    </button>
                    <button class="btn-submit" onclick="getDirections()">
                        <i class="bi bi-signpost"></i> Petunjuk Arah
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section 5: RSVP Form -->
    <section class="interactive-section" id="rsvp">
        <h2 class="section-title">Konfirmasi Kehadiran</h2>
        <p class="text-center mb-4">
            Mohon konfirmasi kehadiran Anda sebelum <?= date('d F Y', strtotime($website['wedding_date'] . ' -3 days')) ?>
        </p>
        
        <div class="rsvp-form-container animate-in">
            <form id="rsvpForm">
                <div class="form-group">
                    <input type="text" class="form-control" id="guestName" 
                           placeholder="Nama Lengkap*" required>
                </div>
                
                <div class="form-group">
                    <input type="email" class="form-control" id="guestEmail" 
                           placeholder="Email (opsional)">
                </div>
                
                <div class="form-group">
                    <input type="tel" class="form-control" id="guestPhone" 
                           placeholder="Nomor WhatsApp*" required>
                </div>
                
                <div class="form-group">
                    <select class="form-control" id="attendance" required>
                        <option value="">Pilih Kehadiran*</option>
                        <option value="yes">Hadir</option>
                        <option value="no">Tidak Hadir</option>
                        <option value="maybe">Masih Ragu</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <input type="number" class="form-control" id="guestCount" 
                           min="1" value="1" placeholder="Jumlah Orang*" required>
                </div>
                
                <div class="form-group">
                    <textarea class="form-control" id="guestMessage" 
                              rows="3" placeholder="Ucapan & Doa (opsional)"></textarea>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="bi bi-send"></i> Kirim Konfirmasi
                </button>
            </form>
            
            <div id="rsvpMessage" class="mt-3" style="display: none;"></div>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <?= $rsvp_stats['attending'] ?? 0 ?> tamu telah konfirmasi hadir
                </small>
            </div>
        </div>
    </section>
    
    <!-- Section 6: Gallery -->
    <section class="interactive-section" id="gallery">
        <h2 class="section-title">Gallery Foto</h2>
        
        <div class="gallery-container animate-in">
            <div class="gallery-grid" id="galleryGrid">
                <?php if ($gallery): ?>
                    <?php foreach ($gallery as $photo): ?>
                    <div class="gallery-item">
                        <img src="<?= $photo['image_path'] ?>" 
                             alt="<?= htmlspecialchars($photo['caption']) ?>" 
                             class="gallery-img"
                             onclick="openLightbox('<?= $photo['image_path'] ?>', '<?= htmlspecialchars($photo['caption']) ?>')">
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center" style="grid-column: 1 / -1; padding: 40px;">
                        <i class="bi bi-images" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="mt-3">Gallery foto akan segera diisi</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Section 7: Guest Book -->
    <section class="interactive-section" id="guestbook">
        <h2 class="section-title">Buku Tamu</h2>
        
        <div class="guestbook-container animate-in">
            <!-- Guest Messages -->
            <div class="guest-messages" id="guestMessages">
                <?php if ($guestbook): ?>
                    <?php foreach ($guestbook as $message): ?>
                    <div class="message-item">
                        <div class="message-author">
                            <?= htmlspecialchars($message['author_name']) ?>
                            <span class="message-date">
                                <?= date('d/m/Y', strtotime($message['created_at'])) ?>
                            </span>
                        </div>
                        <div class="message-text">
                            <?= nl2br(htmlspecialchars($message['message'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p>Belum ada ucapan dari tamu</p>
                        <p class="text-muted">Jadilah yang pertama memberikan ucapan!</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Add Message Form -->
            <div class="rsvp-form-container">
                <h5 class="text-center mb-3">Tinggalkan Ucapan</h5>
                <form id="guestbookForm">
                    <div class="form-group">
                        <input type="text" class="form-control" id="messageName" 
                               placeholder="Nama Anda*" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="messageText" 
                                  rows="3" placeholder="Tulis ucapan Anda di sini...*" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-chat-left-text"></i> Kirim Ucapan
                    </button>
                </form>
                <div id="guestbookMessage" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </section>
    
    <!-- Section 8: Maps -->
    <section class="interactive-section" id="maps">
        <h2 class="section-title">Lokasi Acara</h2>
        
        <div class="animate-in" style="max-width: 800px; margin: 0 auto;">
            <div id="googleMap" style="width:100%; height:400px; border-radius:15px; overflow:hidden;"></div>
            
            <div style="margin-top: 30px; text-align: center;">
                <h4><?= htmlspecialchars($website['location_name']) ?></h4>
                <p><?= nl2br(htmlspecialchars($website['location_address'])) ?></p>
                
                <div style="margin-top: 20px;">
                    <button class="btn-submit" onclick="openMaps()" style="margin-right: 10px;">
                        <i class="bi bi-geo-alt"></i> Buka di Google Maps
                    </button>
                    <button class="btn-submit" onclick="openWaze()">
                        <i class="bi bi-signpost-split"></i> Buka di Waze
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section 9: QR Code -->
    <section class="interactive-section" id="qr">
        <h2 class="section-title">Bagikan Undangan</h2>
        
        <div class="qr-container animate-in">
            <div class="qr-code">
                <!-- Dummy QR Code -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode(SITE_URL . '/sites/' . $folder . '/') ?>" 
                     alt="QR Code">
            </div>
            <p>Scan QR code untuk mengakses website ini</p>
            <button class="btn-submit" onclick="shareLink()" style="max-width: 200px;">
                <i class="bi bi-share"></i> Bagikan Link
            </button>
        </div>
        
        <div class="text-center mt-4">
            <p>Terima kasih telah menjadi bagian dari hari bahagia kami</p>
            <h4 style="color: var(--primary);"><?= htmlspecialchars($website['groom_name']) ?> & <?= htmlspecialchars($website['bride_name']) ?></h4>
        </div>
    </section>
    
    <!-- Footer -->
    <footer style="background: var(--dark); color: white; padding: 30px 20px; text-align: center;">
        <p>&copy; <?= date('Y') ?> Undangan Digital <?= htmlspecialchars($website['groom_name']) ?> & <?= htmlspecialchars($website['bride_name']) ?></p>
        <p>Dibuat dengan ❤️ menggunakan WeddingInvite</p>
        <div style="margin-top: 20px;">
            <button class="btn-submit" style="max-width: 200px; background: var(--secondary);" 
                    onclick="scrollToSection('cover')">
                <i class="bi bi-arrow-up"></i> Kembali ke Atas
            </button>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Countdown Timer
        function updateCountdown() {
            const weddingDate = new Date("<?= $website['wedding_date'] ?>").getTime();
            const now = new Date().getTime();
            const distance = weddingDate - now;
            
            if (distance < 0) {
                document.getElementById('days').innerHTML = '00';
                document.getElementById('hours').innerHTML = '00';
                document.getElementById('minutes').innerHTML = '00';
                document.getElementById('seconds').innerHTML = '00';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('days').innerHTML = days.toString().padStart(2, '0');
            document.getElementById('hours').innerHTML = hours.toString().padStart(2, '0');
            document.getElementById('minutes').innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').innerHTML = seconds.toString().padStart(2, '0');
        }
        
        setInterval(updateCountdown, 1000);
        updateCountdown();
        
        // Music Player
        const audio = document.getElementById('weddingMusic');
        const playBtn = document.getElementById('playBtn');
        const playIcon = document.getElementById('playIcon');
        
        function toggleMusic() {
            if (audio.paused) {
                audio.play();
                playIcon.className = 'bi bi-pause-fill';
            } else {
                audio.pause();
                playIcon.className = 'bi bi-play-fill';
            }
        }
        
        // Auto-play music on page load (with user interaction)
        document.addEventListener('click', function initAudio() {
            if (audio.paused) {
                audio.play().catch(e => console.log('Audio play failed:', e));
                playIcon.className = 'bi bi-pause-fill';
            }
            document.removeEventListener('click', initAudio);
        });
        
        // RSVP Form
        document.getElementById('rsvpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('guestName').value,
                email: document.getElementById('guestEmail').value,
                phone: document.getElementById('guestPhone').value,
                attendance: document.getElementById('attendance').value,
                guestCount: document.getElementById('guestCount').value,
                message: document.getElementById('guestMessage').value,
                website_id: <?= $website['id'] ?>
            };
            
            fetch('ajax/rsvp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('rsvpMessage');
                if (data.success) {
                    messageDiv.innerHTML = '<div class="alert alert-success">Terima kasih telah konfirmasi!</div>';
                    document.getElementById('rsvpForm').reset();
                    
                    // Update counter
                    if (formData.attendance === 'yes') {
                        const counter = document.querySelector('#rsvp .text-center small');
                        const current = parseInt(counter.textContent.match(/\d+/)[0]);
                        counter.textContent = (current + 1) + ' tamu telah konfirmasi hadir';
                    }
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
                messageDiv.style.display = 'block';
                
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            });
        });
        
        // Guestbook Form
        document.getElementById('guestbookForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('messageName').value,
                message: document.getElementById('messageText').value,
                website_id: <?= $website['id'] ?>
            };
            
            fetch('ajax/guestbook.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('guestbookMessage');
                if (data.success) {
                    messageDiv.innerHTML = '<div class="alert alert-success">Ucapan berhasil dikirim!</div>';
                    document.getElementById('guestbookForm').reset();
                    
                    // Add message to list
                    const guestMessages = document.getElementById('guestMessages');
                    const messageItem = document.createElement('div');
                    messageItem.className = 'message-item';
                    messageItem.innerHTML = `
                        <div class="message-author">
                            ${formData.name}
                            <span class="message-date">Baru saja</span>
                        </div>
                        <div class="message-text">${formData.message}</div>
                    `;
                    guestMessages.insertBefore(messageItem, guestMessages.firstChild);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
                messageDiv.style.display = 'block';
                
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            });
        });
        
        // Google Maps
        function initMap() {
            <?php if ($website['location_lat'] && $website['location_lng']): ?>
            const location = { lat: <?= $website['location_lat'] ?>, lng: <?= $website['location_lng'] ?> };
            <?php else: ?>
            // Default location (Jakarta)
            const location = { lat: -6.2088, lng: 106.8456 };
            <?php endif; ?>
            
            const map = new google.maps.Map(document.getElementById("googleMap"), {
                zoom: 15,
                center: location,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });
            
            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: "<?= htmlspecialchars($website['location_name']) ?>",
                animation: google.maps.Animation.DROP
            });
            
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h5><?= htmlspecialchars($website['location_name']) ?></h5>
                        <p><?= htmlspecialchars($website['location_address']) ?></p>
                    </div>
                `
            });
            
            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
            
            // Auto-open info window
            infoWindow.open(map, marker);
        }
        
        // Load Google Maps API
        function loadGoogleMaps() {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=<?= GMAPS_API_KEY ?>&callback=initMap`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }
        
        // Load maps when Maps section is visible
        const mapsObserver = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                loadGoogleMaps();
                mapsObserver.disconnect();
            }
        }, { threshold: 0.5 });
        
        mapsObserver.observe(document.getElementById('maps'));
        
        // Navigation
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        // Update navigation dots on scroll
        const sections = document.querySelectorAll('.wedding-section, .interactive-section');
        const navDots = document.querySelectorAll('.nav-dot');
        
        function updateNavDots() {
            let currentSection = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                if (window.scrollY >= sectionTop - 100 && 
                    window.scrollY < sectionTop + sectionHeight - 100) {
                    currentSection = section.id;
                }
            });
            
            navDots.forEach(dot => {
                dot.classList.remove('active');
                if (dot.dataset.section === currentSection) {
                    dot.classList.add('active');
                }
            });
        }
        
        window.addEventListener('scroll', updateNavDots);
        updateNavDots();
        
        // Nav dot clicks
        navDots.forEach(dot => {
            dot.addEventListener('click', () => {
                scrollToSection(dot.dataset.section);
            });
        });
        
        // Share functions
        function shareWhatsApp() {
            const url = window.location.href;
            const text = `Undangan pernikahan <?= htmlspecialchars($website['groom_name']) ?> & <?= htmlspecialchars($website['bride_name']) ?>\n\n${url}`;
            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        }
        
        function shareFacebook() {
            const url = window.location.href;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        }
        
        function shareInstagram() {
            alert('Copy link website dan bagikan di Instagram Story!');
            navigator.clipboard.writeText(window.location.href);
        }
        
        function shareLink() {
            navigator.clipboard.writeText(window.location.href)
                .then(() => alert('Link berhasil disalin!'));
        }
        
        // Location functions
        function openMaps() {
            <?php if ($website['location_lat'] && $website['location_lng']): ?>
            const url = `https://www.google.com/maps?q=<?= $website['location_lat'] ?>,<?= $website['location_lng'] ?>&z=15`;
            <?php else: ?>
            const url = `https://www.google.com/maps/search/?api=1&query=<?= urlencode($website['location_name'] . ' ' . $website['location_address']) ?>`;
            <?php endif; ?>
            window.open(url, '_blank');
        }
        
        function openWaze() {
            <?php if ($website['location_lat'] && $website['location_lng']): ?>
            const url = `https://waze.com/ul?ll=<?= $website['location_lat'] ?>,<?= $website['location_lng'] ?>&navigate=yes`;
            <?php else: ?>
            const url = `https://waze.com/ul?q=<?= urlencode($website['location_name'] . ' ' . $website['location_address']) ?>&navigate=yes`;
            <?php endif; ?>
            window.open(url, '_blank');
        }
        
        function getDirections() {
            openMaps();
        }
        
        // Save to Calendar
        function saveToCalendar() {
            const weddingDate = new Date("<?= $website['wedding_date'] ?>");
            const endDate = new Date(weddingDate.getTime() + 3 * 60 * 60 * 1000); // +3 hours
            
            const icsContent = [
                'BEGIN:VCALENDAR',
                'VERSION:2.0',
                'BEGIN:VEVENT',
                `SUMMARY:Pernikahan ${<?= json_encode($website['groom_name']) ?>} & ${<?= json_encode($website['bride_name']) ?>}`,
                `DTSTART:${weddingDate.toISOString().replace(/[-:]/g, '').split('.')[0]}Z`,
                `DTEND:${endDate.toISOString().replace(/[-:]/g, '').split('.')[0]}Z`,
                `LOCATION:${<?= json_encode($website['location_name'] . ', ' . $website['location_address']) ?>}`,
                'END:VEVENT',
                'END:VCALENDAR'
            ].join('\n');
            
            const blob = new Blob([icsContent], { type: 'text/calendar' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'undangan-pernikahan.ics';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Lightbox for gallery
        function openLightbox(imageSrc, caption) {
            const lightbox = document.createElement('div');
            lightbox.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                cursor: pointer;
            `;
            
            const img = document.createElement('img');
            img.src = imageSrc;
            img.style.cssText = `
                max-width: 90%;
                max-height: 90%;
                object-fit: contain;
                border-radius: 10px;
            `;
            
            if (caption) {
                const captionDiv = document.createElement('div');
                captionDiv.style.cssText = `
                    position: absolute;
                    bottom: 20px;
                    left: 0;
                    width: 100%;
                    text-align: center;
                    color: white;
                    padding: 10px;
                    background: rgba(0,0,0,0.5);
                `;
                captionDiv.textContent = caption;
                lightbox.appendChild(captionDiv);
            }
            
            lightbox.appendChild(img);
            document.body.appendChild(lightbox);
            
            lightbox.addEventListener('click', () => {
                document.body.removeChild(lightbox);
            });
        }
        
        // Add entrance animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });
        
        sections.forEach(section => {
            observer.observe(section);
        });
    </script>
</body>
</html>
