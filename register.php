<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    check_nologin();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <style>
            body {
                margin-left: 20px;
            }
        </style>
    </head>
    <title>Flight Schedule - Sign up</title>
    <body bgcolor="#EEEEEE">
        <h1>Sign up</h1><br>
        <?php
            echo "<font color='#FF0000'>".$_SESSION['message']."</font>";
            unset($_SESSION['message']);
        ?>
        <br><form name="form" method="post" action="signup.php">
            Account:<br><input type="text" name="email" value='<?php echo $_SESSION['input_email']; unset($_SESSION['input_email']);?>'/><br>
            password:<br><input type="password" name="pw" /><br>
            password confirmation:<br><input type="password" name="pw_" /><br><br>
            <button type="submit" class="btn btn-success btn-xs" name="sign_up" value="Sign up" />Sign up</button><br><br>
            <a href="index.php">Sign in</a>
        </form>
    </body>
</html>
