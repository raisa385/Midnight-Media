<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Registration</title>
    </head>
    <body>
        <h2>Register</h2>
        <?php if(isset($_SESSION['flash_msg'])):?>
        <p style="color:red;"><?=htmlspecialchars($_SESSION['flash_msg']);?></p>
        <?php endif;?>

        <form action="/controlRegister.php" method="POST">
            <input type="text" name="name" placeholder="Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <label for="role">Choose your role</label>
            <select id="userRole" name="userRole">
                <option value="Admin">Apply for Admin Role</option>
                <option value="Moderator">Apply for Moderator Role</option>
            </select>
            <button type="submit">Submit</button>
        </form>
        <p>Already registered? <a href="viewLogin.php">Login here</a></p>
    </body>
</html>