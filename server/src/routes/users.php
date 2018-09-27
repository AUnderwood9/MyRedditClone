<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	

    $this->get('/user', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Hello");

        return $response;
	});
	
	$this->get('/caster/{user}', function (Request $request, Response $response, array $args) {
		// $userName = $args["user"];

		// Create database connection  and controller
		$currentDao = new DaoManager();
		$userController = new UserController($currentDao);
		
		$response->getBody()->write(json_encode($userController->getUserByUserName($args["user"])));

        return $response;
	});

	$this->post('/login', function (Request $request, Response $response, array $args){
		// Create database connection and controller
		$currentDao = new DaoManager();
		$userController = new UserController($currentDao);

		$bodyResult = $request->getParsedBody();
		$requestResult = $userController->loginUser($bodyResult["userName"], $bodyResult["password"]);

		return $requestResult;

	});
?>