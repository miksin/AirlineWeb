<?php
    require_once 'config.php';
    require_once 'check_page.php';
    require_once 'functions.php';
    session_start();
    session_save_path("./session");

    $username = if_login();
    $user_authority = $_SESSION['identity'];
    $user_information = get_user();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
        <script src="bootstrap/js/jquery-1.11.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.js"></script>
        <script>
        $(document).ready(function(){
            $('div[itype="flip"] div:last-child').hide();
            $('div[itype="flip"] div:first-child').click(function(){
                var src = '#'+ $(this).parent().attr('id') +' div:last-child';
                $(src).slideToggle("slow");
            });
        });
        $(document).on('click','div[stype="unbuy"]',function(){
            var button_place = '#' + $(this).parent().parent().parent().parent().parent().attr('id') + ' div[stype="buy_button"]';
            var f1 = $(this).parent().parent().parent().parent().parent().attr('fid1');
            var f2 = $(this).parent().parent().parent().parent().parent().attr('fid2');
            var f3 = $(this).parent().parent().parent().parent().parent().attr('fid3');
            $.ajax({
                url:'shopping.php',
                data:"shopping=" + 'buy' +
                    "&flight1=" + f1 +
                    "&flight2=" + f2 +
                    "&flight3=" + f3 ,
                type:'POST',
                dataType:'html',
                success:function(result){
                    $(button_place).html(result);
                }
            });
        });
        $(document).on('click','div[stype="buyed"]',function(){
            var button_place = '#' + $(this).parent().parent().parent().parent().parent().attr('id') + ' div[stype="buy_button"]';
            var src = $(this).attr('sid');
            $.ajax({
                url:'shopping.php',
                data:"shopping=" + 'unbuy' +
                    "&target_id=" + src ,
                type:'POST',
                dataType:'html',
                success:function(result){
                    $(button_place).html(result);
                }
            });
        });
        <?php
        if($_POST['search_OK'] == 'search_OK'){
            echo <<<__JAVASCRIPT__
        $(document).ready(function(){
            $('#carousel-example-generic').carousel(3);
        });
__JAVASCRIPT__;
        }
        ?>
        </script>
    </head>
    <title>Flight Schedule</title>
    <body bgcolor="#EEEEEE">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <?php
                    if(!$_SESSION['login_message'] && !$_SESSION['reg_message'])
                        echo "<div class='item active'>";
                    else
                        echo "<div class='item'>";
                ?>
                    <div class="row">
                    <div class="col-md-12">
                    <div class="jumbotron">
                        <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <h1>Flight Schedule <small>Ticket Search</small></h1>
                        </div>
                        <div class="col-md-1">
                        <?php
                            if($username === '_nologin_')
                                echo "<a href='#carousel-example-generic' class='btn btn-fenlv btn-lg' data-slide-to='1'>Sign in</a>";
                            else
                                echo "<a href='logout.php' class='btn btn-darkred btn-lg'>Logout</a>";
                        ?>
                        </div>
                        <div class="col-md-1"></div>
                        </div>
                    </div>
                        <div id="searching_bar" class="row">
                        <div class="col-md-8">
                            <?php
                                $sql = "SELECT airport.id AS airport_id,country.fullname AS country_name,airport.fullname AS airport_name".
                                       " FROM `country` JOIN `airport` ON airport.belonging_country_id = country.id".
                                       " ORDER BY country_name";
                                $find_CA1 = $db->prepare($sql);
                                $find_CA1->execute(array());
                                $find_CA2 = $db->prepare($sql);
                                $find_CA2->execute(array());
                            ?>
                            <form method="post" action="index.php">
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                    <span class="label label-lightblue">Departure Airport : </span><br>
                                    <select class="form-control" name="departure_airport" size="1">
                                    <?php
                                        $country_name = '';
                                        while($country_airport = $find_CA1->fetchObject()){
                                            if($country_airport->country_name != $country_name){
                                                echo "<option disabled> </option>";
                                                echo "<option disabled>"."-- ".$country_airport->country_name."</option>";
                                                $country_name = $country_airport->country_name;
                                            }
                                            echo "<option value='".$country_airport->airport_id."'>".$country_airport->airport_name."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                    <span class="label label-lightblue">Destination Airport : </span><br>
                                    <select class="form-control" name="destination_airport" size="1">
                                    <?php
                                        $country_name = '';
                                        while($country_airport = $find_CA2->fetchObject()){
                                            if($country_airport->country_name != $country_name){
                                                echo "<option disabled> </option>";
                                                echo "<option disabled>"."-- ".$country_airport->country_name."</option>";
                                                $country_name = $country_airport->country_name;
                                            }
                                            echo "<option value='".$country_airport->airport_id."'>".$country_airport->airport_name."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-4">
                                    <span class="label label-primary">Transfer times : </span><br>
                                    <select class="form-control" name="transfer_times" size="1">
                                        <option value="0"> 0</option>
                                        <option value="1"> 1</option>
                                        <option value="2"> 2</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <span class="label label-primary">Order by : </span><br>
                                    <select class="form-control" name="ticket_orderby" size="1">
                                        <option value="0"> price</option>
                                        <option value="5"> departure time</option>
                                        <option value="1"> arrival time</option>
                                        <option value="2"> transfer time</option>
                                        <option value="3"> flight time</option>
                                        <option value="4"> total time</option>
                                    </select>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-3" align="right">
                                    <a href="#carousel-example-generic" class="btn btn-black btn-sm" data-slide-to="3">Recent search</a>
                                </div>
                                <div class="col-md-1" align="right">
                                    <button name='search_OK' class="btn btn-darkred" type="submit" value='search_OK'><span class="glyphicon glyphicon-search"></span></button>
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                        <?php 
                            if(!($username === '_nologin_'))
                                echo <<<__HTML__
                        <div class="jumbotron">
                            <h3>Welcome, $username</h3>
                            Go to <a href="view_flight_{$user_authority}.php">My page</a>
                        </div>
__HTML__;
                        ?>
                        </div>
                        </div>
                    </div>
                    </div>
                </div><!--end of page 0-->
                <?php
                    if($_SESSION['login_message'])
                        echo "<div class='item active'>";
                    else
                        echo "<div class='item'>";
                ?>
                    <div class="jumbotron">
                        <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <h1>Flight Schedule <small>Sign in</small></h1>
                        </div>
                        <div class="col-md-1"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-4">
                        <?php  
                            echo "<font color='#FF0000'>".$_SESSION['login_message']."</font>";
                            unset($_SESSION['login_message']);
                        ?>
                        <br><form name="form" method="post" action="signing.php">
                            Account:<br><input class="form-control" type="text" name="email" value="<?php echo $_SESSION['input_email']; unset($_SESSION['input_email']);?>"/><br>
                            password:<br><input class="form-control" type="password" name="pw" /><br><br>
                            <button type="submit" class="btn btn-fenlv" name="sign_in" value="Sign in" />OK</button><br><br>
                            No account? <a id="sign_up" href="#carousel-example-generic" data-slide-to="2">Sign up</a><br><br>
                            <a href="#carousel-example-generic" data-slide-to="0">Back</a>
                        </form>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div> <!--end of page 1-->
                <?php
                    if($_SESSION['reg_message'])
                        echo "<div class='item active'>";
                    else
                        echo "<div class='item'>";
                ?>
                    <div class="jumbotron">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <h1>Flight Schedule <small>Sign up</small></h1>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-4">
                            <?php
                                echo "<font color='#FF0000'>".$_SESSION['reg_message']."</font>";
                                unset($_SESSION['reg_message']);
                            ?>
                            <br><form name="form" method="post" action="signup.php">
                                Account:<br><input type="text" class="form-control" name="email" value='<?php echo $_SESSION['input_email']; unset($_SESSION['input_email']);?>'/><br>
                                password:<br><input type="password" class="form-control" name="pw" /><br>
                                password confirmation:<br><input type="password" class="form-control" name="pw_" /><br><br>
                                <button type="submit" class="btn btn-fenlv" name="sign_up" value="Sign up" />OK</button><br><br>
                                <a href="#carousel-example-generic" data-slide-to="1">Back</a>
                            </form>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                    </div> <!--end of page 2-->
                    
                    <div class='item'>
                    <div class="jumbotron">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <h1>Flight Schedule <small>Search result</small></h1>
                            </div>
                            <div class="col-md-1">
                                <a href="#carousel-example-generic" class="btn btn-black btn-lg" data-slide-to="0">Back</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10" align="right">
                            <a class="btn btn-fenlv" data-toggle="modal" data-target="#SQL">SQL</a>
                            <?php 
                                if($username)
                                    echo '<a class="btn btn-pink" href="shoppinglist.php">My Shoppinglist</a>';
                            ?>
                        <div class="col-md-1"></div>
                        </div>
                    </div><br>
                    <div id="search_result" class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <?php
                                if(!is_numeric($_POST['ticket_orderby']) ||
                                   !is_numeric($_POST['transfer_times']) || 
                                   !is_numeric($_POST['departure_airport']) || 
                                   !is_numeric($_POST['destination_airport'])){
                                    unset($_POST['departure_airport']);
                                    unset($_POST['destination_airport']);
                                    unset($_POST['ticket_orderby']);
                                    unset($_POST['transfer_times']);
                                }
                                $orderby = array('price','arrival_time','transfer_time','flying_time','flight_time','departure_time');
                                $sql = transferSQL($_POST['transfer_times'],$orderby[$_POST['ticket_orderby']]);
                                $printsql = $sql;
                                $printsql = str_replace(':did',$_POST['departure_airport'],$printsql);
                                $printsql = str_replace(':aid',$_POST['destination_airport'],$printsql);

                                $result_count = 1;
                                $sth = $db->prepare($sql);
                                $ggg = $sth->execute(array(
                                            ':did' => $_POST['departure_airport'],
                                            ':aid' => $_POST['destination_airport']
                                            ));
                                echo "<div id='test'></div>";
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
                                echo "<td>Total price</td>\n";
                                echo "</tr></table></tr>\n";
                                while($result = $sth->fetchObject()){
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
                                    $find_flight1->execute(array(':id' => $result->flight_number1));
                                    $flight1 = $find_flight1->fetchObject();
                                    $transfer_times = 0;
                                    if($result->flight_number2){
                                        $transfer_times = 1;
                                        $find_flight2 = $db->prepare($sql1);
                                        $find_flight2->execute(array(':id' => $result->flight_number2));
                                        $flight2 = $find_flight2->fetchObject();
                                    }

                                    if($result->flight_number3){
                                        $transfer_times = 2;
                                        $find_flight3 = $db->prepare($sql1);
                                        $find_flight3->execute(array(':id' => $result->flight_number3));
                                        $flight3 = $find_flight3->fetchObject();
                                    }
                                
                                    echo "<div id='".$result_count."' itype='flip'
                                               fid1='".$result->flight_number1."' 
                                               fid2='".$result->flight_number2."' 
                                               fid3='".$result->flight_number3."' 
                                               >";
                                    echo "<div><tr>\n"; 
                                    echo "<a href='#' class='btn btn-block btn-blue btn-xs btn-justified'><table class='table'><tr>\n";
                                    echo "<td width='40'>".$result_count."</td>\n";
                                    echo "<td width='100'>".$transfer_times."</td>\n";
                                    echo "<td width='220'>".$result->departure_time."</td>\n";            
                                    echo "<td width='220'>".$result->arrival_time."</td>\n";            
                                    echo "<td width='100'>".$result->flight_time."</td>\n";
                                    echo "<td width='100'>".$result->flying_time."</td>\n";
                                    echo "<td width='100'>".$result->transfer_time."</td>\n";
                                    echo "<td width='100'>".number_format(($result->transfer_time/$result->flight_time)*100,2)."%</td>\n";
                                    echo "<td>".(int)$result->price."</td>\n";
                                    echo "</tr></table></a>\n";
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

                                    if($username != '_nologin_'){
                                        if($result->flight_number3){
                                            $sql = <<<__SQL__
                                            SELECT * FROM shoppinglist
                                            WHERE user_id = :ud
                                            AND flight1_id = :f1id
                                            AND flight2_id = :f2id
                                            AND flight3_id = :f3id
__SQL__;
                                            $find_ticket = $db->prepare($sql);
                                            $find_ticket->execute(array(
                                                ':ud' => $user_information->id,
                                                ':f1id' => $result->flight_number1,
                                                ':f2id' => $result->flight_number2,
                                                ':f3id' => $result->flight_number3  ));
                                        }
                                        else if($result->flight_number2){
                                            $sql = <<<__SQL__
                                            SELECT * FROM shoppinglist
                                            WHERE user_id = :ud
                                            AND flight1_id = :f1id
                                            AND flight2_id = :f2id
                                            AND flight3_id is NULL
__SQL__;
                                            $find_ticket = $db->prepare($sql);
                                            $find_ticket->execute(array(
                                                ':ud' => $user_information->id,
                                                ':f1id' => $result->flight_number1,
                                                ':f2id' => $result->flight_number2));
                                        }
                                        else {
                                            $sql = <<<__SQL__
                                            SELECT * FROM shoppinglist
                                            WHERE user_id = :ud
                                            AND flight1_id = :f1id
                                            AND flight2_id is NULL
                                            AND flight3_id is NULL
__SQL__;
                                            $find_ticket = $db->prepare($sql);
                                            $find_ticket->execute(array(
                                                ':ud' => $user_information->id,
                                                ':f1id' => $result->flight_number1));
                                        }
                                        echo "<div class='col-md-2' stype='buy_button'>";
                                        if($ticket = $find_ticket->fetchObject()){
                                            echo "<div stype='buyed' sid='".$ticket->id."'>
                                                  <a href='#' class='btn btn-sm btn-primary'>
                                                  <span class='glyphicon glyphicon-shopping-cart'></span> Delete from shopping list
                                                  </a>
                                                  </div>";
                                        }
                                        else {
                                            echo "<div stype='unbuy'>
                                                  <a href='#' class='btn btn-sm btn-pink'>
                                                  <span class='glyphicon glyphicon-shopping-cart'></span> Add to shopping list
                                                  </a>
                                                  </div>";
                                        }
                                        echo "</div>";
                                    }

                                    echo "</div>"; //row
                                    echo "</div>"; //panel-footer
                                    echo "</tr>\n";
                                    echo "</div>\n"; //panel-success
                                    echo "</div>"; //flip

                                    $result_count = $result_count + 1;
                                }
                                echo "</table>";
                            ?>
                        </div>
                    </div><!--end of page3-->
                </div>
            </div>
        </div>

        <div class="modal fade" id="SQL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">SQL</h4>
                    </div>
                    <div class="modal-body">
                        <?= $printsql ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </body>
</html>




