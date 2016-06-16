<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    $user_information = check_admin();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="bootstrap/js/jquery-1.11.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function(){
                $("#list").hide();
                $("#list").fadeIn("slow");
            });
        </script>
        <style>
            body {
                margin-left: 10px;
            }
        </style>
    </head>
    <title>Flight schedule - User management</title>
    <body bgcolor="#EEEEEE">
        <div class="page-header">
            <h1>Flight Schedule <small>User Management</small></h1>
        </div><br>
        <div id="welcome" class="row">
            <ul class="nav nav-tabs">
                <li class="dropdown active">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="glyphicon glyphicon-user"></span> <?php echo $user_information->account; ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                        <li class="disabled"><a href="#"><span class="glyphicon glyphicon-user"></span> User management</a></li>
                        <li><a href="view_airport.php"><span class="glyphicon glyphicon-map-marker"></span> Airport information</a></li>
                        <li><a href="view_country.php"><span class="glyphicon glyphicon-globe"></span> Country management</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php">logout</a></li>
                    </ul>
                </li>
                <li><a href="view_flight_admin.php"><span class="glyphicon glyphicon-plane"></span> Flight list</a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="glyphicon glyphicon-sort-by-attributes-alt"></span> Compare<span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="cmpsheet.php"> Flight</a></li>
                        <li><a href="shoppinglist.php"> Ticket</a></li>
                    </ul>
                </li>
            </ul><br>
        </div>
        <div id="list">
        <?php            
            $sql = "SELECT * FROM `user` ORDER BY `id`";
            $sth = $db->prepare($sql);
            $sth->execute();
            
            echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
            echo "<td width='30'>id</td>";
            echo "<td width='300'>account</td>";
            echo "<td width='100'>identity</td>";
            echo "<td width='70'>modify</td>";
            echo "<td width='70'>delete</td>";
            echo "<td></td></tr><tr>";
            while($result = $sth->fetchObject()){
                echo "<td width='30'>".$result->id."</td>";
                echo "<td width='300'>".$result->account."</td>";
                if($user_information->id == $result->id){
                    echo "<td width='100'>admin</td>";
                    echo "<td width='70'></td>";
                    echo "<td width='70'></td>";
                    echo "<td></td>";
                    echo "</tr><tr>";
                }
                else {
                    if($result->is_admin == 1){
                        echo "<td width='100'>admin</td>";
                        echo "<form method='post' action='edit_user.php'>";
                        echo "<td width='70'></td>";
                        echo "<td width='70'><button type='submit' class='btn btn-danger btn-sm' name='delete' value='".$result->id."'>delete</button></td>";
                        echo "<td></td>";
                        echo "</form></tr><tr>";
                    }
                    else {
                        echo "<td width='100'>user</td>";
                        echo "<form method='post' action='edit_user.php'>";
                        echo "<td width='70'><button type='submit' class='btn btn-primary btn-sm' name='change' value='".$result->id."'>admin</button></td>";
                        echo "<td width='70'><button type='submit' class='btn btn-danger btn-sm' name='delete' value='".$result->id."'>delete</button></td>";
                        echo "<td></td>";
                        echo "</form></tr><tr>";
                    }
                }


            }
            echo "</table>";
        ?><br><br><?php
        echo "<font color='#FF0000'>".$_SESSION['message']."</font>";
        unset($_SESSION['message']);?>
        <form method='post' action='adding_user.php'>
            <table><tr>
            <td width='200'>account:</td>
            <td width='160'>password:</td>
            <td width='180'>password confirmation:</td>
            </tr><br><tr>
            <td width='200'><input type="text" size="22" name="email" value='<?php echo $_SESSION['input_email']; unset($_SESSION['input_email']);?>'/></td>
            <td width='160'><input type="password" size="16" name="pw" /></td>
            <td width='160'><input type="password" size="16" name="pw_" /></td>
            <td width='85'><select name="is_admin" size="1" />
                <option value='0' >user
                <option value='1' <?php if($_SESSION['choose_admin']){echo 'selected'; unset($_SESSION['choose_admin']);} ?>>admin
            </select></td>
            <td><button type="submit" class="btn btn-success btn-xs" name="add_user" value="add_user" />add user</button></td>
            </tr></table>
        </form>
        </div>
    </body>
</html>
