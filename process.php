<?php
if ($_FILES['fileInput']['error'] === 0) {
    $uploadDir = 'uploads/';

    $folderName = generateUniqueFolderName();
    $folderPath = $uploadDir . $folderName;

    mkdir($folderPath);

    foreach ($_FILES['fileInput']['name'] as $index => $filename) {
        $destination = $folderPath . '/' . $filename;
        move_uploaded_file($_FILES['fileInput']['tmp_name'][$index], $destination);
    }

    $zipPath = $uploadDir . $folderName . '.zip';
    createZipArchive($folderPath, $zipPath);

    deleteDirectory($folderPath);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipPath) . '"');
    readfile($zipPath);

    unlink($zipPath);
}

function generateUniqueFolderName() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';
    $randomChars = substr(str_shuffle($characters), 0, 4);
    $randomDigits = substr(str_shuffle($digits), 0, 2);
    return $randomChars . $randomDigits;
}

function createZipArchive($sourceFolder, $zipPath) {
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceFolder));
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getPathName();
                $relativePath = substr($filePath, strlen($sourceFolder) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
    }
}

function deleteDirectory($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (is_dir($dir . '/' . $object)) {
                    deleteDirectory($dir . '/' . $object);
                } else {
                    unlink($dir . '/' . $object);
                }
            }
        }
        rmdir($dir);
    }
}
?>
