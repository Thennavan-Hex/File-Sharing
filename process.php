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
function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['fileInput']['name']) && is_array($_FILES['fileInput']['name'])) {
        $uniqueId = generateRandomString(4) . rand(10, 99);
        $uploadPath = 'uploads/' . $uniqueId . '/';

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $allowedExtensions = ['py', 'php', 'java'];
                $stmt = $pdo->prepare("INSERT INTO uploads_folders (unique_id) VALUES (?)");
        $stmt->execute([$uniqueId]);

        for ($i = 0; $i < count($_FILES['fileInput']['name']); $i++) {
            $fileName = $_FILES['fileInput']['name'][$i];
            $fileSize = $_FILES['fileInput']['size'][$i];
            $fileType = $_FILES['fileInput']['type'][$i];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (in_array($extension, $allowedExtensions)) {
                $filePath = $uploadPath . basename($fileName) . '.txt';
            } else {
                $filePath = $uploadPath . basename($fileName);
            }
            if (move_uploaded_file($_FILES['fileInput']['tmp_name'][$i], $filePath)) {
                $stmt = $pdo->prepare("INSERT INTO uploads (unique_id, file_name, file_size, file_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$uniqueId, $fileName, $fileSize, $filePath]);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Upload Process</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
        <h2 class="my-4">Uploaded Folders</h2>
        <div class="row">
            <?php
            $stmt = $pdo->query("SELECT unique_id, SUM(file_size) AS total_size FROM uploads GROUP BY unique_id");
            $uploadedFolders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($uploadedFolders as $folder): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Folder ID: <?php echo $folder['unique_id']; ?></h5>
                            <p class="card-text">Total Size: <?php echo formatBytes($folder['total_size']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
</html>

<?php
function formatBytes($bytes, $decimals = 2) {
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
}
?>
