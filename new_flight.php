<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    if($_POST['newflight'] || $_SESSION['message']){
        check_admin();
    }
    else {
        header('location:index.php');
        exit;
    }
    
    date_default_timezone_set('Asia/Taipei');
    $current_time = date("Y-m-d H:i:s");

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
    <title>Flight Schedule - New flight</title>
    <body bgcolor="#EEEEEE">
        <h1>Create</h1><br>
        <?php
            echo "<font color='#FF0000'><strong>".$_SESSION['message']."</strong></font>";
            unset($_SESSION['message']);
            $d_year = substr($current_time,0,4);
            $d_month = substr($current_time,5,2);
            $d_day = substr($current_time,8,2);
            $d_hour = substr($current_time,11,2);
            $d_min = substr($current_time,14,2);
            $d_sec = substr($current_time,17,2);
        ?>
        <br><form name="form" method="post" action="creating.php">
            flight number:<br><input type="text" name="flight_number" value="<?php echo $_SESSION['flight_number']; unset($_SESSION['flight_number']);?>"/><br>
            departure:<br>
            <select name="departure" size="1">
                <?php
                    $sql = "SELECT * FROM `airport` ORDER BY `abbreviation`";
                    $sth = $db->prepare($sql);
                    $sth->execute();
                    while($airports = $sth->fetchObject()){
                        if($airports->id == $_SESSION['departure']){
                            echo "<option value='".$airports->id."' selected>".$airports->abbreviation." (".$airports->fullname.")"."</option>";
                            unset($_SESSION['departure']);
                        }
                        else {
                            echo "<option value='".$airports->id."'>".$airports->abbreviation." (".$airports->fullname.")"."</option>";
                        }
                    }
                ?>
            </select><br>
            destination:<br>
            <select name="destination" size="1">
                <?php
                    $sql = "SELECT * FROM `airport` ORDER BY `abbreviation`";
                    $sth = $db->prepare($sql);
                    $sth->execute();
                    while($airports = $sth->fetchObject()){
                        if($airports->id == $_SESSION['destination']){
                            echo "<option value='".$airports->id."' selected>".$airports->abbreviation." (".$airports->fullname.")"."</option>";
                            unset($_SESSION['destination']);
                        }
                        else {
                            echo "<option value='".$airports->id."'>".$airports->abbreviation." (".$airports->fullname.")"."</option>";
                        }
                    }
                ?>
            </select><br>
            departure date:<br>
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
            arrival date:<br>
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
            price:<br><input type="text" size="14" name="price" value="<?php echo $_SESSION['price']; unset($_SESSION['price']);?>"/><br>
            <button type="submit" class="btn btn-success btn-xs" name="button" value="new">OK</button>&nbsp;&nbsp;&nbsp;
            <a href="view_flight_admin.php">Back</a>
        </form>
    </body>
</html>
