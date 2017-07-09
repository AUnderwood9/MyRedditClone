<?php
    $postArray = array();
    $titleArray = array();

    array_push($titleArray, $_POST['postTitle']);
    array_push($postArray, $_POST['postContent']);

?>

<html>
    <body>
        <p>

            Your title: <?php echo $_POST['postTitle'];?>
            <br />
            <br />

            Your Content: <br />
            <?php echo $_POST['postContent']; ?>

        </p>
    </body>
</html>
