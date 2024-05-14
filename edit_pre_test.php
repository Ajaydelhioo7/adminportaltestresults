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
        header('Location: edit_pre_test.php');
        exit();
    }

    $test_id = $_POST['test_id'];
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM tests WHERE id = ?");
        $stmt->bind_param("i", $test_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Test deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting test: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'update') {
        $testname = $_POST['testname'];
        $batch = $_POST['batch'];
        $max_marks = $_POST['max_marks'];
        $award_for_right = $_POST['award_for_right'];
        $subject = $_POST['subject'];
        $created_by = $_POST['created_by'];
        $created_at = $_POST['created_at'];
        $award_for_wrong = $_POST['award_for_wrong'];
        $total_questions = $_POST['total_questions'];
        $stmt = $conn->prepare("UPDATE tests SET testname = ?, batch = ?, max_marks = ?, award_for_right = ?, subject = ?, created_by = ?, created_at = ?, award_for_wrong = ?, total_questions = ? WHERE id = ?");
        $stmt->bind_param("ssisssssii", $testname, $batch, $max_marks, $award_for_right, $subject, $created_by, $created_at, $award_for_wrong, $total_questions, $test_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Test updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating test: " . $stmt->error;
        }
        $stmt->close();
    }
    header('Location: edit_pre_test.php');
    exit();
}

// Fetch all tests to display
$stmt = $conn->prepare("SELECT * FROM tests");
$stmt->execute();
$tests_result = $stmt->get_result();

// Generate a new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pre Tests</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include './includes/header.php'; ?>
<?php if (isset($_SESSION['message'])): ?>
    <p><?= $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="container mt-5">
    <h4>Edit Pre Tests</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Test Name</th>
                <th>Batch</th>
                <th>Max Marks</th>
                <th>Award for Right</th>
                <th>Subject</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Award for Wrong</th>
                <th>Total Questions</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($test = $tests_result->fetch_assoc()): ?>
            <tr>
                <form action="edit_pre_test.php" method="post">
                    <td><?= htmlspecialchars($test['id']); ?></td>
                    <td><input type="text" name="testname" value="<?= htmlspecialchars($test['testname']); ?>" class="form-control"></td>
                    <td><input type="text" name="batch" value="<?= htmlspecialchars($test['batch']); ?>" class="form-control"></td>
                    <td><input type="number" name="max_marks" value="<?= htmlspecialchars($test['max_marks']); ?>" class="form-control"></td>
                    <td><input type="number" name="award_for_right" value="<?= htmlspecialchars($test['award_for_right']); ?>" class="form-control"></td>
                    <td><input type="text" name="subject" value="<?= htmlspecialchars($test['subject']); ?>" class="form-control"></td>
                    <td><input type="text" name="created_by" value="<?= htmlspecialchars($test['created_by']); ?>" class="form-control"></td>
                    <td><input type="text" name="created_at" value="<?= htmlspecialchars($test['created_at']); ?>" class="form-control"></td>
                    <td><input type="number" name="award_for_wrong" value="<?= htmlspecialchars($test['award_for_wrong']); ?>" class="form-control"></td>
                    <td><input type="number" name="total_questions" value="<?= htmlspecialchars($test['total_questions']); ?>" class="form-control"></td>
                    <td>
                        <input type="hidden" name="test_id" value="<?= $test['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="action" value="update" class="btn btn-primary">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this test?');">Delete</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include './includes/footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
