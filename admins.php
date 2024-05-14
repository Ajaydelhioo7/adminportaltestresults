<?php
session_start();

require './database/db.php'; // Ensure the file is loaded

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

// Handle Update, Delete, or Add action
if (isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = "CSRF token mismatch.";
        header('Location: admins.php');
        exit();
    }

    $admin_id = $_POST['admin_id'];
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM Admin WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Admin deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting admin: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'update') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE Admin SET username = ?, password = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $hashed_password, $email, $phone, $admin_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Admin updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating admin: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'add') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Admin (username, password, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $phone);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Admin added successfully!";
        } else {
            $_SESSION['message'] = "Error adding admin: " . $stmt->error;
        }
        $stmt->close();
    }
    header('Location: admins.php');
    exit();
}

// Fetch all admins to display
$stmt = $conn->prepare("SELECT * FROM Admin");
$stmt->execute();
$admins_result = $stmt->get_result();

// Generate a new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admins</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include './includes/header.php'; ?>
<?php if (isset($_SESSION['message'])): ?>
    <p><?= $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="container mt-5">
    <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#newAdminModal">Add New Admin</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Password</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($admin = $admins_result->fetch_assoc()): ?>
            <tr>
                <form action="admins.php" method="post">
                    <td><?= htmlspecialchars($admin['id']); ?></td>
                    <td><input type="text" name="username" value="<?= htmlspecialchars($admin['username']); ?>" class="form-control"></td>
                    <td><input type="password" name="password" value="" placeholder="New password" class="form-control"></td>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" class="form-control"></td>
                    <td><input type="text" name="phone" value="<?= htmlspecialchars($admin['phone']); ?>" class="form-control"></td>
                    <td>
                        <input type="hidden" name="admin_id" value="<?= $admin['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="action" value="update" class="btn btn-primary">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this admin?');">Delete</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- New Admin Modal -->
<div class="modal fade" id="newAdminModal" tabindex="-1" aria-labelledby="newAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAdminModalLabel">Add New Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="admins.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                    <button type="submit" name="action" value="add" class="btn btn-success">Add Admin</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include './includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
