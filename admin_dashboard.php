<?php
include('con.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $face_encoding = $_POST['face_encoding'];
    $user_type = 'Lecturer';

    $sql = "INSERT INTO users (name, email, password, face_encoding, user_type) VALUES (:name, :email, :password, :face_encoding, :user_type)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':face_encoding', $face_encoding);
    $stmt->bindParam(':user_type', $user_type);

    if ($stmt->execute()) {
        $success_message = "Lecturer enrolled successfully!";
    } else {
        $error_message = "Failed to enroll lecturer!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="js/face-api.min.js"></script>
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-500 text-white p-4">
            <h1 class="text-xl font-bold">Admin Dashboard</h1>
        </header>

        <main class="flex-grow p-6">
            <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-6 text-center">Enroll Lecturer</h2>
                
                <?php
                if (!empty($success_message)) {
                    echo '<p class="text-green-500 text-center">' . $success_message . '</p>';
                }
                if (!empty($error_message)) {
                    echo '<p class="text-red-500 text-center">' . $error_message . '</p>';
                }
                ?>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-3 py-2 mt-1 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-3 py-2 mt-1 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-3 py-2 mt-1 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="face_encoding" class="block text-sm font-medium text-gray-700">Face Encoding *</label>
                        <input type="hidden" id="face_encoding" name="face_encoding" required>
                        <video id="video" width="720" height="560" autoplay muted class="rounded-md border border-gray-300"></video>
                        <button type="button" onclick="captureFace()" class="inline-flex items-center px-4 py-2 mt-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Capture Face</button>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Enroll Lecturer
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <footer class="bg-gray-800 text-white p-4 text-center">
            <p>&copy; 2024 FacialApp. All rights reserved.</p>
        </footer>
    </div>

    <script>
        async function setupCamera() {
            try {
                const video = document.getElementById('video');
                const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();
                };
            } catch (err) {
                console.error("Error accessing the camera: ", err);
            }
        }

        async function captureFace() {
            const video = document.getElementById('video');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const faceDescriptor = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
            if (faceDescriptor) {
                document.getElementById('face_encoding').value = JSON.stringify(faceDescriptor.descriptor);
                alert('Face captured successfully!');
            } else {
                alert('No face detected. Please try again.');
            }
        }

        window.onload = async () => {
            await faceapi.nets.tinyFaceDetector.loadFromUri('models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('models');
            setupCamera();
        }
    </script>
</body>
</html>
