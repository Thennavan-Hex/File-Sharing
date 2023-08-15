<?php
$host = 'localhost';
$dbname = 'file_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
$stmt = $pdo->query("SELECT * FROM uploads");
$uploadedFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Uploaded Files</title>
</head>
<body>
    <h2>View Uploaded Files</h2>
    <ul>
        <?php foreach ($uploadedFiles as $file): ?>
            <li>
                <strong>File Name:</strong> <?php echo $file['file_name']; ?> - 
                <strong>File Size:</strong> <?php echo formatBytes($file['file_size']); ?> - 
                <a href="<?php echo $file['file_path']; ?>" download>Download</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

<?php
function formatBytes($bytes, $decimals = 2) {
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
}
?>
