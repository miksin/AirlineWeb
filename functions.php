<?php
    require_once 'config.php';

function timezone_transform($database_time){
    $hour = substr($database_time,0,2);
    $min = substr($database_time,3,2);

/*    if(($hour>12) || ($hour==12 && $min>0)){
        if($min == 0){
            $timezone = '-'.str_pad((24-$hour),2,'0',STR_PAD_LEFT).':00';
        }
        else {
            $timezone = '-'.str_pad((23-$hour),2,'0',STR_PAD_LEFT).':'.str_pad((60-$min),2,'0',STR_PAD_LEFT);
        }
    }
*/
    if($hour >= 12){
        $timezone = '+'.str_pad(($hour-12),2,'0',STR_PAD_LEFT).':'.str_pad($min,2,'0',STR_PAD_LEFT);
    }
    else {
        if($min == 0){
            $timezone = '-'.str_pad((12-$hour),2,'0',STR_PAD_LEFT).':00';
        }
        else {
            $timezone = '-'.str_pad((11-$hour),2,'0',STR_PAD_LEFT).':'.str_pad((60-$min),2,'0',STR_PAD_LEFT);
        }
    }
    return $timezone;
}

function reverse_timezone_transform($timezone){
    $sign = substr($timezone,0,1);
    $hour = substr($timezone,1,2);
    $min = substr($timezone,4,2);
/*
    if($sign == '-'){
        if($min == 0){
            $database_time = str_pad((24-$hour),2,'0',STR_PAD_LEFT).':00:00';
        }
        else {
            $database_time = str_pad((23-$hour),2,'0',STR_PAD_LEFT).':'.str_pad((60-$min),2,'0',STR_PAD_LEFT).':00';
        }
    }
*/
    if($sign == '+'){
        $database_time = str_pad(($hour+12),2,'0',STR_PAD_LEFT).':'.str_pad($min,2,'0',STR_PAD_LEFT).':00';
    }
    else {
        if($min == 0){
            $database_time = str_pad((12-$hour),2,'0',STR_PAD_LEFT).':00:00';
        }
        else {
            $database_time = str_pad((11-$hour),2,'0',STR_PAD_LEFT).':'.str_pad((60-$min),2,'0',STR_PAD_LEFT).':00';
        }
    }

    return $database_time;
}


function transferSQL($transfer_time,$orderby){
    if($transfer_time == '0'){
        $sql = <<<__SQL__
SELECT flight.id AS flight_number1,
       NULL AS flight_number2,
       NULL AS flight_number3,
       flight.departure_date AS departure_time,
       flight.arrival_date AS arrival_time,
       ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flying_time,
       ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flight_time,
       '00:00:00' AS transfer_time,
       flight.price AS price
FROM ((flight JOIN airport AS Departure_airport ON Departure_airport.id = flight.departure_id) 
       JOIN airport AS Destination_airport ON Destination_airport.id = flight.destination_id)
WHERE flight.departure_id = :did AND flight.destination_id = :aid
  AND NOT flight.departure_id = flight.destination_id
__SQL__;
    $sql.=" ORDER BY ".$orderby." ASC";

        return $sql;
    }
    else if($transfer_time == '1'){
        $sql = <<<__SQL__
SELECT union_table.flight_number1 AS flight_number1,
       union_table.flight_number2 AS flight_number2,
       union_table.flight_number3 AS flight_number3,
       union_table.departure_time AS departure_time,
       union_table.arrival_time AS arrival_time,
       union_table.flying_time AS flying_time,
       union_table.flight_time AS flight_time,
       union_table.transfer_time AS transfer_time,
       union_table.price AS price

FROM (
         (
            SELECT flight.id AS flight_number1,
                   NULL AS flight_number2,
                   NULL AS flight_number3,
                   flight.departure_date AS departure_time,
                   flight.arrival_date AS arrival_time,
                   ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flying_time,
                   ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flight_time,
                   '00:00:00' AS transfer_time,
                   flight.price AS price
            FROM ((flight JOIN airport AS Departure_airport ON Departure_airport.id = flight.departure_id) 
                   JOIN airport AS Destination_airport ON Destination_airport.id = flight.destination_id)
            WHERE flight.departure_id = :did AND flight.destination_id = :aid
              AND NOT flight.departure_id = flight.destination_id
         ) UNION
         (
            SELECT flight1.id AS flight_number1,
                   flight2.id AS flight_number2,
                   NULL AS flight_number3,
                   
                   flight1.departure_date AS departure_time,
                   flight2.arrival_date AS arrival_time,
                   
                   ADDTIME(
                   ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)) 
                   ) AS flying_time,
                   
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport2.timezone)) 
                   AS flight_time,
                   
                   TIMEDIFF(
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport2.timezone)),
                   ADDTIME(
                   ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)))
                   ) AS transfer_time,
                   
                   (flight1.price + flight2.price)*0.9 AS price

            FROM flight AS flight1,
                 flight AS flight2,
                 airport AS Departure_airport1,
                 airport AS Destination_airport1,
                 airport AS Departure_airport2,
                 airport AS Destination_airport2
            WHERE Departure_airport1.id = flight1.departure_id
             AND  Destination_airport1.id = flight1.destination_id
             AND  Departure_airport2.id = flight2.departure_id
             AND  Destination_airport2.id = flight2.destination_id

             AND flight2.departure_date > ADDTIME(flight1.arrival_date,'01:59:59')

             AND  flight1.departure_id = :did
             AND  flight1.destination_id = flight2.departure_id
             AND  flight2.destination_id = :aid
             AND  NOT flight1.destination_id = flight1.departure_id
             AND  NOT flight2.destination_id = flight2.departure_id
             AND  NOT flight2.destination_id = flight1.departure_id
         )
     ) AS union_table
