<?php
    require_once 'config.php';
    require_once 'check_page.php';
    require_once 'functions.php';
    session_start();
    session_save_path("./session");
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
        $(document).ready(function() {      
            $('.carousel').carousel('pause');
        });
        </script>
    </head>
    <title>Flight Schedule</title>
    <body bgcolor="#EEEEEE">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                <li data-target="#carousel-example-generic" data-slide-to="3"></li>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <div class="item active">
                    <div class="row">
                    <div class="col-md-12">
                    <div class="jumbotron">
                        <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <h1>Flight Schedule <small>Ticket Search</small></h1>
                        </div>
                        <div class="col-md-1">
                            <a href="#carousel-example-generic" class="btn btn-lightgreen btn-lg" data-slide-to="1">Sign in</a>
                        </div>
                        <div class="col-md-1"></div>
                        </div>
                    </div>
                        <div id="searching_bar" class="row">
                        <div class="col-md-6">
                            <?php
                                $sql = "SELECT airport.id AS airport_id,country.fullname AS country_name,airport.fullname AS airport_name".
                                       " FROM `country` JOIN `airport` ON airport.belonging_country_id = country.id".
                                       " ORDER BY country_name";
                                $find_CA1 = $db->prepare($sql);
                                $find_CA1->execute(array());
                                $find_CA2 = $db->prepare($sql);
                                $find_CA2->execute(array());
                            ?>
                            <form method="post" action="index2.php">
                            <div class="row">
                                <div class="col-md-1"></div>
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
                                <div class="col-md-1"></div>
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
                                <div class="col-md-1"></div>
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
                                        <option value="1"> arrival time</option>
                                        <option value="2"> transfer time</option>
                                    </select>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-1" align="right">
                                    <button name='search_OK' class="btn btn-darkred" type="submit" value='search_OK'><span class="glyphicon glyphicon-search"></span></button>
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div align="center">
                                <div style="height:15%"></div>
                                <a href="#carousel-example-generic" class="btn btn-lightgreen btn-lg" data-slide-to="1">Sign in</a>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="item">
                    <div class="jumbotron">
                        <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <h1>Sign in</h1>
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
                            <button type="submit" class="btn btn-lightgreen" name="sign_in" value="Sign in" />OK</button><br><br>
                            No account? <a id="sign_up" href="#carousel-example-generic" data-slide-to="2">Sign up</a><br><br>
                            <a href="#carousel-example-generic" data-slide-to="0">Back</a>
                        </form>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="item">
                    <div class="jumbotron">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <h1>Sign up</h1>
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
                    </div>
                </div>
            </div>
    </div>
    </body>
</html>




