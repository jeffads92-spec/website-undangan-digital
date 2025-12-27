<?php
// admin.php - Admin Dashboard untuk Customer
require_once 'config.php';

// Check login
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$website_folder = $_SESSION['website_folder'];

// Get customer data
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

// Get statistics
$rsvp_count = $pdo->prepare("SELECT COUNT(*) as count, 
    SUM(CASE WHEN attendance = 'yes' THEN 1 ELSE 0 END) as attending,
    SUM(CASE WHEN attendance = 'no' THEN 1 ELSE 0 END) as not_attending,
    SUM(guest_count) as total_guests
    FROM rsvp WHERE website_id = ?")->execute([$customer_id])->fetch();

$guestbook_count = $pdo->prepare("SELECT COUNT(*) as count FROM guestbook WHERE website_id = ?")->execute([$customer_id])->fetch();

$gallery_count = $pdo->prepare("SELECT COUNT(*) as count FROM gallery WHERE website_id = ?")->execute([$customer_id])->fetch();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_guestbook':
            // Approve/reject guestbook message
            break;
            
        case 'update_settings':
            // Update website settings
            break;
            
        case 'add_gallery':
            // Add gallery image
            break;
            
        case 'delete_rsvp':
            // Delete RSVP entry
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= $customer['website_folder'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8B4513;
            --secondary: #D4AF37;
            --light: #FFF8DC;
        }
        
        .sidebar {
            background: var(--primary);
            color: white;
            min-height: 100vh;
            padding: 0;
        }
        
        .sidebar-brand {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav li {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-nav a {
            color: white;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid var(--secondary);
        }
        
        .sidebar-nav i {
            width: 20px;
            margin-right: 10px;
        }
        
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card.primary {
            background: linear-gradient(135deg, var(--primary) 0%, #A0522D 100%);
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #17a2b8 0%, #0dcaf0 100%);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .website-url {
            background: var(--light);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .table-responsive {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .nav-tabs .nav-link.active {
            border-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
        }
        
        .btn-custom {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
        }
        
        .btn-custom:hover {
            background: #A0522D;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-brand">
                    <h4><i class="fas fa-heart"></i> Admin Panel</h4>
                    <small><?= $customer['website_folder'] ?></small>
                </div>
                
                <ul class="sidebar-nav">
                    <li>
                        <a href="#dashboard" class="active" data-bs-toggle="tab">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#rsvp" data-bs-toggle="tab">
                            <i class="fas fa-users"></i> RSVP Tamu
                        </a>
                    </li>
                    <li>
                        <a href="#guestbook" data-bs-toggle="tab">
                            <i class="fas fa-book"></i> Guest Book
                        </a>
                    </li>
                    <li>
                        <a href="#gallery" data-bs-toggle="tab">
                            <i class="fas fa-images"></i> Gallery
                        </a>
                    </li>
                    <li>
                        <a href="#settings" data-bs-toggle="tab">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a href="#website" data-bs-toggle="tab">
                            <i class="fas fa-globe"></i> Website Info
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-auto p-4">
                <!-- Dashboard Tab -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="dashboard">
                        <h2 class="mb-4">Dashboard Admin</h2>
                        
                        <!-- Stats Cards -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-card primary">
                                    <div class="stat-number"><?= $rsvp_count['count'] ?? 0 ?></div>
                                    <div>Total RSVP</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card success">
                                    <div class="stat-number"><?= $rsvp_count['attending'] ?? 0 ?></div>
                                    <div>Konfirmasi Hadir</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card warning">
                                    <div class="stat-number"><?= $rsvp_count['total_guests'] ?? 0 ?></div>
                                    <div>Total Tamu</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card info">
                                    <div class="stat-number"><?= $guestbook_count['count'] ?? 0 ?></div>
                                    <div>Ucapan Tamu</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Website URL -->
                        <div class="website-url">
                            <h5><i class="fas fa-link"></i> URL Website Anda:</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       value="<?= SITE_URL ?>/sites/<?= $website_folder ?>/" 
                                       readonly id="websiteUrl">
                                <button class="btn btn-outline-primary" onclick="copyToClipboard()">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                                <a href="/sites/<?= $website_folder ?>/" 
                                   target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> Visit
                                </a>
                            </div>
                        </div>
                        
                        <!-- Recent RSVP -->
                        <div class="table-responsive">
                            <h5 class="mb-3">RSVP Terbaru</h5>
                            <?php
                            $recent_rsvp = $pdo->prepare("
                                SELECT * FROM rsvp 
                                WHERE website_id = ? 
                                ORDER BY submitted_at DESC 
                                LIMIT 10
                            ")->execute([$customer_id])->fetchAll();
                            ?>
                            
                            <?php if ($recent_rsvp): ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Kehadiran</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_rsvp as $rsvp): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($rsvp['guest_name']) ?></td>
                                        <td><?= htmlspecialchars($rsvp['email']) ?></td>
                                        <td>
                                            <?php if ($rsvp['attendance'] == 'yes'): ?>
                                                <span class="badge bg-success">Hadir</span>
                                            <?php elseif ($rsvp['attendance'] == 'no'): ?>
                                                <span class="badge bg-danger">Tidak</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Ragu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $rsvp['guest_count'] ?> orang</td>
                                        <td><?= date('d/m/Y H:i', strtotime($rsvp['submitted_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p>Belum ada RSVP</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- RSVP Tab -->
                    <div class="tab-pane fade" id="rsvp">
                        <h2 class="mb-4">Data RSVP Tamu</h2>
                        
                        <div class="table-responsive">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <button class="btn btn-custom me-2" onclick="exportRSVP()">
                                        <i class="fas fa-download"></i> Export CSV
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="printRSVP()">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                                <div>
                                    <input type="text" class="form-control" placeholder="Cari tamu..." 
                                           onkeyup="filterRSVP(this.value)">
                                </div>
                            </div>
                            
                            <?php
                            $all_rsvp = $pdo->prepare("
                                SELECT * FROM rsvp 
                                WHERE website_id = ? 
                                ORDER BY submitted_at DESC
                            ")->execute([$customer_id])->fetchAll();
                            ?>
                            
                            <table class="table table-hover" id="rsvpTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Tamu</th>
                                        <th>Email/Telepon</th>
                                        <th>Kehadiran</th>
                                        <th>Jumlah</th>
                                        <th>Ucapan</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_rsvp as $index => $rsvp): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($rsvp['guest_name']) ?></td>
                                        <td><?= htmlspecialchars($rsvp['email']) ?></td>
                                        <td>
                                            <select class="form-select form-select-sm attendance-select" 
                                                    data-id="<?= $rsvp['id'] ?>"
                                                    style="width: 100px;">
                                                <option value="yes" <?= $rsvp['attendance'] == 'yes' ? 'selected' : '' ?>>Hadir</option>
                                                <option value="no" <?= $rsvp['attendance'] == 'no' ? 'selected' : '' ?>>Tidak</option>
                                                <option value="maybe" <?= $rsvp['attendance'] == 'maybe' ? 'selected' : '' ?>>Ragu</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm guest-count" 
                                                   value="<?= $rsvp['guest_count'] ?>" min="1"
                                                   data-id="<?= $rsvp['id'] ?>" style="width: 80px;">
                                        </td>
                                        <td>
                                            <?php if ($rsvp['message']): ?>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="showMessage('<?= addslashes($rsvp['message']) ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($rsvp['submitted_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="deleteRSVP(<?= $rsvp['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <div class="text-end mt-3">
                                <strong>Total Tamu: <?= $rsvp_count['total_guests'] ?? 0 ?> orang</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Guestbook Tab -->
                    <div class="tab-pane fade" id="guestbook">
                        <h2 class="mb-4">Ucapan Tamu</h2>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <?php
                                    $guestbook = $pdo->prepare("
                                        SELECT * FROM guestbook 
                                        WHERE website_id = ? 
                                        ORDER BY created_at DESC
                                    ")->execute([$customer_id])->fetchAll();
                                    ?>
                                    
                                    <?php if ($guestbook): ?>
                                    <?php foreach ($guestbook as $message): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="card-title"><?= htmlspecialchars($message['author_name']) ?></h6>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
                                                </small>
                                            </div>
                                            <p class="card-text"><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                                            <div class="d-flex justify-content-end">
                                                <?php if ($message['approved']): ?>
                                                <span class="badge bg-success me-2">Approved</span>
                                                <?php else: ?>
                                                <span class="badge bg-warning me-2">Pending</span>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteMessage(<?= $message['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                        <p>Belum ada ucapan dari tamu</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Moderasi Ucapan</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Semua ucapan akan muncul otomatis di website.</p>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="autoApprove" checked>
                                            <label class="form-check-label" for="autoApprove">
                                                Auto-approve ucapan baru
                                            </label>
                                        </div>
                                        <hr>
                                        <h6>Filter Ucapan:</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="filterBadWords" checked>
                                            <label class="form-check-label" for="filterBadWords">
                                                Filter kata tidak pantas
                                            </label>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-custom w-100" onclick="refreshGuestbook()">
                                                <i class="fas fa-sync"></i> Refresh
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Tab -->
                    <div class="tab-pane fade" id="gallery">
                        <h2 class="mb-4">Gallery Foto</h2>
                        
                        <div class="mb-4">
                            <form id="galleryUploadForm" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Upload Foto Baru</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <input type="file" class="form-control" 
                                                   id="galleryImages" multiple accept="image/*">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" 
                                                   placeholder="Caption foto (opsional)" id="imageCaption">
                                        </div>
                                        <button type="button" class="btn btn-custom" onclick="uploadGallery()">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                        <div class="progress mt-3" style="display: none;" id="uploadProgress">
                                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="row" id="galleryContainer">
                            <?php
                            $gallery = $pdo->prepare("
                                SELECT * FROM gallery 
                                WHERE website_id = ? 
                                ORDER BY display_order, created_at DESC
                            ")->execute([$customer_id])->fetchAll();
                            ?>
                            
                            <?php if ($gallery): ?>
                            <?php foreach ($gallery as $photo): ?>
                            <div class="col-md-3 mb-4" id="photo-<?= $photo['id'] ?>">
                                <div class="card">
                                    <img src="/sites/<?= $website_folder ?>/<?= $photo['image_path'] ?>" 
                                         class="card-img-top" alt="<?= $photo['caption'] ?>"
                                         style="height: 150px; object-fit: cover;">
                                    <div class="card-body">
                                        <p class="card-text small"><?= htmlspecialchars($photo['caption']) ?></p>
                                        <div class="d-flex justify-content-between">
                                            <input type="number" class="form-control form-control-sm order-input" 
                                                   value="<?= $photo['display_order'] ?>" min="0" 
                                                   data-id="<?= $photo['id'] ?>" style="width: 60px;">
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="deletePhoto(<?= $photo['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p>Belum ada foto di gallery</p>
                                <p class="text-muted">Upload foto dari halaman upload section</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-pane fade" id="settings">
                        <h2 class="mb-4">Pengaturan Website</h2>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Informasi Pernikahan</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="weddingInfoForm">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Pengantin Pria</label>
                                                <input type="text" class="form-control" 
                                                       value="<?= htmlspecialchars($customer['groom_name']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Pengantin Wanita</label>
                                                <input type="text" class="form-control" 
                                                       value="<?= htmlspecialchars($customer['bride_name']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Pernikahan</label>
                                                <input type="datetime-local" class="form-control" 
                                                       value="<?= date('Y-m-d\TH:i', strtotime($customer['wedding_date'])) ?>">
                                            </div>
                                            <button type="button" class="btn btn-custom">Update</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Lokasi Acara</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="locationForm">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Lokasi</label>
                                                <input type="text" class="form-control" 
                                                       value="<?= htmlspecialchars($customer['location_name']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alamat Lengkap</label>
                                                <textarea class="form-control" rows="3"><?= htmlspecialchars($customer['location_address']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Koordinat (Latitude, Longitude)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" 
                                                           placeholder="Latitude" 
                                                           value="<?= $customer['location_lat'] ?>">
                                                    <input type="text" class="form-control" 
                                                           placeholder="Longitude" 
                                                           value="<?= $customer['location_lng'] ?>">
                                                </div>
                                                <small class="text-muted">
                                                    Dapatkan koordinat dari 
                                                    <a href="https://maps.google.com" target="_blank">Google Maps</a>
                                                </small>
                                            </div>
                                            <button type="button" class="btn btn-custom">Update</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Pengaturan Lainnya</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enableMusic" checked>
                                            <label class="form-check-label" for="enableMusic">
                                                Aktifkan musik background
                                            </label>
                                        </div>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enableCountdown" checked>
                                            <label class="form-check-label" for="enableCountdown">
                                                Tampilkan countdown timer
                                            </label>
                                        </div>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enableRSVP" checked>
                                            <label class="form-check-label" for="enableRSVP">
                                                Aktifkan form RSVP
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enableGuestbook" checked>
                                            <label class="form-check-label" for="enableGuestbook">
                                                Aktifkan guest book
                                            </label>
                                        </div>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enableGallery" checked>
                                            <label class="form-check-label" for="enableGallery">
                                                Tampilkan gallery foto
                                            </label>
                                        </div>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="enableShare" checked>
                                            <label class="form-check-label" for="enableShare">
                                                Tampilkan tombol share
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="btn btn-custom mt-3" onclick="saveSettings()">
                                    <i class="fas fa-save"></i> Simpan Semua Pengaturan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Website Info Tab -->
                    <div class="tab-pane fade" id="website">
                        <h2 class="mb-4">Informasi Website</h2>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Statistik Website</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-primary"><?= $rsvp_count['count'] ?? 0 ?></h3>
                                                    <p class="mb-0">Total RSVP</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-success"><?= $rsvp_count['attending'] ?? 0 ?></h3>
                                                    <p class="mb-0">Konfirmasi Hadir</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-warning"><?= $guestbook_count['count'] ?? 0 ?></h3>
                                                    <p class="mb-0">Ucapan Tamu</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded">
                                                    <h3 class="text-info"><?= $gallery_count['count'] ?? 0 ?></h3>
                                                    <p class="mb-0">Foto Gallery</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Tools & Utilities</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <button class="btn btn-outline-primary w-100" onclick="generateQRCode()">
                                                    <i class="fas fa-qrcode"></i> Generate QR Code Baru
                                                </button>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <button class="btn btn-outline-warning w-100" onclick="clearCache()">
                                                    <i class="fas fa-broom"></i> Clear Cache
                                                </button>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <button class="btn btn-outline-info w-100" onclick="backupData()">
                                                    <i class="fas fa-download"></i> Backup Data
                                                </button>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <button class="btn btn-outline-danger w-100" onclick="resetWebsite()">
                                                    <i class="fas fa-redo"></i> Reset Website
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">QR Code Website</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <!-- QR Code Dummy -->
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode(SITE_URL . '/sites/' . $website_folder . '/') ?>" 
                                             alt="QR Code" class="img-fluid mb-3">
                                        <p class="small text-muted">
                                            Scan QR code untuk mengakses website undangan
                                        </p>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="downloadQRCode()">
                                            <i class="fas fa-download"></i> Download QR
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Support & Bantuan</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Jika ada masalah dengan website Anda:</p>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-phone me-2"></i> WhatsApp: +62 812-3456-7890</li>
                                            <li><i class="fas fa-envelope me-2"></i> Email: support@weddinginvite.com</li>
                                        </ul>
                                        <button class="btn btn-custom w-100 mt-2" onclick="openSupport()">
                                            <i class="fas fa-question-circle"></i> Hubungi Support
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pesan Tamu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="messageContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tabs
        document.addEventListener('DOMContentLoaded', function() {
            var triggerTabList = [].slice.call(document.querySelectorAll('a[data-bs-toggle="tab"]'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });
        
        function copyToClipboard() {
            var copyText = document.getElementById("websiteUrl");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            
            alert("URL berhasil disalin!");
        }
        
        function showMessage(message) {
            document.getElementById('messageContent').innerHTML = 
                '<div class="alert alert-info">' + message + '</div>';
            new bootstrap.Modal(document.getElementById('messageModal')).show();
        }
        
        function exportRSVP() {
            alert("Fitur export CSV akan diimplementasi!");
        }
        
        function filterRSVP(search) {
            var rows = document.querySelectorAll('#rsvpTable tbody tr');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.includes(search.toLowerCase()) ? '' : 'none';
            });
        }
        
        function deleteRSVP(id) {
            if (confirm("Hapus RSVP ini?")) {
                fetch('ajax.php?action=delete_rsvp&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        }
        
        function deleteMessage(id) {
            if (confirm("Hapus ucapan ini?")) {
                fetch('ajax.php?action=delete_message&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        }
        
        function deletePhoto(id) {
            if (confirm("Hapus foto ini?")) {
                fetch('ajax.php?action=delete_photo&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('photo-' + id).remove();
                        }
                    });
            }
        }
        
        function uploadGallery() {
            var files = document.getElementById('galleryImages').files;
            if (files.length === 0) {
                alert('Pilih foto terlebih dahulu!');
                return;
            }
            
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }
            
            var caption = document.getElementById('imageCaption').value;
            formData.append('caption', caption);
            formData.append('website_id', <?= $customer_id ?>);
            
            var progressBar = document.getElementById('uploadProgress');
            progressBar.style.display = 'block';
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax.php?action=upload_gallery', true);
            
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    var percentComplete = (e.loaded / e.total) * 100;
                    progressBar.querySelector('.progress-bar').style.width = percentComplete + '%';
                }
            };
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Foto berhasil diupload!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
                progressBar.style.display = 'none';
            };
            
            xhr.send(formData);
        }
        
        function saveSettings() {
            var settings = {
                enableMusic: document.getElementById('enableMusic').checked,
                enableCountdown: document.getElementById('enableCountdown').checked,
                enableRSVP: document.getElementById('enableRSVP').checked,
                enableGuestbook: document.getElementById('enableGuestbook').checked,
                enableGallery: document.getElementById('enableGallery').checked,
                enableShare: document.getElementById('enableShare').checked
            };
            
            fetch('ajax.php?action=save_settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({settings: settings, website_id: <?= $customer_id ?>})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pengaturan berhasil disimpan!');
                }
            });
        }
        
        function generateQRCode() {
            alert("QR code baru akan digenerate!");
        }
        
        function downloadQRCode() {
            var qrUrl = document.querySelector('#website img').src;
            var link = document.createElement('a');
            link.href = qrUrl;
            link.download = 'qrcode-<?= $website_folder ?>.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        function clearCache() {
            if (confirm("Clear cache website?")) {
                fetch('ajax.php?action=clear_cache&website_id=<?= $customer_id ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Cache berhasil dibersihkan!");
                        }
                    });
            }
        }
        
        function backupData() {
            window.open('ajax.php?action=backup_data&website_id=<?= $customer_id ?>', '_blank');
        }
        
        function resetWebsite() {
            if (confirm("Reset website ke pengaturan default? Data tidak akan dihapus.")) {
                fetch('ajax.php?action=reset_website&website_id=<?= $customer_id ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Website berhasil direset!");
                        }
                    });
            }
        }
        
        function openSupport() {
            window.open('https://wa.me/6281234567890?text=Halo%20support,%20saya%20butuh%20bantuan%20untuk%20website%20<?= $website_folder ?>', '_blank');
        }
        
        // Auto-save attendance changes
        document.querySelectorAll('.attendance-select').forEach(select => {
            select.addEventListener('change', function() {
                var id = this.getAttribute('data-id');
                var attendance = this.value;
                
                fetch('ajax.php?action=update_attendance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: id, attendance: attendance})
                });
            });
        });
        
        // Auto-save guest count changes
        document.querySelectorAll('.guest-count').forEach(input => {
            input.addEventListener('change', function() {
                var id = this.getAttribute('data-id');
                var count = this.value;
                
                fetch('ajax.php?action=update_guest_count', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: id, count: count})
                });
            });
        });
        
        // Auto-save display order
        document.querySelectorAll('.order-input').forEach(input => {
            input.addEventListener('change', function() {
                var id = this.getAttribute('data-id');
                var order = this.value;
                
                fetch('ajax.php?action=update_photo_order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: id, order: order})
                });
            });
        });
        
        function refreshGuestbook() {
            var container = document.querySelector('#guestbook .table-responsive');
            fetch('ajax.php?action=get_guestbook&website_id=<?= $customer_id ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update guestbook display
                        alert("Guestbook diperbarui!");
                    }
                });
        }
        
        function printRSVP() {
            window.print();
        }
    </script>
</body>
</html>