__SQL__;
    $sql.=" ORDER BY ".$orderby." ASC";
        
        return $sql;
    }
    else if($transfer_time == '2'){
        $sql = <<<__SQL__
SELECT union_table.flight_number1 AS flight_number1,
       union_table.flight_number2 AS flight_number2,
       union_table.flight_number3 AS flight_number3,
       union_table.departure_time AS departure_time,
       union_table.arrival_time AS arrival_time,
       union_table.flying_time AS flying_time,
       union_table.flight_time AS flight_time,
       union_table.transfer_time AS transfer_time,
       union_table.price AS price

FROM (
         (
            SELECT flight.id AS flight_number1,
                   NULL AS flight_number2,
                   NULL AS flight_number3,
                   flight.departure_date AS departure_time,
                   flight.arrival_date AS arrival_time,
                   ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flying_time,
                   ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flight_time,
                   '00:00:00' AS transfer_time,
                   flight.price AS price
            FROM ((flight JOIN airport AS Departure_airport ON Departure_airport.id = flight.departure_id) 
                   JOIN airport AS Destination_airport ON Destination_airport.id = flight.destination_id)
            WHERE flight.departure_id = :did AND flight.destination_id = :aid
              AND NOT flight.departure_id = flight.destination_id
         ) UNION
         (
            SELECT flight1.id AS flight_number1,
                   flight2.id AS flight_number2,
                   NULL AS flight_number3,
                   
                   flight1.departure_date AS departure_time,
                   flight2.arrival_date AS arrival_time,
                   
                   ADDTIME(
                   ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)) 
                   ) AS flying_time,
                   
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport2.timezone)) 
                   AS flight_time,
                   
                   TIMEDIFF(
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport2.timezone)),
                   ADDTIME(
                   ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)))
                   ) AS transfer_time,
                   
                   (flight1.price + flight2.price)*0.9 AS price

            FROM flight AS flight1,
                 flight AS flight2,
                 airport AS Departure_airport1,
                 airport AS Destination_airport1,
                 airport AS Departure_airport2,
                 airport AS Destination_airport2
            WHERE Departure_airport1.id = flight1.departure_id
             AND  Destination_airport1.id = flight1.destination_id
             AND  Departure_airport2.id = flight2.departure_id
             AND  Destination_airport2.id = flight2.destination_id

             AND flight2.departure_date > ADDTIME(flight1.arrival_date,'01:59:59')

             AND  flight1.departure_id = :did
             AND  flight1.destination_id = flight2.departure_id
             AND  flight2.destination_id = :aid
             AND  NOT flight1.destination_id = flight1.departure_id
             AND  NOT flight2.destination_id = flight2.departure_id
             AND  NOT flight2.destination_id = flight1.departure_id
         ) UNION
         (
            SELECT flight1.id AS flight_number1,
                   flight2.id AS flight_number2,
                   flight3.id AS flight_number3,
                   
                   flight1.departure_date AS departure_time,
                   flight3.arrival_date AS arrival_time,
                   
                   ADDTIME(
                       ADDTIME(
                           ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
                           ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)) 
                       ),
                       ADDTIME(TIMEDIFF(flight3.arrival_date,flight3.departure_date),TIMEDIFF(Departure_airport3.timezone,Destination_airport3.timezone)) 
                   ) AS flying_time,
                   
                   ADDTIME(TIMEDIFF(flight3.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport3.timezone)) 
                   AS flight_time,
                   
                   TIMEDIFF(
                       ADDTIME(TIMEDIFF(flight3.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport3.timezone)), 
                       ADDTIME(
                           ADDTIME(
                               ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
                               ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)) 
                           ),
                           ADDTIME(TIMEDIFF(flight3.arrival_date,flight3.departure_date),TIMEDIFF(Departure_airport3.timezone,Destination_airport3.timezone)) 
                       )
                   ) AS transfer_time,
                   
                   (flight1.price + flight2.price + flight3.price)*0.8 AS price

            FROM flight AS flight1,
                 flight AS flight2,
                 flight AS flight3,
                 airport AS Departure_airport1,
                 airport AS Destination_airport1,
                 airport AS Departure_airport2,
                 airport AS Destination_airport2,
                 airport AS Departure_airport3,
                 airport AS Destination_airport3

            WHERE Departure_airport1.id = flight1.departure_id
             AND  Destination_airport1.id = flight1.destination_id
             AND  Departure_airport2.id = flight2.departure_id
             AND  Destination_airport2.id = flight2.destination_id
             AND  Departure_airport3.id = flight3.departure_id
             AND  Destination_airport3.id = flight3.destination_id

             AND flight2.departure_date > ADDTIME(flight1.arrival_date,'01:59:59')
             AND flight3.departure_date > ADDTIME(flight2.arrival_date,'01:59:59')

             AND  flight1.departure_id = :did
             AND  flight1.destination_id = flight2.departure_id
             AND  flight2.destination_id = flight3.departure_id
             AND  flight3.destination_id = :aid
             AND  NOT flight1.destination_id = flight1.departure_id
             AND  NOT flight2.destination_id = flight2.departure_id
             AND  NOT flight3.destination_id = flight3.departure_id
             AND  NOT flight3.departure_id = flight1.departure_id
             AND  NOT flight3.destination_id = flight1.destination_id
         )
     ) AS union_table
