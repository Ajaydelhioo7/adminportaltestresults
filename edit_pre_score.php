<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
        header('Location: edit_pre_score.php');
        exit();
    }

    $score_id = $_POST['score_id'];
    if ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM Test_Scores WHERE id = ?");
        $stmt->bind_param("i", $score_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Score deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting score: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'update') {
        $rollno = $_POST['rollno'];
        $batch = $_POST['batch'];
        $testname = $_POST['testname'];
        $right_question = $_POST['right_question'];
        $wrong_question = $_POST['wrong_question'];
        $not_attempted = $_POST['not_attempted'];
        $max_marks = $_POST['max_marks'];
        $marks_obtained = $_POST['marks_obtained'];
        $percentage = $_POST['percentage'];
        $award_for_wrong = $_POST['award_for_wrong'];
        $award_for_right = $_POST['award_for_right'];
        $total_questions = $_POST['total_questions'];
        $stmt = $conn->prepare("UPDATE Test_Scores SET rollno = ?, batch = ?, testname = ?, right_question = ?, wrong_question = ?, not_attempted = ?, max_marks = ?, marks_obtained = ?, percentage = ?, award_for_wrong = ?, award_for_right = ?, total_questions = ? WHERE id = ?");
        $stmt->bind_param("sssiiiiidiiii", $rollno, $batch, $testname, $right_question, $wrong_question, $not_attempted, $max_marks, $marks_obtained, $percentage, $award_for_wrong, $award_for_right, $total_questions, $score_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Score updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating score: " . $stmt->error;
        }
        $stmt->close();
    }
    header('Location: edit_pre_score.php');
    exit();
}

// Handle Search
$rollno = '';
$scores_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $rollno = trim($_POST['rollno']);
    $stmt = $conn->prepare("SELECT * FROM Test_Scores WHERE rollno = ?");
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
    <title>Edit Pre Scores</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include './includes/header.php'; ?>
<?php if (isset($_SESSION['message'])): ?>
    <p><?= $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
<div class="container mt-5">
    <h4>Edit Pre Scores</h4>
    <form method="post">
        <input type="text" name="rollno" placeholder="Enter roll number" value="<?= htmlspecialchars($rollno); ?>" required>
        <button type="submit" name="search">Search</button>
    </form>
    <?php if ($scores_result && $scores_result->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Roll No</th>
                <th>Batch</th>
                <th>Test Name</th>
                <th>Right Questions</th>
                <th>Wrong Questions</th>
                <th>Not Attempted</th>
                <th>Max Marks</th>
                <th>Marks Obtained</th>
                <th>Percentage</th>
                <th>Award for Wrong</th>
                <th>Award for Right</th>
                <th>Total Questions</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($score = $scores_result->fetch_assoc()): ?>
            <tr>
                <form action="edit_pre_score.php" method="post">
                    <td><?= htmlspecialchars($score['id']); ?></td>
                    <td><input type="text" name="rollno" value="<?= htmlspecialchars($score['rollno']); ?>" class="form-control"></td>
                    <td><input type="text" name="batch" value="<?= htmlspecialchars($score['batch']); ?>" class="form-control"></td>
                    <td><input type="text" name="testname" value="<?= htmlspecialchars($score['testname']); ?>" class="form-control"></td>
                    <td><input type="number" name="right_question" value="<?= htmlspecialchars($score['right_question']); ?>" class="form-control"></td>
                    <td><input type="number" name="wrong_question" value="<?= htmlspecialchars($score['wrong_question']); ?>" class="form-control"></td>
                    <td><input type="number" name="not_attempted" value="<?= htmlspecialchars($score['not_attempted']); ?>" class="form-control"></td>
                    <td><input type="number" name="max_marks" value="<?= htmlspecialchars($score['max_marks']); ?>" class="form-control"></td>
                    <td><input type="number" name="marks_obtained" value="<?= htmlspecialchars($score['marks_obtained']); ?>" class="form-control"></td>
                    <td><input type="number" name="percentage" value="<?= htmlspecialchars($score['percentage']); ?>" class="form-control" step="0.01"></td>
                    <td><input type="number" name="award_for_wrong" value="<?= htmlspecialchars($score['award_for_wrong']); ?>" class="form-control"></td>
                    <td><input type="number" name="award_for_right" value="<?= htmlspecialchars($score['award_for_right']); ?>" class="form-control"></td>
                    <td><input type="number" name="total_questions" value="<?= htmlspecialchars($score['total_questions']); ?>" class="form-control"></td>
                    <td>
                        <input type="hidden" name="score_id" value="<?= $score['id']; ?>">
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
