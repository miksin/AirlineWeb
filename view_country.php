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
    <title>Flight schedule - Country management</title>
    <body bgcolor="#EEEEEE">
        <div class="page-header">
            <h1>Flight Schedule <small>Country Management</small></h1>
        </div><br>
        <div id="welcome" class="row">
            <ul class="nav nav-tabs">
                <li class="dropdown active">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="glyphicon glyphicon-user"></span> <?php echo $user_information->account; ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                        <li><a href="view_user.php"><span class="glyphicon glyphicon-user"></span> User management</a></li>
                        <li><a href="view_airport.php"><span class="glyphicon glyphicon-map-marker"></span> Airport information</a></li>
                        <li class="disabled"><a href="#"><span class="glyphicon glyphicon-globe"></span> Country management</a></li>
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
        <div id="list" class="row">
            <div class="col-md-4">
            <?php            
                $sql = "SELECT * FROM `country` ORDER BY `abbreviation`";
                $sth = $db->prepare($sql);
                $sth->execute();
                
                if(!$id = $_SESSION['country_id']){
                    echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
                    echo "<td width='200'>name</td>";
                    echo "<td width='200'>fullname</td>";
                    echo "<td width='100'>edit</td>";
                    echo "<td>delete</td></tr><tr>";
                    while($result = $sth->fetchObject()){
                        echo "<form method='post' action='edit_country.php'>";
                        echo "<td width='200'>".$result->abbreviation."</td>";
                        echo "<td width='200'>".$result->fullname."</td>";
                        echo "<td width='100'><button type='submit' class='btn btn-primary btn-sm' name='change' value='".$result->id."'>edit</button></td>";
                        echo "<td><button type='submit' class='btn btn-danger btn-sm' name='delete' value='".$result->id."'>delete</button></td>";
                        echo "</form></tr><tr>";
                    }
                    echo "</table>";
                }
                else {
                    echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
                    echo "<td width='200'>name</td>";
                    echo "<td width='200'>fullname</td>";
                    echo "<td width='100'>edit</td>";
                    echo "<td></td></tr><tr>";
                    while($result = $sth->fetchObject()){
                        if($result->id == $id){
                            echo "<form method='post' action='edit_country.php'>";
                            echo "<td width='200'><input type='text' size='20' name='name' value='".$result->abbreviation."'/></td>";
                            echo "<td width='200'><input type='text' size='20' name='fullname' value='".$result->fullname."'/></td>";
                            echo "<td width='100'><button type='submit' class='btn btn-primary btn-sm' name='edit_ok' value='".$result->id."'>ok</button></td>";
                            echo "<td><font color='#FF0000'><em>".$_SESSION['country_message']."</em></font></td>";
                            unset($_SESSION['country_message']);
                            echo "</form></tr><tr>";
                        }
                        else {
                            echo "<td width='200'>".$result->abbreviation."</td>";
                            echo "<td width='200'>".$result->fullname."</td>";
                            echo "<td width='100''></td>";
                            echo "<td></tr></tr><tr>";
                        }
                    }
                    echo "</table>";
                }
            
            if(!$id){
                echo "<font color='#FF0000'>".$_SESSION['message']."</font>";
                unset($_SESSION['message']);
                echo "<form method='post' action='adding_country.php'>";
                echo "<table><tr>";
                echo "<td width='200'>Name:</td>";
                echo "<td width='200'>Fullname:</td>";
                echo "</tr><br><tr>";
                echo "<td width='200'><input type='text' size='22' name='country_name' value='".$_SESSION['input_country_name']."'/></td>";
                unset($_SESSION['input_country_name']);
                echo "<td width='200'><input type='text' size='22' name='country_fullname' value='".$_SESSION['input_fullname']."'/></td>";
                unset($_SESSION['input_fullname']);
                echo "<td><button type='submit' class='btn btn-success btn-xs' name='add_country' value='add_country' />add country</button></td>";
                echo "</tr></table></form>";
            }
            ?>
            </div>
        </div>
    </body>
</html>