__SQL__;
    $sql.=" ORDER BY ".$orderby." ASC";

        return $sql;
    }
}

function print_ticketSQL($transfer_times , $flight_id){
    global $db;

    if($transfer_times == 0){
        $sql = <<<__SQL__
SELECT flight.id AS flight1_id,
       flight.departure_date AS departure_time,
       flight.arrival_date AS arrival_time,
       ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS total_time,
       '00:00:00' AS transfer_time,
       ADDTIME(TIMEDIFF(flight.arrival_date,flight.departure_date),TIMEDIFF(Departure_airport.timezone,Destination_airport.timezone)) AS flight_time,
       flight.price AS total_price
FROM flight,airport AS Departure_airport,airport AS Destination_airport
WHERE flight.id = :fid1
AND  flight.departure_id = Departure_airport.id
AND  flight.destination_id = Destination_airport.id
ORDER BY total_price
__SQL__;

        $find_tickets = $db->prepare($sql);
        $find_tickets->execute(array(
            ':fid1' => $flight_id[0]
            ));

        return $find_tickets->fetchObject();
    }
    else if($transfer_times == 1){
        $sql = <<<__SQL__
SELECT flight1.id AS flight1_id,
       flight2.id AS flight2_id,
       flight1.departure_date AS departure_time,
       flight2.arrival_date AS arrival_time,
       ADDTIME(TIMEDIFF(flight2.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport2.timezone)) AS total_time,
       TIMEDIFF(
           ADDTIME(TIMEDIFF(flight2.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport2.timezone)),
           ADDTIME(
               ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
               ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)))
       ) AS transfer_time,
       ADDTIME(
           ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
           ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)) 
       ) AS flight_time,
       (flight1.price + flight2.price)*0.9 AS total_price

