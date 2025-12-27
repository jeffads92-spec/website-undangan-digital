<?php
// index.php - Halaman Pemesanan
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process order
    // This would save to database and redirect to upload page
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Undangan Digital - Wedding Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8B4513;
            --secondary: #D4AF37;
            --accent: #C19A6B;
            --light: #FFF8DC;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .package-card {
            border: 2px solid #ddd;
            border-radius: 15px;
            transition: all 0.3s;
            height: 100%;
        }
        
        .package-card:hover {
            border-color: var(--primary);
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .package-card.featured {
            border-color: var(--secondary);
            position: relative;
            overflow: hidden;
        }
        
        .package-card.featured::before {
            content: 'POPULER';
            position: absolute;
            top: 20px;
            right: -30px;
            background: var(--secondary);
            color: white;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 12px;
            font-weight: bold;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .feature-list li i {
            color: var(--primary);
            margin-right: 10px;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }
        
        .step-process {
            display: flex;
            justify-content: center;
            margin: 50px 0;
        }
        
        .step {
            text-align: center;
            padding: 0 20px;
            position: relative;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background: var(--light);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: bold;
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .step-process {
                flex-direction: column;
            }
            
            .step {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-heart text-danger"></i>
                <span class="fw-bold" style="color: var(--primary);">WeddingInvite</span>
            </a>
            <div class="d-flex">
                <a href="login.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Buat Undangan Pernikahan Digital</h1>
            <p class="lead mb-4">Desain sendiri dengan Canva, hasilkan website undangan modern dengan fitur lengkap</p>
            <a href="#packages" class="btn btn-light btn-lg px-5">
                <i class="fas fa-gift me-2"></i> Lihat Paket
            </a>
        </div>
    </section>

    <!-- How It Works -->
    <section class="container py-5">
        <h2 class="text-center mb-5">Cara Kerjanya</h2>
        <div class="step-process">
            <div class="step">
                <div class="step-number">1</div>
                <h5>Pilih Paket</h5>
                <p>Pilih paket yang sesuai kebutuhan</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h5>Edit di Canva</h5>
                <p>Desain template di Canva sesuai selera</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h5>Upload Section</h5>
                <p>Export & upload section sebagai gambar</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h5>Website Live!</h5>
                <p>Dapatkan website undangan digital</p>
            </div>
        </div>
    </section>

    <!-- Packages -->
    <section id="packages" class="container py-5">
        <h2 class="text-center mb-5">Pilih Paket Undangan</h2>
        <div class="row">
            <!-- Package 1 -->
            <div class="col-md-4 mb-4">
                <div class="package-card p-4">
                    <h3 class="text-center mb-4">Paket Basic</h3>
                    <div class="text-center mb-4">
                        <span class="display-5 fw-bold" style="color: var(--primary);">Rp 299.000</span>
                        <small class="text-muted d-block">One-time payment</small>
                    </div>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> 1 Template Canva Premium</li>
                        <li><i class="fas fa-check"></i> Website Undangan Digital</li>
                        <li><i class="fas fa-check"></i> Form RSVP Online (max 100 tamu)</li>
                        <li><i class="fas fa-check"></i> Countdown Timer</li>
                        <li><i class="fas fa-check"></i> Google Maps Lokasi</li>
                        <li><i class="fas fa-check"></i> 10 Foto Gallery</li>
                        <li><i class="fas fa-check"></i> Guest Book Online</li>
                        <li><i class="fas fa-check"></i> 1 Lagu Background</li>
                        <li><i class="fas fa-check"></i> QR Code Share</li>
                        <li><i class="fas fa-check"></i> Admin Dashboard</li>
                        <li><i class="fas fa-check"></i> Hosting 6 Bulan</li>
                    </ul>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary-custom w-100" onclick="selectPackage('basic')">
                            Pilih Paket Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- Package 2 - Featured -->
            <div class="col-md-4 mb-4">
                <div class="package-card p-4 featured">
                    <h3 class="text-center mb-4">Paket Premium</h3>
                    <div class="text-center mb-4">
                        <span class="display-5 fw-bold" style="color: var(--secondary);">Rp 599.000</span>
                        <small class="text-muted d-block">One-time payment</small>
                    </div>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Semua Fitur Basic</li>
                        <li><i class="fas fa-check"></i> 3 Template Canva Premium</li>
                        <li><i class="fas fa-check"></i> RSVP Unlimited Tamu</li>
                        <li><i class="fas fa-check"></i> Gallery Foto Unlimited</li>
                        <li><i class="fas fa-check"></i> Playlist Musik (5 lagu)</li>
                        <li><i class="fas fa-check"></i> Video Background/Teaser</li>
                        <li><i class="fas fa-check"></i> Live Streaming Embed</li>
                        <li><i class="fas fa-check"></i> Gift Registry Link</li>
                        <li><i class="fas fa-check"></i> Custom Domain (.com)</li>
                        <li><i class="fas fa-check"></i> Hosting 1 Tahun</li>
                        <li><i class="fas fa-check"></i> Priority Support</li>
                    </ul>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary-custom w-100" onclick="selectPackage('premium')">
                            <i class="fas fa-crown me-2"></i> Pilih Paket Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- Package 3 -->
            <div class="col-md-4 mb-4">
                <div class="package-card p-4">
                    <h3 class="text-center mb-4">Paket Platinum</h3>
                    <div class="text-center mb-4">
                        <span class="display-5 fw-bold" style="color: var(--primary);">Rp 1.299.000</span>
                        <small class="text-muted d-block">One-time payment</small>
                    </div>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Semua Fitur Premium</li>
                        <li><i class="fas fa-check"></i> Full Custom Design</li>
                        <li><i class="fas fa-check"></i> Wedding Website + Mobile App</li>
                        <li><i class="fas fa-check"></i> WhatsApp Blast Invitation</li>
                        <li><i class="fas fa-check"></i> Guest Management System</li>
                        <li><i class="fas fa-check"></i> Seat Arrangement Planner</li>
                        <li><i class="fas fa-check"></i> Analytics Dashboard</li>
                        <li><i class="fas fa-check"></i> Multi-language Support</li>
                        <li><i class="fas fa-check"></i> E-invitation with Tracking</li>
                        <li><i class="fas fa-check"></i> Hosting 2 Tahun</li>
                        <li><i class="fas fa-check"></i> Dedicated Support</li>
                    </ul>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary-custom w-100" onclick="selectPackage('platinum')">
                            Pilih Paket Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Form Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Pemesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm" action="generator.php" method="POST">
                        <input type="hidden" name="package" id="selectedPackage">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Customer*</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email*</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor WhatsApp*</label>
                                <input type="tel" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Folder Website*</label>
                                <input type="text" name="website_folder" class="form-control" required>
                                <small class="text-muted">Contoh: rina-budi-2024 (tanpa spasi)</small>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Data Pernikahan</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Pengantin Pria*</label>
                                <input type="text" name="groom_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Pengantin Wanita*</label>
                                <input type="text" name="bride_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal & Waktu Pernikahan*</label>
                                <input type="datetime-local" name="wedding_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lokasi*</label>
                                <input type="text" name="location_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap*</label>
                            <textarea name="location_address" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informasi Penting</h6>
                            <p class="mb-0">Setelah pesanan dikonfirmasi, Anda akan mendapatkan:</p>
                            <ol class="mb-0">
                                <li>Link untuk mengedit template di Canva</li>
                                <li>Link upload untuk mengupload section yang sudah diedit</li>
                                <li>Admin dashboard untuk mengelola website</li>
                            </ol>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary-custom px-5">
                                <i class="fas fa-shopping-cart me-2"></i> Pesan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-heart text-danger"></i> WeddingInvite</h5>
                    <p>Platform undangan digital modern untuk pernikahan Anda.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Contact: info@weddinginvite.com</p>
                    <p>WhatsApp: +62 812-3456-7890</p>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">&copy; 2024 WeddingInvite. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPackage(packageType) {
            document.getElementById('selectedPackage').value = packageType;
            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
            orderModal.show();
        }
        
        // Auto-generate folder name from couple names
        document.querySelector('input[name="groom_name"]').addEventListener('input', function() {
            const groom = this.value.toLowerCase().replace(/\s+/g, '-');
            const bride = document.querySelector('input[name="bride_name"]').value.toLowerCase().replace(/\s+/g, '-');
            const year = new Date().getFullYear();
            
            if(groom && bride) {
                document.querySelector('input[name="website_folder"]').value = `${groom}-${bride}-${year}`;
            }
        });
        
        document.querySelector('input[name="bride_name"]').addEventListener('input', function() {
            const groom = document.querySelector('input[name="groom_name"]').value.toLowerCase().replace(/\s+/g, '-');
            const bride = this.value.toLowerCase().replace(/\s+/g, '-');
            const year = new Date().getFullYear();
            
            if(groom && bride) {
                document.querySelector('input[name="website_folder"]').value = `${groom}-${bride}-${year}`;
            }
        });
    </script>
</body>
</html>
