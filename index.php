<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<html>

<head>
    <title>Form Example</title>
    <style>
        .form-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-field {
            margin-bottom: 10px;
        }

        .form-field label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="form-wrapper">
        <h1>Registration Form</h1>
        <form id="registration-form" enctype="multipart/form-data">
            <div class="form-field">
                <label for="first-name">First Name:</label>
                <input type="hidden" id="csrf-token" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="text" id="first-name" required>
            </div>
            <div class="form-field">
                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" required>
            </div>
            <div class="form-field">
                <label for="user-image">User Image:</label>
                <input type="file" id="user-image" required accept="image/*">
                <img id="thumbnail" src="#" alt="Thumbnail" style="display: none; max-width: 200px; margin-top: 10px;">
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        // Check if the selected file is a valid image before upload
        function validateImage(file) {
            const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validImageTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, or GIF).');
                return false;
            }
            return true;
        }

        // Display thumbnail for the uploaded image
        function displayThumbnail(file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.getElementById('thumbnail');
                img.src = event.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
        document.getElementById('user-image').addEventListener('change', function(event) {
            const file = event.target.files[0];

            // Validate the image before upload
            if (file && validateImage(file)) {
                displayThumbnail(file);
            }
        });

        // Handle form submission
        document.getElementById('registration-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const firstName = document.getElementById('first-name').value;
            const lastName = document.getElementById('last-name').value;
            const csrfToken = document.getElementById('csrf-token').value;
            const userImage = document.getElementById('user-image').files[0];

            // Validate the image before upload
            if (userImage && !validateImage(userImage)) {
                return;
            }

            // Create a FormData object to send the form data
            const formData = new FormData();
            formData.append('firstName', firstName);
            formData.append('lastName', lastName);
            formData.append('userImage', userImage);
            formData.append('csrf_token', csrfToken);

            // Send the form data to the server
            fetch('/submit.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Form submitted successfully:', data);
                    alert('Form submitted successfully:');
                    // Reset the form
                    document.getElementById('registration-form').reset();
                    // Reset the thumbnail
                    document.getElementById('thumbnail').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                });
        });
    </script>
</body>

</html>