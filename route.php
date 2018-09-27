<?php
    // Slim framework dependencies
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    require 'vendor/autoload.php';

    // TODO create an autoloader for dependencies.
    require_once "server/src/controllers/UserController.php";
    require_once "server/src/controllers/CastController.php";
    require_once "server/src/controllers/PostController.php";
    require_once "server/src/controllers/CommentController.php";
    require_once "server/src/controllers/UserContentRelation.php";
    require_once "server/src/databaseManager/ResultSetTypeEnum.php";
    require_once "server/src/utils/Enum.php";
    require_once "server/src/entities/SearchByMethodEnum.php";
    require_once "server/src/entities/OperationStatusEnum.php";
    require_once "server/src/entities/UserContentType.php";
	require_once "server/src/databaseManager/DaoManager.php";

    $app = new \Slim\App;


    $app->group('/api', function ($app) {

        require_once 'server/src/routes/users.php';
    });

//    $app->run();

    try{
        $app->run();
    } catch (Exception $e){
        echo $e;
    }
?>