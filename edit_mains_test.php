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
        header('Location: edit_mains_test.php');
        exit();
    }

    $test_id = $_POST['test_id'];
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM create_mains_test WHERE id = ?");
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
        $maxmarks = $_POST['maxmarks'];
        $stmt = $conn->prepare("UPDATE create_mains_test SET testname = ?, batch = ?, maxmarks = ? WHERE id = ?");
        $stmt->bind_param("ssii", $testname, $batch, $maxmarks, $test_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Test updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating test: " . $stmt->error;
        }
        $stmt->close();
    }
    header('Location: edit_mains_test.php');
    exit();
}

// Fetch all mains tests to display
$stmt = $conn->prepare("SELECT * FROM create_mains_test");
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
    <title>Edit Mains Tests</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include './includes/header.php'; ?>
<?php if (isset($_SESSION['message'])): ?>
    <p><?= $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="container mt-5">
    <h4>Edit Mains Tests</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Test Name</th>
                <th>Batch</th>
                <th>Max Marks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($test = $tests_result->fetch_assoc()): ?>
            <tr>
                <form action="edit_mains_test.php" method="post">
                    <td><?= htmlspecialchars($test['id']); ?></td>
                    <td><input type="text" name="testname" value="<?= htmlspecialchars($test['testname']); ?>" class="form-control"></td>
                    <td><input type="text" name="batch" value="<?= htmlspecialchars($test['batch']); ?>" class="form-control"></td>
                    <td><input type="number" name="maxmarks" value="<?= htmlspecialchars($test['maxmarks']); ?>" class="form-control"></td>
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
