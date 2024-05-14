<?php
session_start();

require './database/db.php'; // Ensure the file is loaded

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // retrieve the form data by using the element's name attributes value as key
    $username = $_POST['username'];
    $password = $_POST['password']; // Consider hashing the password before storing it
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // prepare and bind
    $stmt = $conn->prepare("INSERT INTO Admin (username, password, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, password_hash($password, PASSWORD_DEFAULT), $email, $phone);

    // execute and check errors
    if ($stmt->execute()) {
        $_SESSION['message'] = "New admin registered successfully!";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header('Location: register_admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../css/teacher_login.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 400px;
            margin-top: 50px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        h4 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include './includes/header.php'; ?>
<div class="container">
    <h4>Register Admin</h4>
    <?php if (isset($_SESSION['message'])): ?>
        <p><?= $_SESSION['message']; ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form action="register_admin.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <button type="submit" class="btn btn-warning text-dark" name="submit">Register</button>
    </form>
</div>

<?php include('./includes/footer.php') ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
