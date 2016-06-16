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
        <style>
            body {
                margin-left: 10px;
            }
        </style>
        <script>
        $(document).ready(function(){
            $('#add_flight_button').click(function(){
                $.ajax({
                    url:'creating.php',
                    data:$('#add_flight_form').serialize(),
                    type:'POST',
                    dataType:'text',
                    success:function(result){
                        if(!result.match('okok')){
                            $('#new_error_message').html(result);
                        }
                        else {
                            window.location.href='view_flight_admin.php';
                        }
                    }
                });
            });
        });
        $(document).ready(function(){
            $('button[ftype="edit"]').click(function(){
                var eid = $(this).parent().attr('id');
                var flight_number = $(this).parent().attr('flight_number');
                var departure_airport = $(this).parent().attr('departure_airport');
                var destination_airport = $(this).parent().attr('destination_airport');
                var d_year = $(this).parent().attr('d_year');
                var d_month = $(this).parent().attr('d_month');
                var d_day = $(this).parent().attr('d_day');
                var d_hour = $(this).parent().attr('d_hour');
                var d_min = $(this).parent().attr('d_min');
                var d_sec = $(this).parent().attr('d_sec');
                var a_year = $(this).parent().attr('a_year');
                var a_month = $(this).parent().attr('a_month');
                var a_day = $(this).parent().attr('a_day');
                var a_hour = $(this).parent().attr('a_hour');
                var a_min = $(this).parent().attr('a_min');
                var a_sec = $(this).parent().attr('a_sec');
                var price = $(this).parent().attr('price');
                $('#edit_flight_number').val(flight_number);
                $('#edit_departure_airport').val(departure_airport);
                $('#edit_destination_airport').val(destination_airport);
                $('#d_year').val(d_year);
                $('#d_month').val(d_month);
                $('#d_day').val(d_day);
                $('#d_hour').val(d_hour);
                $('#d_min').val(d_min);
                $('#d_sec').val(d_sec);
                $('#a_year').val(a_year);
                $('#a_month').val(a_month);
                $('#a_day').val(a_day);
                $('#a_hour').val(a_hour);
                $('#a_min').val(a_min);
                $('#a_sec').val(a_sec);
                $('#edit_price').val(price);
                $('#edit_flight').modal('show');
                $('#edit_flight_button').click(function(){
                    $.ajax({
                        url:'editing.php',
                        data:$('#edit_flight_form').serialize() + "&button=" + eid ,
                        type:'POST',
                        dataType:'text',
                        success:function(result){
                            if(!result.match('okok')){
                                $('#edit_error_message').html(result);
                            }
                            else {
                                window.location.href='view_flight_admin.php';
                            }
                        }
                    });
                });
            });
        });
        $(document).ready(function(){
            $('button[ftype="delete"]').click(function(){
                var eid = $(this).parent().attr('id');
                $.post('deleting.php',{
                    delete_flight: eid 
                });
                window.location.href='view_flight_admin.php';
            });
        });
        $(document).ready(function(){
            $('button[ftype="following"]').click(function(){
                var fid = $(this).parent().attr('id');
                $.post('following.php',{
                    no_prefer_flight: fid
                    }, function(){
                        window.location.href='view_flight_admin.php';
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
                        window.location.href='view_flight_admin.php';
                    }
                );
            });
        });
        </script>
    </head>
    <title>Flight schedule - Flight list</title>
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
                        <li><a href="view_user.php"><span class="glyphicon glyphicon-user"></span> User management</a></li>
                        <li><a href="view_airport.php"><span class="glyphicon glyphicon-map-marker"></span> Airport information</a></li>
                        <li><a href="view_country.php"><span class="glyphicon glyphicon-globe"></span> Country management</a></li>
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
            <div class="col-lg-3"><form method="post" action="view_flight_admin.php">
                <select name="sortby" size="1">
                    <option value="flight_number" <?php if($_SESSION['flight_sortby']==='flight_number'){echo "selected ";}?>>flight number</option>
                    <option value="Departure_airport" <?php if($_SESSION['flight_sortby']==='Departure_airport'){echo "selected ";}?>>departure</option>
                    <option value="Destination_airport" <?php if($_SESSION['flight_sortby']==='Destination_airport'){echo "selected ";}?>>destination</option>
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
            <div class="col-lg-4"><form method="post" action="view_flight_admin.php">
                <select name="searchby" size="1">
                    <option value="flight_number" <?php if($_SESSION['flight_searchby']==='flight_number'){echo "selected ";}?>>flight number</option>
                    <option value="Departure_airport" <?php if($_SESSION['flight_searchby']==='Departure_airport'){echo "selected ";}?>>departure</option>
                    <option value="Destination_airport" <?php if($_SESSION['flight_searchby']==='Destination_airport'){echo "selected ";}?>>destination</option>
                    <option value="departure_date" <?php if($_SESSION['flight_searchby']==='departure_date'){echo "selected ";}?>>departure date</option>
                    <option value="arrival_date" <?php if($_SESSION['flight_searchby']==='arrival_date'){echo "selected ";}?>>arrival date</option>
                    <option value="price" <?php if($_SESSION['flight_searchby']==='price'){echo "selected ";}?>>price</option>
                </select>
                <input type="text" name="input_pattern" value="<?php echo $_SESSION['flight_input_pattern'];?>"></input>
                <button class="btn btn-default btn-sm" type="submit"><span class="glyphicon glyphicon-search"></span></button>
            </form></div>
            <?php
                if($_SESSION['flight_input_pattern']){
                    echo "<div class='col-lg-1'><form method='post' action='view_flight_admin.php'>";
                    echo "<button class='btn btn-danger btn-xs' type='submit' name='cancel_search' value='1'>cancel search</button>";
                    echo "</form></div>";
                }
            ?>
            <div class="col-md-3"></div>
            <div class="col-md-1">
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_flight">
                    new flight <span class="glyphicon glyphicon-plus"></span>
                </button>
            </div>
            <div class="col-md-1"></div>
        </div>

        <?php
            date_default_timezone_set('Asia/Taipei');
            $current_time = date("Y-m-d H:i:s");
            $d_year = substr($current_time,0,4);
            $d_month = substr($current_time,5,2);
            $d_day = substr($current_time,8,2);
            $d_hour = substr($current_time,11,2);
            $d_min = substr($current_time,14,2);
            $d_sec = substr($current_time,17,2);

            $sql = "SELECT airport.id AS airport_id,country.fullname AS country_name,airport.fullname AS airport_name".
                   " FROM `country` JOIN `airport` ON airport.belonging_country_id = country.id".
                   " ORDER BY country_name";
            $find_CA1 = $db->prepare($sql);
            $find_CA1->execute(array());
            $find_CA2 = $db->prepare($sql);
            $find_CA2->execute(array());
            
        ?>
        <div class="modal fade" id="add_flight" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <form id="add_flight_form" name="form" method="post" action="creating.php">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">New flight</h4>
                    </div>
                    <div class="modal-body">
                        <font size='3' color='#EE0000'><div id='new_error_message'></div></font>
                        <font size="3">Flight number:</font><br><input type="text" size="30" name="flight_number"><br><br>
                        <font size="3">Departure airport:</font><br>
                        <select name="departure_airport" size="1">
                            <?php
                                $country_name = '';
                                while($country_airport = $find_CA1->fetchObject()){
                                    if($country_airport->country_name != $country_name){
                                        echo "<option disabled>"."-- ".$country_airport->country_name."</option>";
                                        $country_name = $country_airport->country_name;
                                    }
                                    echo "<option value='".$country_airport->airport_id."'>".$country_airport->airport_name."</option>";
                                }
                            ?>
                        </select><br>
                        <font size="3">Destination airport:</font><br>
                        <select name="destination_airport" size="1">
                            <?php
                                $country_name = '';
                                while($country_airport = $find_CA2->fetchObject()){
                                    if($country_airport->country_name != $country_name){
                                        echo "<option disabled>"."-- ".$country_airport->country_name."</option>";
                                        $country_name = $country_airport->country_name;
                                    }
                                    echo "<option value='".$country_airport->airport_id."'>".$country_airport->airport_name."</option>";
                                }
                            ?>
                        </select><br><br>
                        <font size="3">Departure date:</font><br>
                        <select name="d_year" size="1"/>
                            <?php
                                for($i=2014;$i<=2016;$i++){
                                    if($i==$d_year){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select>
                        <select name="d_month" size="1" />
                            <option value="01"<?php if($d_month=='01'){echo 'selected';}?>>Jan</option>
                            <option value="02"<?php if($d_month=='02'){echo 'selected';}?>>Feb</option>
                            <option value="03"<?php if($d_month=='03'){echo 'selected';}?>>Mar</option>
                            <option value="04"<?php if($d_month=='04'){echo 'selected';}?>>Apr</option>
                            <option value="05"<?php if($d_month=='05'){echo 'selected';}?>>May</option>
                            <option value="06"<?php if($d_month=='06'){echo 'selected';}?>>Jun</option>
                            <option value="07"<?php if($d_month=='07'){echo 'selected';}?>>Jul</option>
                            <option value="08"<?php if($d_month=='08'){echo 'selected';}?>>Aug</option>
                            <option value="09"<?php if($d_month=='09'){echo 'selected';}?>>Sep</option>
                            <option value="10"<?php if($d_month=='10'){echo 'selected';}?>>Oct</option>
                            <option value="11"<?php if($d_month=='11'){echo 'selected';}?>>Nov</option>
                            <option value="12"<?php if($d_month=='12'){echo 'selected';}?>>Dec</option>
                        </select>
                        <select name="d_day" size="1" />
                            <?php
                                for($i=1;$i<=31;$i++){
                                    if($i==$d_day){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select>
                        &nbsp;
                        <select name="d_hour" size="1" />
                            <?php
                                for($i=00;$i<=23;$i++){
                                    if($i==$d_hour){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select>:
                        <select name="d_min" size="1" />
                            <?php
                                for($i=00;$i<=59;$i++){
                                    if($i==$d_min){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select>:
                        <select name="d_sec" size="1" />
                            <?php
                                for($i=00;$i<=59;$i++){
                                    if($i==$d_sec){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>
                        </select><br>
                        <font size="3">Arrival date:</font><br>
                        <select name="a_year" size="1"/>
                            <?php
                                for($i=2014;$i<=2016;$i++){
                                    if($i==$d_year){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select>
                        <select name="a_month" size="1" />
                            <option value="01"<?php if($d_month=='01'){echo 'selected';}?>>Jan</option>
                            <option value="02"<?php if($d_month=='02'){echo 'selected';}?>>Feb</option>
                            <option value="03"<?php if($d_month=='03'){echo 'selected';}?>>Mar</option>
                            <option value="04"<?php if($d_month=='04'){echo 'selected';}?>>Apr</option>
                            <option value="05"<?php if($d_month=='05'){echo 'selected';}?>>May</option>
                            <option value="06"<?php if($d_month=='06'){echo 'selected';}?>>Jun</option>
                            <option value="07"<?php if($d_month=='07'){echo 'selected';}?>>Jul</option>
                            <option value="08"<?php if($d_month=='08'){echo 'selected';}?>>Aug</option>
                            <option value="09"<?php if($d_month=='09'){echo 'selected';}?>>Sep</option>
                            <option value="10"<?php if($d_month=='10'){echo 'selected';}?>>Oct</option>
                            <option value="11"<?php if($d_month=='11'){echo 'selected';}?>>Nov</option>
                            <option value="12"<?php if($d_month=='12'){echo 'selected';}?>>Dec</option>
                        </select>
                        <select name="a_day" size="1" />
                            <?php
                                for($i=1;$i<=31;$i++){
                                    if($i==$d_day){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select>
                        &nbsp;
                        <select name="a_hour" size="1" />
                            <?php
                                for($i=00;$i<=23;$i++){
                                    if($i==$d_hour){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>
                        </select>:
                        <select name="a_min" size="1" />
                            <?php
                                for($i=00;$i<=59;$i++){
                                    if($i==$d_min){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select>:
                        <select name="a_sec" size="1" />
                            <?php
                                for($i=00;$i<=59;$i++){
                                    if($i==$d_sec){
                                        echo "<option value='".$i."' selected>".$i."</option>";
                                    }
                                    else {
                                        echo "<option value='".$i."'>".$i."</option>";
                                    }
                                }
                            ?>    
                        </select><br>
                        <font size="3">Price:</font><br>
                        <input type="text" size="14" name="price" value="<?php echo $_SESSION['price']; unset($_SESSION['price']);?>"/><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type='button' id='add_flight_button' name="OK" value="OK" class="btn btn-primary">OK</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <div id="flight_list" class="row">
            <div class="col-md-12">
            <?php
                $sql = "SELECT flight.id AS id,".
                       "flight_number,".
                       "A.abbreviation AS Departure_airport,".
                       "B.abbreviation AS Destination_airport,".
                       "A.id AS Departure_airport_id,".
                       "B.id AS Destination_airport_id,".
                       "departure_date,".
                       "arrival_date,".
                       "price,".
                       "A.timezone AS Departure_timezone,".
                       "B.timezone AS Destination_timezone ".
                       "FROM `flight`,`airport` AS `A`,`airport` AS `B`".
                       " WHERE A.id = flight.departure_id AND B.id = flight.destination_id".
                       " ORDER BY ".$_SESSION['flight_sortby']." ".$_SESSION['flight_order'].",`flight_number` ASC";

                $sth = $db->prepare($sql);
                $sth->execute();
            
                echo "<table class='table table-striped table-condensed table-hover'><tr class='info'>";
                if($_SESSION['flight_sortby'] === 'flight_number')
                    if($_SESSION['flight_order']==='ASC')
                        echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-up'></span></td>";
                    else
                        echo "<td width='160'>Flight number <span class='glyphicon glyphicon-chevron-down'></span></td>";
                else
                        echo "<td width='160'>Flight number </td>";

                if($_SESSION['flight_sortby'] === 'Departure_airport')
                    if($_SESSION['flight_order']==='ASC')
                        echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-up'></span></td>";
                    else
                        echo "<td width='160'>Departure <span class='glyphicon glyphicon-chevron-down'></span></td>";
                else
                    echo "<td width='160'>Departure</td>";

                if($_SESSION['flight_sortby'] === 'Destination_airport')
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

                echo "<td width='150'></td>";
                echo "<td></td></tr><tr>";
                while($result = $sth->fetchObject()){
                    if(preg_match("/".$_SESSION['flight_input_pattern']."/i",$result->$_SESSION['flight_searchby'])){
                        echo "<tr><td width='160'>".$result->flight_number."</td>";
                        echo "<td width='200'>".$result->Departure_airport." (".timezone_transform($result->Departure_timezone).")"."</td>";
                        echo "<td width='200'>".$result->Destination_airport." (".timezone_transform($result->Destination_timezone).")"."</td>";
                        echo "<td width='160'>".$result->departure_date."</td>";
                        echo "<td width='160'>".$result->arrival_date."</td>";
                        echo "<td width='100'>".$result->price."</td>";

                        $d_year  = substr($result->departure_date,0,4);
                        $d_month = substr($result->departure_date,5,2);
                        $d_day   = substr($result->departure_date,8,2);
                        $d_hour  = substr($result->departure_date,11,2);
                        $d_min   = substr($result->departure_date,14,2);
                        $d_sec   = substr($result->departure_date,17,2);
                        $a_year  = substr($result->arrival_date,0,4);
                        $a_month = substr($result->arrival_date,5,2);
                        $a_day   = substr($result->arrival_date,8,2);
                        $a_hour  = substr($result->arrival_date,11,2);
                        $a_min   = substr($result->arrival_date,14,2);
                        $a_sec   = substr($result->arrival_date,17,2);

                        $sql = "SELECT * FROM `cmpsheet`"
                              ." WHERE `flight_id` = :fd AND `user_id` = :ud";
                        $sth1 = $db->prepare($sql);
                        $sth1->execute(array(
                            ':fd' => $result->id,
                            ':ud' => $user_information->id));

                        if($cmp = $sth1->fetchObject()){
                            echo "<td width='150'><div class='btn-group' id='".$result->id."'
                                                                         flight_number='".$result->flight_number."'
                                                                         departure_airport='".$result->Departure_airport_id."'
                                                                         destination_airport='".$result->Destination_airport_id."'
                                                                         price='".$result->price."'
                                                                         d_year='".(int)$d_year."'
                                                                         d_month='".$d_month."'
                                                                         d_day='".(int)$d_day."'
                                                                         d_hour='".(int)$d_hour."'
                                                                         d_min='".(int)$d_min."' 
                                                                         d_sec='".(int)$d_sec."' 
                                                                         a_year='".(int)$a_year."'
                                                                         a_month='".$a_month."'
                                                                         a_day='".(int)$a_day."'
                                                                         a_hour='".(int)$a_hour."'
                                                                         a_min='".(int)$a_min."' 
                                                                         a_sec='".(int)$a_sec."' 
                                                                         >
                                    <button type='button' class='btn btn-primary btn-sm' ftype='edit'><span class='glyphicon glyphicon-pencil'></span></button>
                                    <button type='button' class='btn btn-danger btn-sm'  ftype='delete'><span class='glyphicon glyphicon-trash'></span></button>
                                    <button type='button' class='btn btn-warning btn-sm' ftype='following'><span class='glyphicon glyphicon-heart'></span></button>
                                  </div></td>";
                        }
                        else {
                            echo "<td width='150'><div class='btn-group' id='".$result->id."'
                                                                         flight_number='".$result->flight_number."'
                                                                         departure_airport='".$result->Departure_airport_id."'
                                                                         destination_airport='".$result->Destination_airport_id."'
                                                                         price='".$result->price."'
                                                                         d_year='".(int)$d_year."'
                                                                         d_month='".$d_month."'
                                                                         d_day='".(int)$d_day."'
                                                                         d_hour='".(int)$d_hour."'
                                                                         d_min='".(int)$d_min."' 
                                                                         d_sec='".(int)$d_sec."' 
                                                                         a_year='".(int)$a_year."'
                                                                         a_month='".$a_month."'
                                                                         a_day='".(int)$a_day."'
                                                                         a_hour='".(int)$a_hour."'
                                                                         a_min='".(int)$a_min."' 
                                                                         a_sec='".(int)$a_sec."' 
                                                                         >
                                    <button type='button' class='btn btn-primary btn-sm' ftype='edit'><span class='glyphicon glyphicon-pencil'></span></button>
                                    <button type='button' class='btn btn-danger btn-sm'  ftype='delete'><span class='glyphicon glyphicon-trash'></span></button>
                                    <button type='button' class='btn btn-warning btn-sm' ftype='nofollow'><span class='glyphicon glyphicon-heart-empty'></span></button>
                                  </div></td>";
                        }
                        echo "<td></td></tr><tr>";
                    }
                }
            ?>
            </div>
        </div><!--end of flight list-->
        
        <!--start of edit form-->
        <?php
            $sql = "SELECT airport.id AS airport_id,country.fullname AS country_name,airport.fullname AS airport_name".
                   " FROM `country` JOIN `airport` ON airport.belonging_country_id = country.id".
                   " ORDER BY country_name";
            $find_CA1 = $db->prepare($sql);
            $find_CA1->execute(array());
            $find_CA2 = $db->prepare($sql);
            $find_CA2->execute(array());
            
        ?>
        <div class="modal fade" id="edit_flight" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <form id="edit_flight_form" name="form" method="post" action="editing.php">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel2">Edit flight</h4>
                    </div>
                    <div class="modal-body">
                        <font size='3' color='#EE0000'><div id='edit_error_message'></div></font>
                        <font size="3">Flight number:</font><br><input type="text" size="30" name="flight_number" id='edit_flight_number'><br><br>
                        <font size="3">Departure airport:</font><br>
                        <select name="departure_airport" size="1" id='edit_departure_airport'>
                            <?php
                                $country_name = '';
                                while($country_airport = $find_CA1->fetchObject()){
                                    if($country_airport->country_name != $country_name){
                                        echo "<option disabled>"."-- ".$country_airport->country_name."</option>";
                                        $country_name = $country_airport->country_name;
                                    }
                                    echo "<option value='".$country_airport->airport_id."'>".$country_airport->airport_name."</option>";
                                }
                            ?>
                        </select><br>
                        <font size="3">Destination airport:</font><br>
                        <select name="destination_airport" size="1" id='edit_destination_airport'>
                            <?php
                                $country_name = '';
                                while($country_airport = $find_CA2->fetchObject()){
                                    if($country_airport->country_name != $country_name){
                                        echo "<option disabled>"."-- ".$country_airport->country_name."</option>";
                                        $country_name = $country_airport->country_name;
                                    }
                                    echo "<option value='".$country_airport->airport_id."'>".$country_airport->airport_name."</option>";
                                }
                            ?>
                        </select><br><br>
                        <font size="3">Departure date:</font><br>
                        <select name="d_year" size="1" id='d_year'>
                            <?php
                                for($i=2014;$i<=2016;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select>
                        <select name="d_month" size="1" id='d_month'>
                            <option value="01">Jan</option>
                            <option value="02">Feb</option>
                            <option value="03">Mar</option>
                            <option value="04">Apr</option>
                            <option value="05">May</option>
                            <option value="06">Jun</option>
                            <option value="07">Jul</option>
                            <option value="08">Aug</option>
                            <option value="09">Sep</option>
                            <option value="10">Oct</option>
                            <option value="11">Nov</option>
                            <option value="12">Dec</option>
                        </select>
                        <select name="d_day" size="1" id='d_day'>
                            <?php
                                for($i=1;$i<=31;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select>
                        &nbsp;
                        <select name="d_hour" size="1" id='d_hour'>
                            <?php
                                for($i=00;$i<=23;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select>:
                        <select name="d_min" size="1" id='d_min'>
                            <?php
                                for($i=00;$i<=59;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select>:
                        <select name="d_sec" size="1" id='d_sec'>
                            <?php
                                for($i=00;$i<=59;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>
                        </select><br>
                        <font size="3">Arrival date:</font><br>
                        <select name="a_year" size="1" id='a_year'>
                            <?php
                                for($i=2014;$i<=2016;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select>
                        <select name="a_month" size="1" id='a_month'>
                            <option value="01">Jan</option>
                            <option value="02">Feb</option>
                            <option value="03">Mar</option>
                            <option value="04">Apr</option>
                            <option value="05">May</option>
                            <option value="06">Jun</option>
                            <option value="07">Jul</option>
                            <option value="08">Aug</option>
                            <option value="09">Sep</option>
                            <option value="10">Oct</option>
                            <option value="11">Nov</option>
                            <option value="12">Dec</option>
                        </select>
                        <select name="a_day" size="1" id='a_day'>
                            <?php
                                for($i=1;$i<=31;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select>
                        &nbsp;
                        <select name="a_hour" size="1" id='a_hour'>
                            <?php
                                for($i=00;$i<=23;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>
                        </select>:
                        <select name="a_min" size="1" id='a_min'>
                            <?php
                                for($i=00;$i<=59;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select>:
                        <select name="a_sec" size="1" id='a_sec'>
                            <?php
                                for($i=00;$i<=59;$i++){
                                    echo "<option value='".$i."'>".$i."</option>";
                                }
                            ?>    
                        </select><br>
                        <font size="3">Price:</font><br>
                        <input type="text" size="14" name="price" id='edit_price' /><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type='button' id='edit_flight_button' name="OK" value="OK" class="btn btn-primary">OK</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </body>
</html>

