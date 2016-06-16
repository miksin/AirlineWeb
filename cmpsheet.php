<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    $user_information = check_login();
    $user_authority = $_SESSION['identity'];
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="bootstrap/js/jquery-1.11.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <style>
            body {
                margin-left: 10px;
            }
        </style>
    </head>
    <title>Flight Schedule - Comparasion sheet</title>
    <body bgcolor="#EEEEEE">
        <div class="page-header">
            <h1>Flight Schedule <small>Your Favorates - flight</small></h1>
        </div><br>
        <div id="welcome" class="row">
            <ul class="nav nav-tabs">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="glyphicon glyphicon-user"></span> <?php echo $user_information->account; ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                        <li><a href="view_user.php"><span class="glyphicon glyphicon-user"></span> User management</a></li>
                        <li><a href="view_airport.php"><span class="glyphicon glyphicon-map-marker"></span> Airport information</a></li>
                        <li><a href="view_country.php"><span class="glyphicon glyphicon-globe"></span> Country management</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php">logout</a></li>
                    </ul>
                </li>
                <li><a href="view_flight_<?php echo $user_authority;?>.php"><span class="glyphicon glyphicon-plane"></span> Flight list</a></li>
                <li class="dropdown active">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="glyphicon glyphicon-sort-by-attributes-alt"></span> Compare<span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="disabled"><a href="#"> Flight</a></li>
                        <li><a href="shoppinglist.php"> Ticket</a></li>
                    </ul>
                </li>
            </ul><br>
        </div>
        <?php
            if($_POST['cancel_search']){
                unset($_SESSION['cmp_input_pattern']);
                unset($_SEESION['cmp_searchby']);
            }

            if($_POST['sortby'])
                $_SESSION['cmp_sortby'] = $_POST['sortby'];
            else if(!$_SESSION['cmp_sortby'])
                $_SESSION['cmp_sortby'] = 'flight_number';

            if($_POST['order'])
                $_SESSION['cmp_order'] = $_POST['order'];
            else if(!$_SESSION['cmp_order'])
                $_SESSION['cmp_order'] = 'ASC';

            if($_POST['searchby'])
                $_SESSION['cmp_searchby'] = $_POST['searchby'];
            else if(!$_SESSION['cmp_searchby'])
                $_SESSION['cmp_searchby'] = 'flight_number';

            if($_POST['input_pattern'])
                $_SESSION['cmp_input_pattern'] = $_POST['input_pattern'];
            else if(!$_SESSION['cmp_input_pattern'])
                $_SESSION['cmp_input_pattern'] = '';
        ?>
        <div class="row">
            <div class="col-lg-3"><form method="post" action="cmpsheet.php">
                <select name="sortby" size="1">
                    <option value="id" <?php if($_SESSION['cmp_sortby']==='id'){echo "selected ";}?>>id</option>
                    <option value="flight_number" <?php if($_SESSION['cmp_sortby']==='flight_number'){echo "selected ";}?>>flight number</option>
                    <option value="departure" <?php if($_SESSION['cmp_sortby']==='departure'){echo "selected ";}?>>departure</option>
                    <option value="destination" <?php if($_SESSION['cmp_sortby']==='destination'){echo "selected ";}?>>destination</option>
                    <option value="departure_date" <?php if($_SESSION['cmp_sortby']==='departure_date'){echo "selected ";}?>>departure date</option>
                    <option value="arrival_date" <?php if($_SESSION['cmp_sortby']==='arrival_date'){echo "selected ";}?>>arrival date</option>
                    <option value="price" <?php if($_SESSION['cmp_sortby']==='price'){echo "selected ";}?>>price</option>
                </select>
                <select name="order" size="1">
                    <option value="ASC" <?php if($_SESSION['cmp_order']==='ASC'){echo "selected ";}?>>ascending</option>
                    <option value="DESC" <?php if($_SESSION['cmp_order']==='DESC'){echo "selected ";}?>>descending</option>
                </select>
                <button type="submit" class="btn btn-success btn-xs" name="button" >OK</button>
            </form></div>
            <div class="col-lg-5"><form method="post" action="cmpsheet.php">
                <select name="searchby" size="1">
                    <option value="flight_number" <?php if($_SESSION['cmp_searchby']==='flight_number'){echo "selected ";}?>>flight number</option>
                    <option value="departure" <?php if($_SESSION['cmp_searchby']==='departure'){echo "selected ";}?>>departure</option>
                    <option value="destination" <?php if($_SESSION['cmp_searchby']==='destination'){echo "selected ";}?>>destination</option>
                    <option value="departure_date" <?php if($_SESSION['cmp_searchby']==='departure_date'){echo "selected ";}?>>departure date</option>
                    <option value="arrival_date" <?php if($_SESSION['cmp_searchby']==='arrival_date'){echo "selected ";}?>>arrival date</option>
                    <option value="price" <?php if($_SESSION['cmp_searchby']==='price'){echo "selected ";}?>>price</option>
                </select>
                <input type="text" name="input_pattern" value="<?php echo $_SESSION['cmp_input_pattern'];?>"></input>
                <button class="btn btn-default btn-sm" type="submit"><span class="glyphicon glyphicon-search"></span></button>
            </form></div>
            <?php
                if($_SESSION['cmp_input_pattern']){
                    echo "<div class='col-lg-1'><form method='post' action='cmpsheet.php'>";
                    echo "<button class='btn btn-danger btn-xs' type='submit' name='cancel_search' value='1'>cancel search</button>";
                    echo "</form></div>";
                }
            ?>
        </div>
        
        <?php
            $sql = "SELECT * FROM `flight` ORDER BY ".$_SESSION['cmp_sortby']." ".$_SESSION['cmp_order'].",`flight_number` ASC";
            $sth = $db->prepare($sql);
            $sth->execute();
            
            echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
            if($_SESSION['cmp_sortby'] === 'id')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='50'>id <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='50'>id <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                    echo "<td width='50'>id </td>";
                
            if($_SESSION['cmp_sortby'] === 'flight_number')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                    echo "<td width='160'>Flight number </td>";

            if($_SESSION['cmp_sortby'] === 'departure')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='160'>Departure</td>";

            if($_SESSION['cmp_sortby'] === 'destination')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='160'>Destination <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Destination <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='160'>Destination</td>";

            if($_SESSION['cmp_sortby'] === 'departure_date')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='200'>Departure date <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='200'>Departure date <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='200'>Departure date</td>";

            if($_SESSION['cmp_sortby'] === 'arrival_date')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='200'>Arrival date <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='200'>Arrival date <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='200'>Arrival date</td>";

            if($_SESSION['cmp_sortby'] === 'price')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='100'>Price <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='100'>Price <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='100'>Price</td>";

            echo "<td width='70'>delete</td>";
            echo "<td></td></tr><tr>";
            while($result = $sth->fetchObject()){
                $sql = "SELECT * FROM `cmpsheet`"
                      ." WHERE `flight_id` = :fd AND `user_id` = :ud";
                $sth1 = $db->prepare($sql);
                $sth1->execute(array(
                    ':fd' => $result->id,
                    ':ud' => $user_information->id));
                if($sheet = $sth1->fetchObject()){
                    if(preg_match("/".$_SESSION['cmp_input_pattern']."/i",$result->$_SESSION['cmp_searchby'])){
                        echo "<td width='50'>".$result->id."</td>";
                        echo "<td width='160'>".$result->flight_number."</td>";
                        echo "<td width='160'>".$result->departure."</td>";
                        echo "<td width='160'>".$result->destination."</td>";
                        echo "<td width='200'>".$result->departure_date."</td>";
                        echo "<td width='200'>".$result->arrival_date."</td>";
                        echo "<td width='100'>".$result->price."</td>";
                        echo "<td witdh='70'><form method='post' action='following.php'>";
                        echo "<button type='submit' class='btn btn-danger btn-sm' name='no_follow' value='".$sheet->id."'>delete</button>";
                        echo "</form></td>";
                        echo "<td></td></tr><tr>";
                    }
                }
            }
            echo "</table>";
        ?>

        <br><br>
        <h3>Not in comparasion sheet</h3>
        <?php
            $sql = "SELECT * FROM `flight` ORDER BY ".$_SESSION['cmp_sortby']." ".$_SESSION['cmp_order'].",`flight_number` ASC";
            $sth = $db->prepare($sql);
            $sth->execute();
            
            echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
            if($_SESSION['cmp_sortby'] === 'id')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='50'>id <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='50'>id <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                    echo "<td width='50'>id </td>";
                
            if($_SESSION['cmp_sortby'] === 'flight_number')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                    echo "<td width='160'>Flight number </td>";

            if($_SESSION['cmp_sortby'] === 'departure')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='160'>Departure</td>";

            if($_SESSION['cmp_sortby'] === 'destination')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='160'>Destination <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Destination <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='160'>Destination</td>";

            if($_SESSION['cmp_sortby'] === 'departure_date')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='200'>Departure date <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='200'>Departure date <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='200'>Departure date</td>";

            if($_SESSION['cmp_sortby'] === 'arrival_date')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='200'>Arrival date <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='200'>Arrival date <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='200'>Arrival date</td>";

            if($_SESSION['cmp_sortby'] === 'price')
                if($_SESSION['cmp_order']==='ASC')
                    echo "<td width='100'>Price <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='100'>Price <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='100'>Price</td>";

            echo "<td width='70'>follow</td>";
            echo "<td></td></tr><tr>";
            while($result = $sth->fetchObject()){
                $sql = "SELECT * FROM `cmpsheet`"
                      ." WHERE `flight_id` = :fd AND `user_id` = :ud";
                $sth1 = $db->prepare($sql);
                $sth1->execute(array(
                    ':fd' => $result->id,
                    ':ud' => $user_information->id));
                if(!$sth1->fetchObject()){
                    if(preg_match("/".$_SESSION['cmp_input_pattern']."/i",$result->$_SESSION['cmp_searchby'])){
                        echo "<td width='50'>".$result->id."</td>";
                        echo "<td width='160'>".$result->flight_number."</td>";
                        echo "<td width='160'>".$result->departure."</td>";
                        echo "<td width='160'>".$result->destination."</td>";
                        echo "<td width='200'>".$result->departure_date."</td>";
                        echo "<td width='200'>".$result->arrival_date."</td>";
                        echo "<td width='100'>".$result->price."</td>";
                        echo "<td witdh='70'><form method='post' action='following.php'>";
                        echo "<button type='submit' class='btn btn-success btn-sm' name='add_follow' value='".$result->id."'>follow</button>";
                        echo "</form></td>";
                        echo "<td></td></tr><tr>";
                    }
                }
            }
            echo "</table>";
        ?>

    </body>
</html>
