<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    check_admin();

    if($id = $_POST['edit_flight']){
        if($log_id = $_SESSION['id']){
            $sql = "SELECT * FROM `user`"
                  ."WHERE `id` = :id";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':id' => $log_id
                ));
            if($user_information = $sth->fetchObject()){
                if($user_information->is_admin == 1){
                }
                else {
                    header('location:view_flight_user.php');
                    exit;
                }
            }
            else {
                unset($_SESSION['id']);
                header('location:index.php');
                exit;
            }
        }
        else {
            header('location:index.php');
            exit;
        }
    
    }
    else if($_SESSION['edit_flight_id']){
        $id = $_SESSION['edit_flight_id'];
        unset($_SESSION['edit_flight_id']);
    }
    else {
        header('location:index.php');
        exit;
    }
    $sql = "SELECT * FROM `flight`"
          ."WHERE `id` = :id";
    $sth = $db->prepare($sql);
    $sth->execute(array(
        ':id' => $id
        ));
    $result = $sth->fetchObject()
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
    <title>Flight schedule - Adjust flight</title>
    <body bgcolor="#EEEEEE">
        <h1>Edit</h1><br>
        <?php
            echo "<font color='#FF0000'>".$_SESSION['message']."</font>";
            unset($_SESSION['message']);
            $d_year = substr($result->departure_date,0,4);
            $d_month = substr($result->departure_date,5,2);
            $d_day = substr($result->departure_date,8,2);
            $d_hour = substr($result->departure_date,11,2);
            $d_min = substr($result->departure_date,14,2);
            $d_sec = substr($result->departure_date,17,2);
            $a_year = substr($result->arrival_date,0,4);
            $a_month = substr($result->arrival_date,5,2);
            $a_day = substr($result->arrival_date,8,2);
            $a_hour = substr($result->arrival_date,11,2);
            $a_min = substr($result->arrival_date,14,2);
            $a_sec = substr($result->arrival_date,17,2);
        ?>
        <br><form name="form" method="post" action="editing.php">
            flight number:<br><input type="text" name="flight_number" value='<?php echo $result->flight_number;?>' /><br>
            departure:<br>
            <select name="departure" size="1">
                <?php
                    $sql = "SELECT * FROM `airport` ORDER BY `name`";
                    $sth = $db->prepare($sql);
                    $sth->execute();
                    while($airports = $sth->fetchObject()){
                        if($airports->name == $result->departure){
                            echo "<option value='".$airports->name."' selected>".$airports->name."</option>";
                        }
                        else {
                            echo "<option value='".$airports->name."'>".$airports->name."</option>";
                        }
                    }
                ?>
            </select><br>
            destination:<br>
            <select name="destination" size="1">
                <?php
                    $sql = "SELECT * FROM `airport` ORDER BY `name`";
                    $sth = $db->prepare($sql);
                    $sth->execute();
                    while($airports = $sth->fetchObject()){
                        if($airports->name == $result->destination){
                            echo "<option value='".$airports->name."' selected>".$airports->name."</option>";
                        }
                        else {
                            echo "<option value='".$airports->name."'>".$airports->name."</option>";
                        }
                    }
                ?>
            </select><br>
            destination:<br><input type="text" name="destination" value='<?php echo $result->destination;?>' /><br>
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
                        if($i==$a_year){
                            echo "<option value='".$i."' selected>".$i."</option>";
                        }
                        else {
                            echo "<option value='".$i."'>".$i."</option>";
                        }
                    }
                ?>    
            </select>
            <select name="a_month" size="1" />
                <option value="01"<?php if($a_month=='01'){echo 'selected';}?>>Jan</option>
                <option value="02"<?php if($a_month=='02'){echo 'selected';}?>>Feb</option>
                <option value="03"<?php if($a_month=='03'){echo 'selected';}?>>Mar</option>
                <option value="04"<?php if($a_month=='04'){echo 'selected';}?>>Apr</option>
                <option value="05"<?php if($a_month=='05'){echo 'selected';}?>>May</option>
                <option value="06"<?php if($a_month=='06'){echo 'selected';}?>>Jun</option>
                <option value="07"<?php if($a_month=='07'){echo 'selected';}?>>Jul</option>
                <option value="08"<?php if($a_month=='08'){echo 'selected';}?>>Aug</option>
                <option value="09"<?php if($a_month=='09'){echo 'selected';}?>>Sep</option>
                <option value="10"<?php if($a_month=='10'){echo 'selected';}?>>Oct</option>
                <option value="11"<?php if($a_month=='11'){echo 'selected';}?>>Nov</option>
                <option value="12"<?php if($a_month=='12'){echo 'selected';}?>>Dec</option>
            </select>
            <select name="a_day" size="1" />
                <?php
                    for($i=1;$i<=31;$i++){
                        if($i==$a_day){
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
                        if($i==$a_hour){
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
                        if($i==$a_min){
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
                        if($i==$a_sec){
                            echo "<option value='".$i."' selected>".$i."</option>";
                        }
                        else {
                            echo "<option value='".$i."'>".$i."</option>";
                        }
                    }
                ?>    
            </select><br>
            price:<br><input type="text" size="14" name="price" value='<?php echo $result->price;?>' /><br><br>
            <button type="submit" class="btn btn-primary btn-xs" name="button" value="<?php echo $id;?>">OK</button>&nbsp;&nbsp;&nbsp;
            <a href="view_flight_admin.php">Back</a>
        </form>
    </body>
</html>
