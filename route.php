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
    require_once "server/src/controllers/UserContentRelationController.php";
    require_once "server/src/databaseManager/ResultSetTypeEnum.php";
    require_once "server/src/utils/Enum.php";
    require_once "server/src/entities/SearchByMethodEnum.php";
    require_once "server/src/entities/OperationStatusEnum.php";
    require_once "server/src/entities/UserContentType.php";
	require_once "server/src/databaseManager/DaoManager.php";

    $app = new \Slim\App;

	$app->options('/{routes:.+}', function ($request, $response, $args) {
		return $response;
	});
	
	$app->add(function ($req, $res, $next) {
		$response = $next($req, $res);
		return $response
				->withHeader('Access-Control-Allow-Origin', '*')
				->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
				->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
				->withHeader('Access-Control-Allow-Methods', 'Content-Type: application/json');
	});

    $app->group('/api', function ($app) {
		require_once 'server/src/routes/userRoute.php';
		require_once 'server/src/routes/subCastRoute.php';
		require_once 'server/src/routes/postRoute.php';
	});
	
	// Catch-all route to serve a 404 Not Found page if none of the routes match
	// NOTE: make sure this route is defined last
	$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
		$handler = $this->notFoundHandler; // handle using the default Slim page not found handler
		return $handler($req, $res);
	});

//    $app->run();

    try{
        $app->run();
    } catch (Exception $e){
        echo $e;
    }
?>