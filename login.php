<?php
session_start();
include 'includes/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


$error_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

  
    if (empty($email) || empty($password)) {
        $error_message = "Моля, попълнете всички полета.";
    } else {
        
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
         
            $row = $result->fetch_assoc();

           
            if (password_verify($password, $row['password'])) {
             
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

            
                header("Location: index.php");
                exit;
            } else {
                $error_message = "Грешна парола. Моля, опитайте отново.";
            }
        } else {
            $error_message = "Потребителят с този имейл не е намерен.";
        }
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Takeaway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2 class="text-center">Вход в профила</h2>

  
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

 
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Електронна поща</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Парола</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Вход</button>
        </form>

        <p class="mt-3 text-center">Нямате профил? <a href="register.php">Регистрирайте се тук</a>.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
