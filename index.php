<?php
include('con.php');

// Initialize error message
$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user-type'];

    if ($user_type ==='Admin' && empty($email) || empty($password)) {
        $error_message = "Please fill in all required fields.";
    } else {
        $error_message = "";
        // Prepare a query to check user credentials
        $sql = "SELECT * FROM users WHERE email = :email AND password = :password AND user_type = :user_type";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':user_type', $user_type);
        $stmt->execute();

        // Check if login is successful
        if ($stmt->rowCount() > 0) {
            // Login successful, handle it (e.g., redirect to a secure page)
            echo "Login successful!";
            // Example: Redirect to a welcome page
            if($user_type ==='Admin'){
                header("Location: admin_dashboard.php");
            }else{
                header("Location: lecturer_dashboard.php");
            }    
            exit;
        } else {
            // Login failed, handle it (e.g., display an error message)
            $error_message = "Invalid email, password, or user type!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="js/face-api.min.js"></script>
    <script>
        function handleUserTypeChange() {
            var userType = document.getElementById('user-type').value;
            var emailInput = document.getElementById('email');
            var passwordInput = document.getElementById('password');
            var faceLoginDiv = document.getElementById('face-login');

            if (userType === 'Lecturer') {
                emailInput.style.display = 'none';
                passwordInput.style.display = 'none';
                faceLoginDiv.style.display = 'block';
            } else {
                emailInput.style.display = 'block';
                passwordInput.style.display = 'block';
                faceLoginDiv.style.display = 'none';
            }
        }

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
                document.getElementById('login').submit(); // Automatically submit form after face capture
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
</head>

<body>
    <div class="h-screen w-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="w-full max-w-sm bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Welcome!!</h1>
            <h4 class="text-xl mb-4 text-center">Login</h4>
            <?php
                if (!empty($error_message)) {
                    echo '<p class="text-red-500 text-center">' . $error_message . '</p>';
                }
            ?>
            <form id="login" class="space-y-4" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div>
                    <label for="user-type" class="block text-sm font-medium text-gray-700">User type *</label>
                    <select id="user-type" name="user-type" onchange="handleUserTypeChange()"
                        class="w-full px-3 py-2 mt-1 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Admin">Admin</option>
                        <option value="Lecturer">Lecturer</option>
                    </select>
                </div>
                <div id="emailField">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" id="email" name="email" placeholder="example@gmail.com"
                        class="w-full px-3 py-2 mt-1 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div id="passwordField">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" id="password" name="password" placeholder="password"
                        class="w-full px-3 py-2 mt-1 rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div id="face-login" style="display: none;">
                    <p class="text-sm text-gray-700">Lecturers should use face login.</p>
                    <video id="video" width="320" height="240" autoplay muted class="rounded-md border border-gray-300"></video>
                    <input type="hidden" id="face_encoding" name="face_encoding" required>
                    <button type="button" onclick="captureFace()" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Capture Face</button>
                </div>
                <div class="flex justify-center">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