FROM flight AS flight1,
     flight AS flight2,
     airport AS Departure_airport1,
     airport AS Destination_airport1,
     airport AS Departure_airport2,
     airport AS Destination_airport2
        
WHERE Departure_airport1.id = flight1.departure_id
 AND  Destination_airport1.id = flight1.destination_id
 AND  Departure_airport2.id = flight2.departure_id
 AND  Destination_airport2.id = flight2.destination_id
 AND  flight1.id = :fid1
 AND  flight2.id = :fid2
ORDER BY total_price
__SQL__;

        $find_tickets = $db->prepare($sql);
        $find_tickets->execute(array(
            ':fid1' => $flight_id[0],
            ':fid2' => $flight_id[1]
            ));

        return $find_tickets->fetchObject();
    }
    else if($transfer_times == 2){
        $sql = <<<__SQL__
SELECT flight1.id AS flight1_id,
       flight2.id AS flight2_id,
       flight3.id AS flight3_id,
       flight1.departure_date AS departure_time,
       flight3.arrival_date AS arrival_time,
       ADDTIME(TIMEDIFF(flight3.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport3.timezone)) 
       AS total_time,
       ADDTIME(
           ADDTIME(
               ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
               ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)) 
           ),
           ADDTIME(TIMEDIFF(flight3.arrival_date,flight3.departure_date),TIMEDIFF(Departure_airport3.timezone,Destination_airport3.timezone)) 
       ) AS flight_time,
       TIMEDIFF(
           ADDTIME(TIMEDIFF(flight3.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport3.timezone)), 
           ADDTIME(
               ADDTIME(
                   ADDTIME(TIMEDIFF(flight1.arrival_date,flight1.departure_date),TIMEDIFF(Departure_airport1.timezone,Destination_airport1.timezone)),
                   ADDTIME(TIMEDIFF(flight2.arrival_date,flight2.departure_date),TIMEDIFF(Departure_airport2.timezone,Destination_airport2.timezone)) 
               ),
               ADDTIME(TIMEDIFF(flight3.arrival_date,flight3.departure_date),TIMEDIFF(Departure_airport3.timezone,Destination_airport3.timezone)) 
           )
       ) AS transfer_time,
       (flight1.price + flight2.price + flight3.price)*0.8 AS total_price
FROM flight AS flight1,
     flight AS flight2,
     flight AS flight3,
     airport AS Departure_airport1,
     airport AS Destination_airport1,
     airport AS Departure_airport2,
     airport AS Destination_airport2,
     airport AS Departure_airport3,
     airport AS Destination_airport3

WHERE Departure_airport1.id = flight1.departure_id
 AND  Destination_airport1.id = flight1.destination_id
 AND  Departure_airport2.id = flight2.departure_id
 AND  Destination_airport2.id = flight2.destination_id
 AND  Departure_airport3.id = flight3.departure_id
 AND  Destination_airport3.id = flight3.destination_id
 AND  flight1.id = :fid1
 AND  flight2.id = :fid2
 AND  flight3.id = :fid3
ORDER BY total_price
__SQL__;

        $find_tickets = $db->prepare($sql);
        $find_tickets->execute(array(
            ':fid1' => $flight_id[0],
            ':fid2' => $flight_id[1],
            ':fid3' => $flight_id[2]
            ));

        return $find_tickets->fetchObject();
    }

}

?>
