<?php
    session_start();
    if(!isset($_SESSION["csrf_token"])){
        $_SESSION["csrf_token"] = bin2hex(random_bytes(16));
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Registration</title>
        <link rel="stylesheet" href="../assets/loginstyle.css">
    </head>
    <body class="page-body">
        <h2 class="main-heading">Register Admin / Moderator</h2>

        <?php if (isset($_SESSION["flash_msg"])) { ?>
            <p class="message"><?php echo htmlspecialchars($_SESSION["flash_msg"]); ?></p>
            <?php unset($_SESSION["flash_msg"]); ?>
        <?php } ?>

        <form id="registerForm" class="form-container" action="../controllers/controlRegister.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
            <input type="text" id="name" name="name" class="input-field" placeholder="Name"><br><br>
            <input type="email" id="email" name="email" class="input-field" placeholder="Email"><br><br>
            <input type="password" id="password" name="password" class="input-field" placeholder="Password"><br><br>
            <input type="password" id="confirm_password" name="confirm_password" class="input-field" placeholder="Confirm Password"><br><br>

            <select id="role" name="role" class="input-field">
                <option value="">Choose Role</option>
                <option value="admin">Admin</option>
                <option value="moderator">Moderator</option>
            </select><br><br>

            <button type="submit" class="submit-btn">Register</button>
        </form>

        <p class="text-container"><a href="viewLogin.php" class="nav-link">Login</a></p>
        <p class="text-container"><a href="../controllers/controlHome.php" class="nav-link">Home</a></p>

        <script>
        document.getElementById("registerForm").onsubmit = function() {
            var name = document.getElementById("name").value;
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;
            var confirm_password = document.getElementById("confirm_password").value;
            var role = document.getElementById("role").value;

            if (name == "" || email == "" || password == "" || confirm_password == "" || role == "") {
                alert("Please fill all fields");
                return false;
            }
            if (password.length < 8) {
                alert("Password must be at least 8 characters long");
                return false;
            }
            if (password != confirm_password) {
                alert("Passwords do not match");
                return false;
            }
            return true;
        };
        </script>
    </body>
</html>
