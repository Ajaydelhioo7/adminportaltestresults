<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php"); // Redirect to login page if not logged in
    exit;
}

include './database/db.php'; // Database connection

// Fetch current admin details
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM Admin WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Retrieve form data
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $admin['password'];

        // Update admin details
        $updateStmt = $conn->prepare("UPDATE Admin SET username=?, email=?, phone=?, password=? WHERE id=?");
        $updateStmt->bind_param("ssssi", $username, $email, $phone, $password, $admin_id);
        if ($updateStmt->execute()) {
            echo "<script>alert('Profile updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating profile: " . $updateStmt->error . "');</script>";
        }
        $updateStmt->close();
    } elseif (isset($_POST['logout'])) {
        // Logout functionality
        session_destroy();
        header("Location: login_admin.php");
        exit;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
<?php include './includes/header.php'; ?>

<div class="container">
    <h2>Edit Profile</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['phone']); ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary" name="update">Update Profile</button>
        <button type="submit" class="btn btn-danger" name="logout">Logout</button>
    </form>
</div>

<?php include './includes/footer.php'; ?>
</body>
</html>
