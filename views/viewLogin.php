<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
    </head>
    <body>
        <h2>Sign In</h2>
        <?php if(isset($_SESSION['flash_msg'])):?>
        <p style="color:red;"><?=htmlspecialchars($_SESSION['flash_msg']);?></p>
        <?php unset($_SESSION['flash_msg']); ?>
        <?php endif;?>

        <form id="loginForm" action="../controlLogin.php" method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <label>
                <input type="checkbox" name="remember" value="checked">Remember me
            </label><br>
        <button type="submit">Submit</button>
        </form>
        <p>New? <a href="viewRegister.php">Register here</a></p>
    </body>
</html>