<?php
/**
 * Manual PHPMailer Installation Script
 * Run this file in your browser: http://localhost/project/install-phpmailer-manual.php
 */

set_time_limit(300); // 5 minutes

$downloadUrl = 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip';
$zipFile = 'phpmailer.zip';
$extractTo = __DIR__ . '/vendor/phpmailer/phpmailer';

echo "<h2>PHPMailer Manual Installation</h2>";
echo "<pre>";

// Step 1: Download
echo "Step 1: Downloading PHPMailer...\n";
$zipContent = file_get_contents($downloadUrl);
if ($zipContent === false) {
    die("Error: Failed to download PHPMailer\n");
}
file_put_contents($zipFile, $zipContent);
echo "✓ Downloaded successfully\n\n";

// Step 2: Extract
echo "Step 2: Extracting files...\n";
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    // Create vendor directory structure
    if (!is_dir('vendor/phpmailer')) {
        mkdir('vendor/phpmailer', 0755, true);
    }
    
    $zip->extractTo('vendor/phpmailer/');
    $zip->close();
    
    // Move files from extracted folder to correct location
    $extractedFolder = 'vendor/phpmailer/PHPMailer-6.9.1';
    if (is_dir($extractedFolder)) {
        rename($extractedFolder, $extractTo);
    }
    
    echo "✓ Extracted successfully\n\n";
} else {
    die("Error: Failed to extract ZIP file\n");
}

// Step 3: Create autoload.php
echo "Step 3: Creating autoload file...\n";
if (!is_dir('vendor')) {
    mkdir('vendor', 0755, true);
}

$autoloadContent = <<<'PHP'
<?php
// PHPMailer Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'PHPMailer\\PHPMailer\\';
    $base_dir = __DIR__ . '/phpmailer/phpmailer/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});
PHP;

file_put_contents('vendor/autoload.php', $autoloadContent);
echo "✓ Autoload created\n\n";

// Step 4: Cleanup
echo "Step 4: Cleaning up...\n";
unlink($zipFile);
echo "✓ Cleanup complete\n\n";

echo "<strong style='color: green;'>✓ PHPMailer installed successfully!</strong>\n\n";
echo "Next steps:\n";
echo "1. Configure email-config.php with your Gmail App Password\n";
echo "2. Set MAIL_ENABLED = true\n";
echo "3. Test at: <a href='test-email.php'>test-email.php</a>\n";
echo "</pre>";

echo "<p><a href='test-email.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Test Email Configuration</a></p>";
?>
