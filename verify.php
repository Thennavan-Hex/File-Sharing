<?php
$uploadDir = "uploads/";
$zipDir = "zip_files/";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    $zipId = $_POST["zipId"]; 
    $zipName = $zipId . ".zip"; 
    $zipPath = $zipDir . $zipName;
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $uploadedFiles = $_FILES["fileToUpload"];
        foreach ($uploadedFiles["name"] as $key => $fileName) {
            $tempFilePath = $uploadedFiles["tmp_name"][$key];
            $zip->addFile($tempFilePath, $fileName);
        }
        $zip->close();

        $servername = "localhost";
        $username = "";
        $password = "";
        $dbname = "fileshare";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $zipFilePath = mysqli_real_escape_string($conn, $zipPath);
        $sql = "INSERT INTO files (zip_path) VALUES ('$zipFilePath')";
        if ($conn->query($sql) === TRUE) {
            echo "Files uploaded and stored in a zip archive. Zip file stored in the database.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    } else {
        echo "Failed to create zip archive.";
    }
} else {
    echo "No files uploaded.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <form action="verify.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="zipId" class="form-label">Enter Unique ID for Zip File</label>
                <input type="text" class="form-control" name="zipId" id="zipId" required>
            </div>
            <div class="mb-3">
                <label for="formFileMultiple" class="form-label">Upload multiple files</label>
                <input class="form-control" type="file" name="fileToUpload[]" id="formFileMultiple" multiple />
            </div>
            <button type="submit" class="btn btn-primary mt-2">Upload and Store</button>
        </form>
    </div>
</body>
</html>
