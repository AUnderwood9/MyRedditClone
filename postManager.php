<?php

    session_start();

    $dbh = pg_connect("host=localhost port=5432 dbname=mytest user=postgres password=corvette");

    if (!$dbh) {

        die("Error in connection: " . pg_last_error());

    }

    else {
        echo "Connection Successful!";
    }
    $usr = 'test';

    //$update_arr = array('user'=>'test', 'date'=>'?', 'title'=>$_POST['postTitle'], 'description'=>$_POST['postContent'] );

    // This is safe, since $_POST is converted automatically
    //$res = pg_update($db, 'post_log', $_POST, $data);
    $res = pg_query_params($dbh, "INSERT INTO post_test VALUES($1, current_date, $2, $3, 1);", array('test', $_POST['postTitle'], $_POST['postContent']));
    if ($res)
    {
        echo "Data is updated: $res\n";
    }
    else
    {
        echo "User must have sent wrong inputs\n";
    }

    //array_push($_SESSION['titles'], $_POST['postTitle']);
    //array_push($_SESSION['posts'], $_POST['postContent']);

?>

<html>
    <body>
        <p>

            <?php
            /*
                $dbh = pg_connect("host=localhost port=5432 dbname=mytest user=postgres password=corvette");

                if (!$dbh) {

                    die("Error in connection: " . pg_last_error());

                }

                else {
                    echo "Connection Successful!";
                }
                */
                for ($i=0; $i < count($_SESSION['titles']); $i++) {

                    $currentTitle = $_SESSION['titles'][$i];
                    $currentPost = $_SESSION['posts'][$i];

                    echo "<p>
                        $currentTitle
                        <br />
                        <br />
                        $currentPost
                    </p>";
                }

            ?>

            <a href="createPost.html">Add another post?</a>

        </p>
    </body>
</html>
