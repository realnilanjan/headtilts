<?php

use App\Helpers\Helpers;

$uploadDir = __DIR__ . '/../../public/uploads/';
$imageFiles = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    if (is_array($files)) {
        foreach ($files as $file) {
            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                $imageFiles[] = Helpers::getBaseUrl() . "/uploads/" . $file;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Media Library</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 10px;
            background: #f5f6fa;
            margin: 0;
        }

        h3 {
            margin-top: 0;
            font-size: 18px;
            color: #2c3e50;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .image-item {
            cursor: pointer;
            border: 2px solid transparent;
            transition: border 0.2s;
            text-align: center;
            background: #fff;
            padding: 5px;
            border-radius: 6px;
        }

        .image-item:hover {
            border-color: #3498db;
        }

        .image-item img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .upload-form {
            margin-bottom: 15px;
        }

        .upload-form input[type="file"] {
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <h3>Media Library</h3>

    <!-- Upload Form -->
    <form class="upload-form" id="uploadForm" enctype="multipart/form-data">
        <label for="upload">Upload New Image:</label>
        <input type="file" name="file" id="upload" accept="image/*">
    </form>

    <!-- Image Grid -->
    <div class="image-grid" id="imageGrid">
        <?php foreach ($imageFiles as $url): ?>
            <div class="image-item" onclick="selectImage('<?= htmlspecialchars($url) ?>')">
                <img src="<?= htmlspecialchars($url) ?>" alt="">
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Send selected image back to TinyMCE
        function selectImage(url) {
            window.parent.postMessage({
                type: 'tinymce-image-selected',
                url: url,
                title: url.split('/').pop()
            }, window.location.origin);
        }

        // Handle file upload
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            const fileInput = document.querySelector('input[type="file"]');
            const file = fileInput.files[0];

            if (!file) {
                alert("Please select an image to upload.");
                return;
            }

            formData.append('file', file);

            fetch('/upload-image', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.location) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'image-item';
                        imgDiv.setAttribute('onclick', `selectImage('${data.location}')`);
                        imgDiv.innerHTML = `<img src="${data.location}" alt="">`;
                        document.getElementById('imageGrid').prepend(imgDiv);
                        fileInput.value = '';
                        alert("Image uploaded successfully!");
                    } else {
                        alert("Upload failed: " + (data.error || "Unknown error"));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Upload failed.");
                });
        });
    </script>

</body>

</html>