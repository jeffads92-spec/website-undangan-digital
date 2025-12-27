<?php
// upload.php - Upload section gambar dari Canva
require_once 'config.php';

$token = $_GET['token'] ?? '';
$website_folder = $_GET['folder'] ?? '';

// Verify token
$stmt = $pdo->prepare("SELECT * FROM customers WHERE admin_token = ?");
$stmt->execute([$token]);
$customer = $stmt->fetch();

if (!$customer) {
    die("Token tidak valid!");
}

$website_path = SITES_PATH . $customer['website_folder'] . '/';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file = $_FILES['image'];
        
        // Validate image
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed_types)) {
            die("File harus JPG, PNG, atau WebP!");
        }
        
        // Compress image
        $image_info = getimagesize($file['tmp_name']);
        $new_width = 1200; // Max width for web
        
        if ($image_info[0] > $new_width) {
            // Resize image
            $ratio = $new_width / $image_info[0];
            $new_height = $image_info[1] * $ratio;
            
            if ($file['type'] === 'image/jpeg') {
                $src = imagecreatefromjpeg($file['tmp_name']);
                $dst = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $image_info[0], $image_info[1]);
                imagejpeg($dst, $website_path . "images/sections/{$section}.jpg", 85);
            } elseif ($file['type'] === 'image/png') {
                $src = imagecreatefrompng($file['tmp_name']);
                $dst = imagecreatetruecolor($new_width, $new_height);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $image_info[0], $image_info[1]);
                imagepng($dst, $website_path . "images/sections/{$section}.png", 7);
            }
            
            imagedestroy($src);
            imagedestroy($dst);
        } else {
            // Just copy the file
            move_uploaded_file($file['tmp_name'], $website_path . "images/sections/{$section}." . pathinfo($file['name'], PATHINFO_EXTENSION));
        }
        
        // Update website HTML to use new image
        // updateWebsiteImage($customer['website_folder'], $section);
        
        echo json_encode(['success' => true, 'message' => 'Section uploaded!']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Section Canva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .upload-section {
            border: 3px dashed #ddd;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .upload-section:hover {
            border-color: #8B4513;
            background: #FFF8DC;
        }
        
        .upload-section.dragover {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .preview-img {
            max-width: 100%;
            max-height: 200px;
            margin-top: 20px;
            border-radius: 10px;
        }
        
        .progress {
            height: 25px;
            margin: 20px 0;
        }
        
        .section-complete {
            border-color: #28a745;
            background: #d4edda;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-5">Upload Section dari Canva</h1>
        <p class="text-center mb-4">
            Export masing-masing section dari Canva sebagai JPG/PNG, lalu upload di sini
        </p>
        
        <div class="row">
            <!-- Section 1: Cover -->
            <div class="col-md-4 mb-4">
                <div class="upload-section" data-section="cover" id="upload-cover">
                    <h4>1. COVER</h4>
                    <p>Background dengan nama pengantin</p>
                    <input type="file" class="d-none" accept="image/*" 
                           onchange="uploadSection('cover', this.files[0])">
                    <img id="preview-cover" class="preview-img">
                    <div class="progress mt-3" style="display: none;">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Section 2: Couple Story -->
            <div class="col-md-4 mb-4">
                <div class="upload-section" data-section="couple" id="upload-couple">
                    <h4>2. COUPLE STORY</h4>
                    <p>Foto & cerita pengantin</p>
                    <input type="file" class="d-none" accept="image/*" 
                           onchange="uploadSection('couple', this.files[0])">
                    <img id="preview-couple" class="preview-img">
                </div>
            </div>
            
            <!-- Section 3: Event Details -->
            <div class="col-md-4 mb-4">
                <div class="upload-section" data-section="details" id="upload-details">
                    <h4>3. EVENT DETAILS</h4>
                    <p>Jadwal & lokasi acara</p>
                    <input type="file" class="d-none" accept="image/*" 
                           onchange="uploadSection('details', this.files[0])">
                    <img id="preview-details" class="preview-img">
                </div>
            </div>
            
            <!-- Section 4: Gallery -->
            <div class="col-md-4 mb-4">
                <div class="upload-section" data-section="gallery" id="upload-gallery">
                    <h4>4. GALLERY</h4>
                    <p>Foto-foto prewedding (banyak)</p>
                    <input type="file" class="d-none" accept="image/*" multiple
                           onchange="uploadGallery(this.files)">
                    <div id="gallery-preview"></div>
                </div>
            </div>
            
            <!-- Section 5: Family -->
            <div class="col-md-4 mb-4">
                <div class="upload-section" data-section="family" id="upload-family">
                    <h4>5. FAMILY</h4>
                    <p>Orang tua & keluarga (opsional)</p>
                    <input type="file" class="d-none" accept="image/*" 
                           onchange="uploadSection('family', this.files[0])">
                    <img id="preview-family" class="preview-img">
                </div>
            </div>
            
            <!-- Section 6: Thank You -->
            <div class="col-md-4 mb-4">
                <div class="upload-section" data-section="thankyou" id="upload-thankyou">
                    <h4>6. THANK YOU</h4>
                    <p>Penutup & ucapan terima kasih</p>
                    <input type="file" class="d-none" accept="image/*" 
                           onchange="uploadSection('thankyou', this.files[0])">
                    <img id="preview-thankyou" class="preview-img">
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <div id="upload-status" class="alert alert-info" style="display: none;"></div>
            <button class="btn btn-success btn-lg px-5" onclick="finishUpload()">
                <i class="fas fa-check"></i> Selesai & Lihat Website
            </button>
            <a href="<?= SITE_URL ?>/sites/<?= $customer['website_folder'] ?>/" 
               target="_blank" class="btn btn-primary btn-lg px-5 ms-3">
                <i class="fas fa-eye"></i> Preview Website
            </a>
        </div>
    </div>
    
    <script>
        // Drag and drop functionality
        document.querySelectorAll('.upload-section').forEach(section => {
            section.addEventListener('dragover', (e) => {
                e.preventDefault();
                section.classList.add('dragover');
            });
            
            section.addEventListener('dragleave', () => {
                section.classList.remove('dragover');
            });
            
            section.addEventListener('drop', (e) => {
                e.preventDefault();
                section.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                const input = section.querySelector('input[type="file"]');
                
                if (input.hasAttribute('multiple')) {
                    uploadGallery(files);
                } else {
                    uploadSection(section.dataset.section, files[0]);
                }
            });
            
            section.addEventListener('click', () => {
                section.querySelector('input[type="file"]').click();
            });
        });
        
        function uploadSection(section, file) {
            if (!file) return;
            
            const formData = new FormData();
            formData.append('section', section);
            formData.append('image', file);
            formData.append('token', '<?= $token ?>');
            
            const progressBar = document.querySelector(`#upload-${section} .progress`);
            const progressFill = progressBar.querySelector('.progress-bar');
            
            progressBar.style.display = 'block';
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);
            
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = (e.loaded / e.total) * 100;
                    progressFill.style.width = percent + '%';
                }
            };
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById(`preview-${section}`).src = e.target.result;
                            document.getElementById(`upload-${section}`).classList.add('section-complete');
                        };
                        reader.readAsDataURL(file);
                        
                        showStatus(`Section ${section} berhasil diupload!`, 'success');
                    }
                }
                progressBar.style.display = 'none';
            };
            
            xhr.send(formData);
        }
        
        function uploadGallery(files) {
            if (!files || files.length === 0) return;
            
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('gallery[]', files[i]);
            }
            formData.append('section', 'gallery');
            formData.append('token', '<?= $token ?>');
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php?action=gallery', true);
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Show gallery preview
                        const galleryPreview = document.getElementById('gallery-preview');
                        galleryPreview.innerHTML = '';
                        
                        for (let i = 0; i < files.length; i++) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'preview-img me-2';
                                img.style.width = '80px';
                                img.style.height = '80px';
                                img.style.objectFit = 'cover';
                                galleryPreview.appendChild(img);
                            };
                            reader.readAsDataURL(files[i]);
                        }
                        
                        document.getElementById('upload-gallery').classList.add('section-complete');
                        showStatus(`${files.length} foto berhasil diupload ke gallery!`, 'success');
                    }
                }
            };
            
            xhr.send(formData);
        }
        
        function showStatus(message, type) {
            const statusDiv = document.getElementById('upload-status');
            statusDiv.textContent = message;
            statusDiv.className = `alert alert-${type}`;
            statusDiv.style.display = 'block';
            
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 3000);
        }
        
        function finishUpload() {
            alert('Selamat! Website Anda sudah siap.');
            window.location.href = '<?= SITE_URL ?>/sites/<?= $customer['website_folder'] ?>/';
        }
    </script>
</body>
</html>
