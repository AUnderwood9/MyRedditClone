<?php
    session_start();
    $postArray = array();
    $titleArray = array();

    //array_push($titleArray, $_POST['postTitle']);
    //array_push($postArray, $_POST['postContent']);

    $_SESSION['titles']=$titleArray;
    $_SESSION['posts']=$postArray;

?>

<html>

    <head>
        <title>Simple Hash Table</title>
    </head>

    <body>

        <form action="postManagerTest.php" method="get">
            <label>Name: </label> <input type="text" name="name" />
            <br />
            <br />

            <label>Color: </label> <input type="text" name="color" />
            <br />
            <br />
            <input type="submit" value="submit" />
        </form>

        <a href="createPost.html" class="button">Create a post</a>
        <br />
        <a href="dBTest.php" class="button">Test DataBase connection</a>

    </body>

</html>
