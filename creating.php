<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");

    if($_SESSION['identity'] === 'admin'){
        $flight_number = $_POST['flight_number'];
        $departure = $_POST['departure_airport'];
        $destination = $_POST['destination_airport'];
        $d_year = $_POST['d_year'];
        $d_month = $_POST['d_month'];
        $d_day = $_POST['d_day'];
        $d_hour = $_POST['d_hour'];
        $d_min = $_POST['d_min'];
        $d_sec = $_POST['d_sec'];
        $a_year = $_POST['a_year'];
        $a_month = $_POST['a_month'];
        $a_day = $_POST['a_day'];
        $a_hour = $_POST['a_hour'];
        $a_min = $_POST['a_min'];
        $a_sec = $_POST['a_sec'];
        $price = $_POST['price'];
        
        if(str_replace(" ","",$flight_number)===""){
            echo 'Flight number should not be empty';
    //        $_SESSION['new_flight_message'] = 'Flight number should not be empty.';
        //    header('location:view_flight_admin.php');
        //    exit;
        }
        else if($d_year==="2016" && $d_month==="02" && ($d_day==="30" || $d_day==="31")){
            echo 'Wrong date';
    //        $_SESSION['new_flight_message'] = 'Wrong date';
    //        header('location:view_flight_admin.php');
    //        exit;
        }
        else if($d_year!="2016" && $d_month==="02" && ($d_day==="29" || $d_day==="30" || $d_day==="31")){
            echo 'Wrong date';
    //        $_SESSION['new_flight_message'] = 'Wrong date';
    //        header('location:view_flight_admin.php');
    //        exit;
        }
        else if(($d_month==="04"||$d_month==="06"||$d_month==="09"||$d_month==="11") && $d_day==="31"){
            echo 'Wrong date';
    //        $_SESSION['new_flight_message'] = 'Wrong date';
    //       header('location:view_flight_admin.php');
    //        exit;
        }
        else if($a_year==="2016" && $a_month==="02" && ($a_day==="30" || $a_day==="31")){
            echo 'Wrong date';
    //        $_SESSION['new_flight_message'] = 'Wrong date';
    //        header('location:view_flight_admin.php');
    //        exit;
        }
        else if($a_year!="2016" && $a_month==="02" && ($a_day==="29" || $a_day==="30" || $a_day==="31")){
            echo 'Wrong date';
    //        $_SESSION['new_flight_message'] = 'Wrong date';
    //        header('location:view_flight_admin.php');
    //        exit;
        }
        else if(($a_month==="04"||$a_month==="06"||$a_month==="09"||$a_month==="11") && $a_day==="31"){
            echo 'Wrong date';
    //        $_SESSION['new_flight_message'] = 'Wrong date';
    //        header('location:view_flight_admin.php');
    //        exit;
        }
        else if(!is_numeric($price)){
            echo "Incorrect price";
    //        $_SESSION['new_flight_message'] = 'Incorrect price';
       //     header('location:view_flight_admin.php');
       //     exit;
        }
        else{
            $match_pattern = '/^([\_\+\-\ 0-9a-zA-Z]+)$/';
            if(preg_match($match_pattern,$flight_number) && is_numeric($departure) && is_numeric($destination)){
                $sql = "INSERT INTO `flight` (flight_number,departure_id,destination_id,departure_date,arrival_date,price)"
                      ." VALUES(:fn,:dp,:ds,:dd,:ad,:pr)";
                $sth = $db->prepare($sql);
                $sth->execute(array(
                    ':fn' => $flight_number,
                    ':dp' => $departure,
                    ':ds' => $destination,
                    ':dd' => $d_year."-".$d_month."-".$d_day." ".$d_hour.":".$d_min.":".$d_sec,
                    ':ad' => $a_year."-".$a_month."-".$a_day." ".$a_hour.":".$a_min.":".$a_sec,
                    ':pr' => $price));
            }
            echo "okok";
    //        header('location:view_flight_admin.php');
    //        exit;
        }
    }
?>


