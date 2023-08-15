<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>File Upload</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .selected-file {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      padding: 5px;
      margin: 5px 0;
      border-radius: 4px;
      display: flex;
      align-items: center;
    }
    .selected-file span {
      margin-right: 10px;
    }
    .remove-link {
      color: #dc3545;
      cursor: pointer;
      margin-right: 10px;
    }
    .remove-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h2 class="my-4 text-center">Upload Your Files</h2>
        <form action="process.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="fileInput" class="form-label">Select Files</label>
            <input type="file" class="form-control" id="fileInput" name="fileInput" multiple>
            <span id="fileCount" class="ms-2"></span>
          </div>
          <div id="selectedFiles" class="mb-3"></div>
          <div id="totalSize" class="mt-3"></div>
          <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Upload</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
     const maxFileSize = 2 * 1024 * 1024 * 1024;
    const fileInput = document.getElementById("fileInput");
    const fileCountDisplay = document.getElementById("fileCount");
    const selectedFilesContainer = document.getElementById("selectedFiles");
    const totalSizeDisplay = document.getElementById("totalSize");
    let totalFileSize = 0;

    fileInput.addEventListener("change", function(event) {
      selectedFilesContainer.innerHTML = "";
      totalFileSize = 0;

      for (const file of event.target.files) {
        if (file.size <= maxFileSize) {
          const fileWrapper = document.createElement("div");
          fileWrapper.classList.add("selected-file");
          fileWrapper.dataset.fileSize = file.size;
          const fileName = document.createElement("span");
          fileName.textContent = file.name;
          const removeLink = document.createElement("a");
          removeLink.href = "#";
          removeLink.textContent = "Remove";
          removeLink.classList.add("remove-link");
          removeLink.addEventListener("click", function() {
            totalFileSize -= file.size;
            fileWrapper.remove();
            updateFileCount();
            updateTotalSize();
          });
          fileWrapper.appendChild(fileName);
          fileWrapper.appendChild(removeLink);
          selectedFilesContainer.appendChild(fileWrapper);
          totalFileSize += file.size;
        } else {
          alert(`File ${file.name} is too large. Maximum file size is 2GB.`);
          fileInput.value = null;
        }
      }
      updateFileCount();
      updateTotalSize();
    });

    function updateTotalSize() {
      const formattedTotalSize = formatBytes(totalFileSize);
      totalSizeDisplay.textContent = `Total File Size: ${formattedTotalSize}`;
    }

    function formatBytes(bytes, decimals = 2) {
      if (bytes === 0) return "0 Bytes";

      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

      const i = Math.floor(Math.log(bytes) / Math.log(k));

      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
    }

    function updateFileCount() {
      const fileWrappers = selectedFilesContainer.getElementsByClassName("selected-file");
      const count = fileWrappers.length;
      fileCountDisplay.textContent = count === 0
        ? ""
        : `${count} file${count !== 1 ? 's' : ''} selected.`;
    }
  </script>
</body>
</html>