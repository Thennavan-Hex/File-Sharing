<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">FileShare</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarRightAlignExample"
                aria-controls="navbarRightAlignExample"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarRightAlignExample">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Uploads</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Downloads</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
            $uploadDir = "uploads/";
            $zipDir = "zip_files/";
            
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
        <form action="index.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="zipId" class="form-label">Enter Unique ID eg.ABDC12</label>
                <input type="text" class="form-control" name="zipId" id="zipId" required>
            </div>
            <div class="mb-3">
                <label for="formFileMultiple" class="form-label">Upload multiple files</label>
                <input class="form-control" type="file" name="fileToUpload[]" id="formFileMultiple" multiple />
            </div>
            <button type="submit" class="btn btn-primary mt-2">Upload and Store</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua5zL3F6tN0a4MPKJeH4A6BfJugfIVa17/pk5JX6p6ipNVG9iE987xyD" crossorigin="anonymous"></script>
    <script>
        const fileInput = document.getElementById('formFileMultiple');
        const selectedFilesDiv = document.getElementById('selectedFiles');

        fileInput.addEventListener('change', function() {
            selectedFilesDiv.innerHTML = '';

            for (const file of fileInput.files) {
                const fileName = document.createElement('p');
                fileName.textContent = file.name;
                selectedFilesDiv.appendChild(fileName);
            }
        });
    </script>

</body>
</html>
