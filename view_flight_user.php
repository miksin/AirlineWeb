<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    $user_information = check_user();
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
        <script>
        $(document).ready(function(){
            $('button[ftype="following"]').click(function(){
                var fid = $(this).parent().attr('id');
                $.post('following.php',{
                    no_prefer_flight: fid
                    }, function(){
                        window.location.href='view_flight_user.php';
                    }
                );
            });
        });
        $(document).ready(function(){
            $('button[ftype="nofollow"]').click(function(){
                var fid = $(this).parent().attr('id');
                $.post('following.php',{
                    prefer_flight: fid 
                    }, function(){
                        window.location.href='view_flight_user.php';
                    }
                );
            });
        });
        </script>
    </head>
    <title>Flight Schedule - Flight list</title>
    <body bgcolor="#EEEEEE">
        <div class="page-header">
            <h1>Flight Schedule <small>Flight List</small></h1>
        </div><br>
        <div id="welcome" class="row">
            <ul class="nav nav-tabs">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="glyphicon glyphicon-user"></span> <?php echo $user_information->account; ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php">logout</a></li>
                    </ul>
                </li>
                <li class="active"><a href="#"><span class="glyphicon glyphicon-plane"></span> Flight list</a></li>
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
        <?php
            if($_POST['cancel_search']){
                unset($_SESSION['flight_input_pattern']);
                unset($_SEESION['flight_searchby']);
            }

            if($_POST['sortby'])
                $_SESSION['flight_sortby'] = $_POST['sortby'];
            else if(!$_SESSION['flight_sortby'])
                $_SESSION['flight_sortby'] = 'flight_number';

            if($_POST['order'])
                $_SESSION['flight_order'] = $_POST['order'];
            else if(!$_SESSION['flight_order'])
                $_SESSION['flight_order'] = 'ASC';

            if($_POST['searchby'])
                $_SESSION['flight_searchby'] = $_POST['searchby'];
            else if(!$_SESSION['flight_searchby'])
                $_SESSION['flight_searchby'] = 'flight_number';

            if($_POST['input_pattern'])
                $_SESSION['flight_input_pattern'] = $_POST['input_pattern'];
            else if(!$_SESSION['flight_input_pattern'])
                $_SESSION['flight_input_pattern'] = '';
        ?>
        <div class="row">
            <div class="col-lg-3"><form method="post" action="view_flight_user.php">
                <select name="sortby" size="1">
                    <option value="id" <?php if($_SESSION['flight_sortby']==='id'){echo "selected ";}?>>id</option>
                    <option value="flight_number" <?php if($_SESSION['flight_sortby']==='flight_number'){echo "selected ";}?>>flight number</option>
                    <option value="departure" <?php if($_SESSION['flight_sortby']==='departure'){echo "selected ";}?>>departure</option>
                    <option value="destination" <?php if($_SESSION['flight_sortby']==='destination'){echo "selected ";}?>>destination</option>
                    <option value="departure_date" <?php if($_SESSION['flight_sortby']==='departure_date'){echo "selected ";}?>>departure date</option>
                    <option value="arrival_date" <?php if($_SESSION['flight_sortby']==='arrival_date'){echo "selected ";}?>>arrival date</option>
                    <option value="price" <?php if($_SESSION['flight_sortby']==='price'){echo "selected ";}?>>price</option>
                </select>
                <select name="order" size="1">
                    <option value="ASC" <?php if($_SESSION['flight_order']==='ASC'){echo "selected ";}?>>ascending</option>
                    <option value="DESC" <?php if($_SESSION['flight_order']==='DESC'){echo "selected ";}?>>descending</option>
                </select>
                <button type="submit" class="btn btn-success btn-xs" name="button" >OK</button>
            </form></div>
            <div class="col-lg-4"><form method="post" action="view_flight_user.php">
                <select name="searchby" size="1">
                    <option value="flight_number" <?php if($_SESSION['flight_searchby']==='flight_number'){echo "selected ";}?>>flight number</option>
                    <option value="departure" <?php if($_SESSION['flight_searchby']==='departure'){echo "selected ";}?>>departure</option>
                    <option value="destination" <?php if($_SESSION['flight_searchby']==='destination'){echo "selected ";}?>>destination</option>
                    <option value="departure_date" <?php if($_SESSION['flight_searchby']==='departure_date'){echo "selected ";}?>>departure date</option>
                    <option value="arrival_date" <?php if($_SESSION['flight_searchby']==='arrival_date'){echo "selected ";}?>>arrival date</option>
                    <option value="price" <?php if($_SESSION['flight_searchby']==='price'){echo "selected ";}?>>price</option>
                </select>
                <input type="text" name="input_pattern" value="<?php echo $_SESSION['flight_input_pattern'];?>"></input>
                <button class="btn btn-default btn-sm" type="submit"><span class="glyphicon glyphicon-search"></span></button>
            </form></div>
            <?php
                if($_SESSION['flight_input_pattern']){
                    echo "<div class='col-lg-1'><form method='post' action='view_flight_user.php'>";
                    echo "<button class='btn btn-danger btn-xs' type='submit' name='cancel_search' value='1'>cancel search</button>";
                    echo "</form></div>";
                }
            ?>
        </div>

        <?php
            $sql = "SELECT * FROM `flight` ORDER BY ".$_SESSION['flight_sortby']." ".$_SESSION['flight_order'].",`flight_number` ASC";
            $sth = $db->prepare($sql);
            $sth->execute();
            
            echo "<table class='table table-striped table-hover'><tr class='info'>";
            if($_SESSION['flight_sortby'] === 'id')
                if($_SESSION['flight_order']==='ASC')
                    echo "<td width='50'>id <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='50'>id <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                    echo "<td width='50'>id </td>";
                
            if($_SESSION['flight_sortby'] === 'flight_number')
                if($_SESSION['flight_order']==='ASC')
                    echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                    echo "<td width='160'>Flight number </td>";

            if($_SESSION['flight_sortby'] === 'departure')
                if($_SESSION['flight_order']==='ASC')
                    echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='160'>Departure</td>";

            if($_SESSION['flight_sortby'] === 'destination')
                if($_SESSION['flight_order']==='ASC')
                    echo "<td width='160'>Destination <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='160'>Destination <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='160'>Destination</td>";

            if($_SESSION['flight_sortby'] === 'departure_date')
                if($_SESSION['flight_order']==='ASC')
                    echo "<td width='200'>Departure date <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='200'>Departure date <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='200'>Departure date</td>";

            if($_SESSION['flight_sortby'] === 'arrival_date')
                if($_SESSION['flight_order']==='ASC')
                    echo "<td width='200'>Arrival date <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='200'>Arrival date <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='200'>Arrival date</td>";

            if($_SESSION['flight_sortby'] === 'price')
                if($_SESSION['flight_order']==='ASC')
                    echo "<td width='100'>Price <span class='glyphicon glyphicon-chevron-up'></span></td>";
                else
                    echo "<td width='100'>Price <span class='glyphicon glyphicon-chevron-down'></span></td>";
            else
                echo "<td width='100'>Price</td>";

            echo "<td width='70'>favorite</td>";
            echo "<td></td></tr>";
            
            while($result = $sth->fetchObject()){
                if(preg_match("/".$_SESSION['flight_input_pattern']."/i",$result->$_SESSION['flight_searchby'])){
                    echo "<tr><td width='30'>".$result->id."</td>";
                    echo "<td width='160'>".$result->flight_number."</td>";
                    echo "<td width='160'>".$result->departure."</td>";
                    echo "<td width='160'>".$result->destination."</td>";
                    echo "<td width='200'>".$result->departure_date."</td>";
                    echo "<td width='200'>".$result->arrival_date."</td>";
                    echo "<td width='100'>".$result->price."</td>";
                    $sql = "SELECT * FROM `cmpsheet`"
                          ." WHERE `flight_id` = :fd AND `user_id` = :ud";
                    $sth1 = $db->prepare($sql);
                    $sth1->execute(array(
                        ':fd' => $result->id,
                        ':ud' => $user_information->id));
                    if(!$cmp = $sth1->fetchObject()){
                        echo "<td><div id='".$result->id."'><button type='button' class='btn btn-warning btn-sm' ftype='nofollow'><span class='glyphicon glyphicon-heart-empty'></span></button></div></td>";
                    }
                    else {
                        echo "<td><div id='".$result->id."'><button type='button' class='btn btn-warning btn-sm' ftype='following'><span class='glyphicon glyphicon-heart'></span></button></div></td>";
                    }
                    echo "<td></td></tr>";
                }
            }
            echo "</table>";
        ?>
        <br><br><a href="logout.php">logout</a><br>
    </body>
</html>
