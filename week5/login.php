<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="row justify-content-center">
                <div class="col-md-8 login-left">
                    <h2>Login</h2>
                    <form action="validation.php" method="post">
                        <div class="form-group">
                            <label>Username</label>
                            <input
                                type="text"
                                name="user"
                                class="form-control"
                                value="<?php if(isset($_COOKIE['username'])) echo $_COOKIE['username']; ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                value="<?php if(isset($_COOKIE['password'])) echo $_COOKIE['password']; ?>"
                                required
                            >
                        </div>

                        <div class="form-group form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="remember"
                                name="remember"
                                <?php if(isset($_COOKIE['username'])) echo "checked"; ?>
                            >
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>

                    <p class="mt-3 text-center">
                        Don't have an account ?
                        <a href="register.php">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
