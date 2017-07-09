<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>

  <head></head>

  <body>

    <?php

    // attempt a connection

    //$dbh = pg_connect("host=localhost dbname=test user=postgres");
    $dbh = pg_connect("host=localhost port=5432 dbname=mytest user=postgres password=corvette");

    if (!$dbh) {

        die("Error in connection: " . pg_last_error());

    }

    else {
        echo "Connection Successful!";
    }


    // execute query


    //$sql = "SELECT * FROM Countries";


    //$result = pg_query($dbh, $sql);

/*
    if (!$result) {

        die("Error in SQL query: " . pg_last_error());

    }
*/
    // iterate over result set

    // print each row

    /*
    while ($row = pg_fetch_array($result)) {

        echo "Country code: " . $row[0] . "<br />";
        echo "Country name: " . $row[1] . "<p />";

    }

    */
    // free memory

    //pg_free_result($result);

    // close connection

    pg_close($dbh);

    ?>

  </body>

</html>
