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
        header('Location: teachers.php');
        exit();
    }

    $teacher_id = $_POST['teacher_id'];
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM Teachers WHERE id = ?");
        $stmt->bind_param("i", $teacher_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Teacher deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting teacher: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'update') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE Teachers SET username = ?, password = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $hashed_password, $email, $phone, $teacher_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Teacher updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating teacher: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'add') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Teachers (username, password, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $phone);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Teacher added successfully!";
        } else {
            $_SESSION['message'] = "Error adding teacher: " . $stmt->error;
        }
        $stmt->close();
    }
    header('Location: teachers.php');
    exit();
}

// Fetch all teachers to display
$stmt = $conn->prepare("SELECT * FROM Teachers");
$stmt->execute();
$teachers_result = $stmt->get_result();

// Generate a new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teachers</title>
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
    <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#newTeacherModal">Add New Teacher</button>
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
            <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
            <tr>
                <form action="teachers.php" method="post">
                    <td><?= htmlspecialchars($teacher['id']); ?></td>
                    <td><input type="text" name="username" value="<?= htmlspecialchars($teacher['username']); ?>" class="form-control"></td>
                    <td><input type="password" name="password" value="" placeholder="New password" class="form-control"></td>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($teacher['email']); ?>" class="form-control"></td>
                    <td><input type="text" name="phone" value="<?= htmlspecialchars($teacher['phone']); ?>" class="form-control"></td>
                    <td>
                        <input type="hidden" name="teacher_id" value="<?= $teacher['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="action" value="update" class="btn btn-primary">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- New Teacher Modal -->
<div class="modal fade" id="newTeacherModal" tabindex="-1" aria-labelledby="newTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newTeacherModalLabel">Add New Teacher</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="teachers.php" method="post">
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
                    <button type="submit" name="action" value="add" class="btn btn-success">Add Teacher</button>
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
