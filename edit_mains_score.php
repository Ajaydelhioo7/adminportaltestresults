<?php
session_start();

require './database/db.php'; // Ensure the file is loaded

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

// Handle Update or Delete action
if (isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = "CSRF token mismatch.";
        header('Location: edit_mains_score.php');
        exit();
    }

    $test_id = $_POST['test_id'];
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM mains_test_score WHERE id = ?");
        $stmt->bind_param("i", $test_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Score deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting score: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'update') {
        $testname = $_POST['testname'];
        $batch = $_POST['batch'];
        $max_marks = $_POST['max_marks'];
        $marks_obtained = $_POST['marks_obtained'];
        $percentage = $_POST['percentage'];
        $stmt = $conn->prepare("UPDATE mains_test_score SET testname = ?, batch = ?, max_marks = ?, marks_obtained = ?, percentage = ? WHERE id = ?");
        $stmt->bind_param("ssiddi", $testname, $batch, $max_marks, $marks_obtained, $percentage, $test_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Score updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating score: " . $stmt->error;
        }
        $stmt->close();
    }
    header('Location: edit_mains_score.php');
    exit();
}

// Handle Search
$rollno = '';
$scores_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $rollno = trim($_POST['rollno']);
    $stmt = $conn->prepare("SELECT * FROM mains_test_score WHERE rollno = ?");
    if ($stmt) {
        $stmt->bind_param("s", $rollno);
        $stmt->execute();
        $scores_result = $stmt->get_result();
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error preparing fetch statement: " . $conn->error;
    }
}

// Generate a new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Scores</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include './includes/header.php'; ?>
<?php if (isset($_SESSION['message'])): ?>
    <p><?= $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
<div class="container mt-5">
    <h4>Edit Mains Scores</h4>
    <form method="post">
        <input type="text" name="rollno" placeholder="Enter roll number" value="<?= htmlspecialchars($rollno); ?>" required>
        <button type="submit" name="search">Search</button>
    </form>
    <?php if ($scores_result && $scores_result->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Test Name</th>
                <th>Batch</th>
                <th>Max Marks</th>
                <th>Marks Obtained</th>
                <th>Percentage</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($score = $scores_result->fetch_assoc()): ?>
            <tr>
                <form action="edit_mains_score.php" method="post">
                    <td><?= htmlspecialchars($score['id']); ?></td>
                    <td><input type="text" name="testname" value="<?= htmlspecialchars($score['testname']); ?>" class="form-control"></td>
                    <td><input type="text" name="batch" value="<?= htmlspecialchars($score['batch']); ?>" class="form-control"></td>
                    <td><input type="number" name="max_marks" value="<?= htmlspecialchars($score['max_marks']); ?>" class="form-control"></td>
                    <td><input type="number" name="marks_obtained" value="<?= htmlspecialchars($score['marks_obtained']); ?>" class="form-control"></td>
                    <td><input type="number" name="percentage" value="<?= htmlspecialchars($score['percentage']); ?>" class="form-control" step="0.01"></td>
                    <td>
                        <input type="hidden" name="test_id" value="<?= $score['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="action" value="update" class="btn btn-primary">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this score?');">Delete</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php include './includes/footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
