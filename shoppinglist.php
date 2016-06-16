<?php
    require_once 'config.php';
    require_once 'check_page.php';
    require_once 'functions.php';
    session_start();
    session_save_path("./session");
    $user_information = check_login();
    $user_authority = $_SESSION['identity'];
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
        <script src="bootstrap/js/jquery-1.11.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.js"></script>
        <style>
            body {
                margin-left: 10px;
            }
        </style>
        <script>
        $(document).ready(function(){
            $('div[itype="flip"] > div:last-child').hide();
            $('a[stype="detail"]').click(function(){
                var src = '#'+ $(this).parent().parent().parent().parent().parent().parent().attr('id') +' > div:last-child';
                $(src).slideToggle("slow");
            });
        });
        $(document).on('click','a[stype="delete"]',function(){
            var src = $(this).attr('sid');
            $.ajax({
                url:'shopping.php',
                data:"shopping=" + 'unbuy' +
                    "&target_id=" + src ,
                type:'POST',
                dataType:'html',
                success:function(result){
                    location.reload();
                }
            });
        });
        </script>
    </head>
    <title>Flight Schedule - Comparasion sheet</title>
    <body bgcolor="#EEEEEE">
        <div class="page-header">
            <h1>Flight Schedule <small>Your Favorates - ticket</small></h1>
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
                        <li><a href="cmpsheet.php"> Flight</a></li>
                        <li class="disabled"><a href="#"> Ticket</a></li>
                    </ul>
                </li>
            </ul><br>
        </div>
        
        <div id="ticket_list" class="row">
            <div class="col-md-12">
            <?php
                $sql = <<<__SQL__
                SELECT * FROM shoppinglist
                WHERE user_id = :uid
