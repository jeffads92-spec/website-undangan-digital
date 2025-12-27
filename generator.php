<?php
// generator.php - Generate website dari upload section
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Process order data
    $order_data = [
        'customer_name' => sanitize($_POST['customer_name']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'website_folder' => sanitize($_POST['website_folder']),
        'groom_name' => sanitize($_POST['groom_name']),
        'bride_name' => sanitize($_POST['bride_name']),
        'wedding_date' => $_POST['wedding_date'],
        'location_name' => sanitize($_POST['location_name']),
        'location_address' => sanitize($_POST['location_address']),
        'package_type' => sanitize($_POST['package'] ?? 'basic')
    ];
    
    // 2. Create customer in database
    $password = generateRandomString(8);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $admin_token = bin2hex(random_bytes(16));
    
    $stmt = $pdo->prepare("INSERT INTO customers 
        (website_folder, customer_name, email, phone, groom_name, bride_name, 
         wedding_date, location_name, location_address, package_type, 
         payment_status, password_hash, admin_token) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'paid', ?, ?)");
    
    $stmt->execute([
        $order_data['website_folder'],
        $order_data['customer_name'],
        $order_data['email'],
        $order_data['phone'],
        $order_data['groom_name'],
        $order_data['bride_name'],
        $order_data['wedding_date'],
        $order_data['location_name'],
        $order_data['location_address'],
        $order_data['package_type'],
        $hashed_password,
        $admin_token
    ]);
    
    $customer_id = $pdo->lastInsertId();
    
    // 3. Create website directory
    $website_path = SITES_PATH . $order_data['website_folder'] . '/';
    mkdir($website_path, 0755, true);
    mkdir($website_path . 'admin/', 0755, true);
    mkdir($website_path . 'css/', 0755, true);
    mkdir($website_path . 'js/', 0755, true);
    mkdir($website_path . 'images/', 0755, true);
    mkdir($website_path . 'images/sections/', 0755, true);
    mkdir($website_path . 'images/gallery/', 0755, true);
    mkdir($website_path . 'data/', 0755, true);
    
    // 4. Copy template files
    copy(TEMPLATES_PATH . 'main.php', $website_path . 'index.php');
    copy('assets/templates/admin-template.php', $website_path . 'admin/index.php');
    
    // 5. Create website configuration
    $config_content = "<?php\n";
    // Include config utama dari root (naik 2 level: sites/folder/config.php -> root/config.php)
    $config_content .= "require_once '../../config.php';\n\n"; 
    $config_content .= "define('WEBSITE_ID', {$customer_id});\n";
    $config_content .= "define('WEBSITE_FOLDER', '{$order_data['website_folder']}');\n";
    // ... sisa define lainnya biarkan sama ...
    $config_content .= "?>";
    
    file_put_contents($website_path . 'config.php', $config_content);
    
    // 6. Create .htaccess for the website
    $htaccess_content = "RewriteEngine On\n";
    $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
    $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
    $htaccess_content .= "RewriteRule ^(.*)$ index.php [QSA,L]\n";
    file_put_contents($website_path . '.htaccess', $htaccess_content);
    
    // 7. Send email to customer with upload link
    $upload_link = SITE_URL . "/upload.php?token=" . $admin_token;
    $admin_link = SITE_URL . "/sites/{$order_data['website_folder']}/admin/";
    $website_link = SITE_URL . "/sites/{$order_data['website_folder']}/";
    
    // 8. Redirect to upload page
    $_SESSION['upload_token'] = $admin_token;
    $_SESSION['website_folder'] = $order_data['website_folder'];
    
    header("Location: upload.php?token={$admin_token}");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
