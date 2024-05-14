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
        header('Location: studentslist.php');
        exit();
    }

    $student_id = $_POST['student_id'];
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM Students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Student deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting student: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'update') {
        $rollno = $_POST['rollno'];
        $name = $_POST['name'];
        $batch = $_POST['batch'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $stmt = $conn->prepare("UPDATE Students SET rollno = ?, name = ?, batch = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $rollno, $name, $batch, $email, $phone, $student_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Student updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating student: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'add') {
        $rollno = $_POST['rollno'];
        $name = $_POST['name'];
        $batch = $_POST['batch'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $stmt = $conn->prepare("INSERT INTO Students (rollno, name, batch, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $rollno, $name, $batch, $email, $phone);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Student added successfully!";
        } else {
            $_SESSION['message'] = "Error adding student: " . $stmt->error;
        }
        $stmt->close();
    }
    header('Location: studentslist.php');
    exit();
}

// Fetch all students to display
$stmt = $conn->prepare("SELECT * FROM Students");
$stmt->execute();
$students_result = $stmt->get_result();

// Generate a new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Students</title>
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
    <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#newStudentModal">Add New Student</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Roll No</th>
                <th>Name</th>
                <th>Batch</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $students_result->fetch_assoc()): ?>
            <tr>
                <form action="studentslist.php" method="post">
                    <td><?= htmlspecialchars($student['id']); ?></td>
                    <td><input type="text" name="rollno" value="<?= htmlspecialchars($student['rollno']); ?>" class="form-control"></td>
                    <td><input type="text" name="name" value="<?= htmlspecialchars($student['name']); ?>" class="form-control"></td>
                    <td><input type="text" name="batch" value="<?= htmlspecialchars($student['batch']); ?>" class="form-control"></td>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" class="form-control"></td>
                    <td><input type="text" name="phone" value="<?= htmlspecialchars($student['phone']); ?>" class="form-control"></td>
                    <td>
                        <input type="hidden" name="student_id" value="<?= $student['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="action" value="update" class="btn btn-primary">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- New Student Modal -->
<div class="modal fade" id="newStudentModal" tabindex="-1" aria-labelledby="newStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newStudentModalLabel">Add New Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="studentslist.php" method="post">
                    <div class="form-group">
                        <label for="rollno">Roll No</label>
                        <input type="text" class="form-control" name="rollno" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="batch">Batch</label>
                        <input type="text" class="form-control" name="batch" required>
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
                    <button type="submit" name="action" value="add" class="btn btn-success">Add Student</button>
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
