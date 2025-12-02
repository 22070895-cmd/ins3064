<!DOCTYPE html>
<html lang="en">

<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<head>
    <title>User login and Registration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
<h0>Welcome!</h0>
    <div class="container">
        <div class="login-box">
            <div class="row">
               <div class="col-md-6 login-left">
                   <h2>Login Here</h2>
                   <form action="validation.php" method="post">

                   <div class="form-group">
                       <label>Username</label>
                       <label>
                        <input type="text" name="user" class="form-control"
                        value="<?php if(isset($_COOKIE['username'])) echo $_COOKIE['username']; ?>">
                       </label>
                   </div>

                   <div class="form-group">
                       <label>Password</label>
                       <label>
                           <input type="password" name="password" class="form-control"
                           value="<?php if(isset($_COOKIE['password'])) echo $_COOKIE['password']; ?>">
                       </label>
                   </div>

                   <!-- Show / Hide Password -->
                   <input type="checkbox" onclick="togglePassword()"> Show Password
                   <br><br>

                   <button type="submit" class="btn btn-primary">Login</button>

                   <div class="form-group">
                        <label>
                            <input type="checkbox" name="remember"
                               <?php if(isset($_COOKIE['username'])) echo "checked"; ?>>
                            Remember me
                        </label>
                    </div>

                   </form>
               </div>

                <div class="col-md-6 login-right">
                    <h2>Registration Here</h2>
                    <form action="registration.php" method="post">
                     <div class="form-group">
                         <label>Username</label>
                         <input type="text" name="user" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Show password script -->
<script>
function togglePassword() {
  var x = document.getElementsByName("password")[0];
  x.type = (x.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
