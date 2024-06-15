<?php
session_start();
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: login.php");
    exit;
}

include('con.php');

$lecturer_id = $_SESSION['lecturer_id'];
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $lecturer_id);
$stmt->execute();
$lecturer = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="w-full max-w-4xl bg-white p-8 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Lecturer Dashboard</h1>
            <a href="index.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
        </div>
        <div class="space-y-4">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h2 class="text-xl font-semibold">Lecturer Details</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($lecturer['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($lecturer['email']); ?></p>
                <!-- Additional details can be added here -->
            </div>
            <!-- Add more dashboard sections here -->
        </div>
    </div>
</body>
</html>
