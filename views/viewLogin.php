<?php
session_start();
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(16));
}//cross site request forgery prevention
$rem_email= $_COOKIE["remember"] ?? ""; 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/loginStyle.css">
</head>
<body class="page-body">
    <h2 class="main-heading">Admin / Moderator Login</h2>

    <?php if (isset($_SESSION["flash_msg"])){?>
        <p class="message"><?php echo htmlspecialchars($_SESSION["flash_msg"]); ?></p>
        <?php unset($_SESSION["flash_msg"]); ?>
    <?php } ?>

    <form id="loginForm" class="form-container" action="../controllers/controlLogin.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
        <input type="email" name="email" id="email" class="input-field" placeholder="Email" value="<?php echo htmlspecialchars($rem_email); ?>"><br><br>
        <input type="password" name="password" id="password" class="input-field" placeholder="Password"><br><br>
        <label class="checkbox-label"><input type="checkbox" name="remember" class="checkbox-input" value="1"> Remember Me</label><br><br>
        <button type="submit" class="submit-btn">Login</button>
    </form>

    <p class="text-container"><a href="viewRegister.php" class="nav-link">Register Admin/Moderator</a></p>
    <p class="text-container"><a href="../controllers/controlHome.php" class="nav-link">Continue as Member</a></p>

    <script>
    document.getElementById("loginForm").onsubmit = function() {
        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;

        if (email == "" || password == "") {
            alert("Please fill all fields");
            return false;
        }
        return true;
    };
    </script>
</body>
</html>