__SQL__;
                $find_ticket = $db->prepare($sql);
                $find_ticket->execute(array('uid' => $user_information->id));
                $result_count = 1;

                echo "<table class='table table-hover table-condensed' table-bordered>\n";
                echo "<tr><table class='table'><tr class='info'>\n";
                echo "<td width='40' >#</td>\n";
                echo "<td width='100'>Transfer times</td>\n";
                echo "<td width='220'>Departure time</td>\n";            
                echo "<td width='220'>Arrival time</td>\n";            
                echo "<td width='100'>Total time</td>\n";
                echo "<td width='100'>Flight time</td>\n";
                echo "<td width='100'>Transfer time</td>\n";
                echo "<td width='100'>Waste time rate</td>\n";
                echo "<td width='120'>Total price</td>\n";
                echo "<td></td>\n";
                echo "<td></td>\n";
                echo "</tr></table></tr>\n";
            
                while($ticket = $find_ticket->fetchObject()){
                    $transfer_times = 0;
                    if($ticket->flight2_id) $transfer_times = 1;
                    if($ticket->flight3_id) $transfer_times = 2;

                    $ticket_information = print_ticketSQL($transfer_times,array($ticket->flight1_id,$ticket->flight2_id,$ticket->flight3_id));

                    $sql1 = "SELECT flight.flight_number AS flight_number, ".
                            "Departure_airport.abbreviation AS departure_airport, ".
                            "Destination_airport.abbreviation AS destination_airport, ".
                            "flight.departure_date AS departure_time, ".
                            "flight.arrival_date AS destination_time, ".
                            "ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flight_time, ".
                            "flight.price AS price ".
                            "FROM flight, airport AS Departure_airport, airport AS Destination_airport ".
                            "WHERE Departure_airport.id = flight.departure_id AND Destination_airport.id = flight.destination_id ".
                            "AND flight.id = :id";
                    $find_flight1 = $db->prepare($sql1);
                    $find_flight1->execute(array(':id' => $ticket->flight1_id));
                    $flight1 = $find_flight1->fetchObject();
                    if($ticket->flight2_id){
                        $find_flight2 = $db->prepare($sql1);
                        $find_flight2->execute(array(':id' => $ticket->flight2_id));
                        $flight2 = $find_flight2->fetchObject();
                    }

                    if($ticket->flight3_id){
                        $find_flight3 = $db->prepare($sql1);
                        $find_flight3->execute(array(':id' => $ticket->flight3_id));
                        $flight3 = $find_flight3->fetchObject();
                    }

                    echo "<div id='".$result_count."' itype='flip'
                               fid1='".$ticket->flight1_id."' 
                               fid2='".$ticket->flight2_id."' 
                               fid3='".$ticket->flight3_id."' 
                               >";
                    echo "<div><tr>\n"; 
                    echo "<table class='table'><tr>\n";
                    echo "<td width='40'>".$result_count."</td>\n";
                    echo "<td width='100'>".$transfer_times."</td>\n";
                    echo "<td width='220'>".$ticket_information->departure_time."</td>\n";            
                    echo "<td width='220'>".$ticket_information->arrival_time."</td>\n";            
                    echo "<td width='100'>".$ticket_information->total_time."</td>\n";
                    echo "<td width='100'>".$ticket_information->flight_time."</td>\n";
                    echo "<td width='100'>".$ticket_information->transfer_time."</td>\n";
                    echo "<td width='100'>".number_format(($ticket_information->transfer_time / $ticket_information->total_time)*100,2)."%</td>\n";
                    echo "<td width='120'>".(int)$ticket_information->total_price."</td>\n";
                    echo "<td><a href='#' class='btn btn-sm btn-default' sid='".$ticket->id."' stype='delete'><span class='glyphicon glyphicon-trash'></span> delete</a></td>";
                    echo "<td align='right'><a href='#' class='btn btn-sm btn-default' stype='detail'><span class='glyphicon glyphicon-chevron-down'></span></a></td>";
                    echo "</tr></table>\n";
                    echo "</tr></div>\n";

                    echo "<tr>\n";
                    echo "<div class='panel'>\n";

                    echo "<table class='table'><tr class='success'>\n";
                    echo "<td>flight number</td>\n";
                    echo "<td>departure airport</td>\n";
                    echo "<td>destination airport</td>\n";
                    echo "<td>departure time</td>\n";
                    echo "<td>destination time</td>\n";
                    echo "<td>flight time</td>\n";
                    echo "<td>price</td>";
                    echo "</tr>\n"; 
                    
                    echo "<tr>";
                    echo "<td>".$flight1->flight_number."</td>\n";
                    echo "<td>".$flight1->departure_airport."</td>\n";
                    echo "<td>".$flight1->destination_airport."</td>\n";
                    echo "<td>".$flight1->departure_time."</td>\n";
                    echo "<td>".$flight1->destination_time."</td>\n";
                    echo "<td>".$flight1->flight_time."</td>\n";
                    echo "<td>".$flight1->price."</td></tr>\n";
                    echo "</tr>";
                    
                    if($result->flight_number2){
                        echo "<tr>";
                        echo "<td>".$flight2->flight_number."</td>\n";
                        echo "<td>".$flight2->departure_airport."</td>\n";
                        echo "<td>".$flight2->destination_airport."</td>\n";
                        echo "<td>".$flight2->departure_time."</td>\n";
                        echo "<td>".$flight2->destination_time."</td>\n";
                        echo "<td>".$flight2->flight_time."</td>\n";
                        echo "<td>".$flight2->price."</td></tr>\n";
                        echo "</tr>";
                    }
                    if($result->flight_number3){
                        echo "<tr>";
                        echo "<td>".$flight3->flight_number."</td>\n";
                        echo "<td>".$flight3->departure_airport."</td>\n";
                        echo "<td>".$flight3->destination_airport."</td>\n";
                        echo "<td>".$flight3->departure_time."</td>\n";
                        echo "<td>".$flight3->destination_time."</td>\n";
                        echo "<td>".$flight3->flight_time."</td>\n";
                        echo "<td>".$flight3->price."</td></tr>\n";
                        echo "</tr>";
                    }

                    echo "</table>\n";
                    echo "<div class='panel-footer'>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-2'></div>";
                    echo "<div class='col-md-8' align='center'>";
                    if($transfer_times == 0){
                        echo $flight1->departure_airport;
                        echo "<span class='glyphicon glyphicon-chevron-right'></span> ";
                        echo $flight1->destination_airport;
                    }
                    else if($transfer_times == 1){
                        echo $flight1->departure_airport;
                        echo "<span class='glyphicon glyphicon-chevron-right'></span> ";
                        echo $flight1->destination_airport;
                        echo "<span class='glyphicon glyphicon-chevron-right'></span> ";
                        echo $flight2->destination_airport;
                    }
                    else if($transfer_times == 2){
                        echo $flight1->departure_airport;
                        echo "<span class='glyphicon glyphicon-chevron-right'></span> ";
                        echo $flight1->destination_airport;
                        echo "<span class='glyphicon glyphicon-chevron-right'></span> ";
                        echo $flight2->destination_airport;
                        echo "<span class='glyphicon glyphicon-chevron-right'></span> ";
                        echo $flight3->destination_airport;
                    }
                    echo "</div>";

                    echo "</div>"; //row
                    echo "</div>"; //panel-footer
                    echo "</tr>\n";
                    echo "</div>\n"; //panel-success
                    echo "</div>"; //flip

                    $result_count++;
                }
                echo "</table>";
            ?>

            </div>
        </div> <!--end of ticket list-->

    </body>
</html>
