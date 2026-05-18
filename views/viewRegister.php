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
        <p id="JS_flash_msg" style="color:red display:none;"></p>

        <form id="registerForm" action="../controllers/controlRegister.php" method="POST">
            <input type="text" id='name' name="name" placeholder="Name" required><br>
            <input type="email" id='email'name="email" placeholder="Email" required><br>
            <input type="password" id='password' name="password" placeholder="Password" required><br>
            <label for="role">Choose your role</label>
            <select id="userRole" name="userRole">
                <option value="Admin">Apply for Admin Role</option>
                <option value="Moderator">Apply for Moderator Role</option>
            </select>
            <button type="submit">Submit</button>
        </form>
        <p>Already registered? <a href="viewLogin.php">Login here</a></p>

        <script>
            document.getElementById('registerForm').addEventListener('submit',function(event)){
                const name= document.getElementById('name').value.trim();
                const email= document.getElementById('email').value.trim();
                const password= document.getElementById('password').value.trim();
                const userRole= document.getElementById('userRole').value.trim();
                const message= document.getElementById('JS_flash_msg').value.trim();

                if(password.length<8){
                    event.preventDefault();
                    message.innerText="Password length must be at least 8 characters";
                    message.style.display='block';
                    return;
                }   
        
                if($name=='' || $email=='' || $password==''|| $userRole==''){
                    event.preventDefault();
                    message.innerText="Please fill all required fields";
                    message.style.display='block';
                    return;
                }

                const emailpattern = document.createElement('emailpattern');
                emailpattern.type='email';
                emailpattern.value=email;
                if(emailpattern.checkValidity()==false){
                    event.preventDefault();
                    message.innerText="Please enter a valid email ";
                    message.style.display='block';
                    return;
                }  
                message.style.display='none';
            }
        </script>
    </body>
</html>