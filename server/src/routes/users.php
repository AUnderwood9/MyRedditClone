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

		$bodyData = $request->getParsedBody();
		$requestResult = $userController->loginUser($bodyData["userName"], $bodyData["password"]);

		return $requestResult;

	});

	$this->get('/loginStatus/{userName}', function (Request $request, Response $response, array $args){
		// Create database connection and controller
		$currentDao = new DaoManager();
		$userController = new UserController($currentDao);

		$response->getBody()->write(json_encode($userController->isLoggedIn($args["userName"])));

		return $response;
	});

	$this->post('/logout', function (Request $request, Response $response, array $args){
		// Create database connection and controller
		$currentDao = new DaoManager();
		$userController = new UserController($currentDao);

		$requestResult = $userController->logoutUser();

		return $requestResult;
	})

?>