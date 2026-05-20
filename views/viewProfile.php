<?php
    include __DIR__ . "/../controllers/controlProfile.php";

    $pic_src = "../assets/defaultprofilepic.png";
    if (!empty($userData["profilePic"]) && file_exists(__DIR__ . "/../public/uploads/" . $userData["profilePic"])) {
        $pic_src = "../public/uploads/" . $userData["profilePic"];
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Profile</title>
        <link rel="stylesheet" href="../assets/loginstyle.css">
    </head>
    <body class="page-body">
        <h2 class="main-heading">Profile</h2>

        <?php if (isset($_SESSION["flash_msg"])) { ?>
            <p class="message"><?php echo htmlspecialchars($_SESSION["flash_msg"]); ?></p>
            <?php unset($_SESSION["flash_msg"]); ?>
        <?php } ?>

        <img src="<?php echo $pic_src; ?>" class="profile-avatar" alt="Profile Picture" width="120" height="120"><br><br>

        <form class="form-container" action="../controllers/controlProfile.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
            <input type="hidden" name="action" value="upload_picture">
            <input type="file" name="profilePic" class="input-field"><br><br>
            <button type="submit" class="submit-btn">Upload Picture</button>
        </form><br>

        <form id="profileForm" class="form-container" action="../controllers/controlProfile.php" method="POST">
            <h2 class="main-heading" style="font-size: 20px; margin-bottom: 16px;">Update Info</h2>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
            <input type="hidden" name="action" value="update_info">
            <input type="text" name="name" id="name" class="input-field" value="<?php echo htmlspecialchars($userData["name"]); ?>"><br><br>
            <input type="email" name="email" id="email" class="input-field" value="<?php echo htmlspecialchars($userData["email"]); ?>"><br><br>
            <button type="submit" class="submit-btn">Update</button>
        </form><br>

        <form id="passwordForm" class="form-container" action="../controllers/controlProfile.php" method="POST">
            <h2 class="main-heading" style="font-size: 20px; margin-bottom: 16px;">Change Password</h2>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
            <input type="hidden" name="action" value="change_password">
            <input type="password" name="current_password" class="input-field" placeholder="Current Password"><br><br>
            <input type="password" name="new_password" id="new_password" class="input-field" placeholder="New Password"><br><br>
            <input type="password" name="confirm_password" id="confirm_password" class="input-field" placeholder="Confirm Password"><br><br>
            <button type="submit" class="submit-btn">Change Password</button>
        </form>

        <p class="text-container">
            <a href="../controllers/controlHome.php" class="nav-link">Home</a> &nbsp;|&nbsp;
            <a href="../logout.php" class="nav-link">Logout</a>
        </p>

        <script>
        document.getElementById("profileForm").onsubmit = function() {
            if (document.getElementById("name").value == "" || document.getElementById("email").value == ""){
                alert("Name and email are required");
                return false;
            }
            return true;
        };

        document.getElementById("passwordForm").onsubmit = function() {
            var newPW = document.getElementById("new_password").value;
            var confirmPW = document.getElementById("confirm_password").value;

            if (newPW.length<8) {
                alert("New password must be at least 8 characters long");
                return false;
            }

            if (newPW!= confirmPW) {
                alert("Passwords do not match");
                return false;
            }
            return true;
        };
        </script>
    </body>
</html>
