<?php
    require_once 'config.php';
    require_once 'check_page.php';
    require_once 'functions.php';
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
                $("#list2").fadeIn("slow");
            });
        </script>
        <style>
            body {
                margin-left: 10px;
            }
        </style>
    </head>
    <title>Flight schedule - Airport information</title>
    <body bgcolor="#EEEEEE">
        <div class="page-header">
            <h1>Flight Schedule <small>Airports</small></h1>
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
                        <li class="disabled"><a href="#"><span class="glyphicon glyphicon-map-marker"></span> Airport information</a></li>
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
        <div id="list" class="row">
            <div class="col-md-12">
            <?php            
                $sql = "SELECT * FROM `airport` ORDER BY `abbreviation`";
                $sth = $db->prepare($sql);
                $sth->execute();
                
                if(!$id = $_SESSION['airport_id']){
                    echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
                    echo "<td width='30'>id</td>";
                    echo "<td width='200'>name</td>";
                    echo "<td width='350'>fullname</td>";
                    echo "<td width='100' align='right'>longitude</td>";
                    echo "<td width='100' align='right'>latitude</td>";
                    echo "<td width='70' align='right'>belonging</td>";
                    echo "<td width='100' align='right'>timezone</td>";
                    echo "<td width='100' align='right'>edit</td>";
                    echo "<td>delete</td></tr><tr>";
                    while($result = $sth->fetchObject()){
                        echo "<td width='30'>".$result->id."</td>";
                        echo "<form method='post' action='edit_airport.php'>";
                        echo "<td width='200'>".$result->abbreviation."</td>";
                        echo "<td width='350'>".$result->fullname."</td>";
                        echo "<td width='100' align='right'>".number_format($result->longitude,5)."</td>";
                        echo "<td width='100' align='right'>".number_format($result->latitude,5)."</td>";
                        
                        $sql = "SELECT * FROM `country` WHERE `id` = :id";
                        $find_country_name = $db->prepare($sql);
                        $find_country_name->execute(array(':id' => $result->belonging_country_id));
                        $country = $find_country_name->fetchObject();

                        echo "<td width='70' align='right'>".$country->abbreviation."</td>";
                        echo "<td width='100' align='right'>".timezone_transform($result->timezone)."</td>";
                        echo "<td width='100' align='right'><button type='submit' class='btn btn-primary btn-sm' name='change' value='".$result->id."'>edit</button></td>";
                        echo "<td><button type='submit' class='btn btn-danger btn-sm' name='delete' value='".$result->id."'>delete</button></td>";
                        echo "</form></tr><tr>";
                    }
                    echo "</table>";
                }
                else {
                    echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
                    echo "<td width='30'>id</td>";
                    echo "<td width='200'>name</td>";
                    echo "<td width='350'>fullname</td>";
                    echo "<td width='100' align='right'>longitude</td>";
                    echo "<td width='100' align='right'>latitude</td>";
                    echo "<td width='70' align='right'>belonging</td>";
                    echo "<td width='100' align='right'>timezone</td>";
                    echo "<td width='100' align='right'>edit</td>";
                    echo "<td></td></tr><tr>";
                    while($result = $sth->fetchObject()){
                        if($result->id == $id){
                            echo "<td width='30'>".$result->id."</td>";
                            echo "<form method='post' action='edit_airport.php'>";
                            echo "<td width='200'><input type='text' size='20' name='name' value='".$result->abbreviation."'/></td>";
                            echo "<td width='350'><input type='text' size='38' name='fullname' value='".$result->fullname."'/></td>";
                            echo "<td width='100'><input type='text' size='10' name='longitude' value='".$result->longitude."'/></td>";
                            echo "<td width='100'><input type='text' size='10' name='latitude' value='".$result->latitude."'/></td>";

                            echo "<td width='70'><select name='airport_belonging' size='1'/>";
                            $sql = "SELECT * FROM `country` ORDER BY `abbreviation`";
                            $country_option_sth = $db->prepare($sql);
                            $country_option_sth->execute();
                            while($country_option = $country_option_sth->fetchObject()){
                                if($result->belonging_country_id == $country_option->id){
                                    echo "<option value=".$country_option->id." selected>".$country_option->abbreviation."</option>";
                                }
                                echo "<option value=".$country_option->id.">".$country_option->abbreviation."</option>";
                            }
                            echo "</select></td>";

                            echo "<td width='100'><input type='text' size='10' name='timezone' value='".timezone_transform($result->timezone)."'/></td>";
                            echo "<td width='100' align='right'><button type='submit' class='btn btn-primary btn-sm' name='edit_ok' value='".$result->id."'>ok</button></td>";
                            echo "<td><font color='#FF0000'><em>".$_SESSION['airport_message']."</em></font></td>";
                            unset($_SESSION['airport_message']);
                            echo "</form></tr><tr>";
                        }
                        else {
                            echo "<td width='30'>".$result->id."</td>";
                            echo "<td width='200'>".$result->abbreviation."</td>";
                            echo "<td width='350'>".$result->fullname."</td>";
                            echo "<td width='100' align='right'>".number_format($result->longitude,5)."</td>";
                            echo "<td width='100' align='right'>".number_format($result->latitude,5)."</td>";
                            $sql = "SELECT * FROM `country` WHERE `id` = :id";
                            $find_country_name = $db->prepare($sql);
                            $find_country_name->execute(array(':id' => $result->belonging_country_id));
                            $country = $find_country_name->fetchObject();

                            echo "<td width='70' align='right'>".$country->abbreviation."</td>";

                            echo "<td width='100' align='right'>".timezone_transform($result->timezone)."</td>";
                            echo "<td width='100' align='right'></td>";
                            echo "<td></tr></tr><tr>";
                        }
                    }
                    echo "</table>";
                }
            ?>
            </div>
        </div>
        <div id="list2" class="row">
            <div class="col-md-12">
            <?php
            if(!$id){
                echo "<font color='#FF0000'>".$_SESSION['message']."</font>";
                unset($_SESSION['message']);
                echo "<form method='post' action='adding_airport.php'>";
                echo "<table><tr>";
                echo "<td width='200'>Name:</td>";
                echo "<td width='350'>fullname:</td>";
                echo "<td width='100'>longitude:</td>";
                echo "<td width='100'>latitude:</td>";
                echo "<td width='70'>belong:</td>";
                echo "<td width='100'>timezone:</td>";
                echo "</tr><br><tr>";
                echo "<td width='200'><input type='text' size='22' name='airport_name' value='".$_SESSION['input_airport_name']."'/></td>";
                unset($_SESSION['input_airport_name']);
                echo "<td width='350'><input type='text' size='38' name='airport_fullname' value='".$_SESSION['input_airport_fullname']."'/></td>";
                unset($_SESSION['input_airport_fullname']);
                echo "<td width='100'><input type='text' size='7' name='airport_longitude' value='".$_SESSION['input_longitude']."'/></td>";
                unset($_SESSION['input_longitude']);
                echo "<td width='100'><input type='text' size='7' name='airport_latitude' value='".$_SESSION['input_latitude']."'/></td>";
                unset($_SESSION['input_latitude']);
                echo "<td width='70'><select name='airport_belonging' size='1'/>";
                $sql = "SELECT * FROM `country` ORDER BY `abbreviation`";
                $country_option_sth = $db->prepare($sql);
                $country_option_sth->execute();
                while($country_option = $country_option_sth->fetchObject()){
                    if($_SESSION['input_belonging'] == $country_option->id){
                        echo "<option value=".$country_option->id." selected>".$country_option->abbreviation."</option>";
                    }
                    echo "<option value=".$country_option->id.">".$country_option->abbreviation."</option>";
                }
                echo "</select></td>";
                unset($_SESSION['input_belonging']);
                echo "<td width='100'><input type='text' size='7' name='airport_timezone' value='".timezone_transform($_SESSION['input_timezone'])."'/></td>";
                unset($_SESSION['input_timezone']);
                echo "<td><button type='submit' class='btn btn-success btn-xs' name='add_airport' value='add_airport' />add airport</button></td>";
                echo "</tr></table></form>";
            }
            
            ?>
            </div>
        </div>
    </body>
</html>
