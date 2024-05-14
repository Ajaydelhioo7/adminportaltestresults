<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php"); // Redirect to login page if not logged in
    exit;
}

include './database/db.php'; // Make sure your database connection path is correct

// Fetch number of students
$studentsQuery = "SELECT COUNT(*) AS student_count FROM Students";
$studentsResult = $conn->query($studentsQuery);
$studentsCount = $studentsResult->fetch_assoc()['student_count'];

// Fetch number of teachers
$teachersQuery = "SELECT COUNT(*) AS teacher_count FROM Teachers";
$teachersResult = $conn->query($teachersQuery);
$teachersCount = $teachersResult->fetch_assoc()['teacher_count'];

// Fetch number of pre tests
$preTestsQuery = "SELECT COUNT(*) AS pre_test_count FROM tests";
$preTestsResult = $conn->query($preTestsQuery);
$preTestsCount = $preTestsResult->fetch_assoc()['pre_test_count'];

// Fetch number of mains tests
$mainsTestsQuery = "SELECT COUNT(*) AS mains_test_count FROM create_mains_test";
$mainsTestsResult = $conn->query($mainsTestsQuery);
$mainsTestsCount = $mainsTestsResult->fetch_assoc()['mains_test_count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .dashboard {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include './includes/header.php'; ?> <!-- Make sure your header path is correct -->

<div class="container dashboard">
    <h5>Welcome to Admin Dashboard</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header text-dark">Students Enrolled</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $studentsCount; ?></h5>
                    <p class="card-text">Total number of students.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-header text-dark">Teachers Enrolled</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $teachersCount; ?></h5>
                    <p class="card-text">Total number of teachers.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-info mb-3">
                <div class="card-header text-dark">Pre Tests Taken</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $preTestsCount; ?></h5>
                    <p class="card-text">Total number of pre tests taken.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header text-dark">Mains Tests Taken</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $mainsTestsCount; ?></h5>
                    <p class="card-text">Total number of mains tests taken.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './includes/footer.php'; ?> <!-- Make sure your footer path is correct -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
