<?php
/**
 * PHPMailer Installation Without ZIP Extension
 * This downloads individual files without needing ZIP
 */

set_time_limit(300);

echo "<h2>PHPMailer Installation (No ZIP Required)</h2>";
echo "<pre>";

// Create directory structure
echo "Creating directories...\n";
$dirs = [
    'vendor',
    'vendor/phpmailer',
    'vendor/phpmailer/phpmailer',
    'vendor/phpmailer/phpmailer/src'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✓ Created: $dir\n";
    }
}

echo "\nDownloading PHPMailer files...\n";

// List of files to download
$files = [
    'src/PHPMailer.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php',
    'src/SMTP.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php',
    'src/Exception.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php',
    'src/OAuth.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/OAuth.php',
    'src/POP3.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/POP3.php',
];

$success = 0;
$failed = 0;

foreach ($files as $localPath => $url) {
    $fullPath = 'vendor/phpmailer/phpmailer/' . $localPath;
    echo "Downloading: " . basename($localPath) . "... ";
    
    $content = @file_get_contents($url);
    
    if ($content !== false) {
        file_put_contents($fullPath, $content);
        echo "✓\n";
        $success++;
    } else {
        echo "✗ Failed\n";
        $failed++;
    }
}

echo "\nCreating autoloader...\n";

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
echo "✓ Autoloader created\n";

echo "\n";
echo "<strong style='color: green;'>✓ Installation Complete!</strong>\n";
echo "Downloaded: $success files\n";
if ($failed > 0) {
    echo "Failed: $failed files\n";
}

echo "\n<strong>Next Steps:</strong>\n";
echo "1. Go to: <a href='test-email.php'>test-email.php</a>\n";
echo "2. Verify PHPMailer is installed\n";
echo "3. Send a test email\n";

echo "</pre>";

echo "<p><a href='test-email.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Test Email Configuration</a></p>";
?>
