<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	
	$this->get('/caster', function (Request $request, Response $response, array $args) {
		// Create database connection  and controller
		$currentDao = new DaoManager();
		$userController = new UserController($currentDao);
		
		$response->getBody()->write(json_encode($userController->getLoggedInUserName()));

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

	$this->post('/caster', function (Request $request, Response $response, array $args){
		// Create database connection and controller
		$currentDao = new DaoManager();
		$userController = new UserController($currentDao);

		$bodyData = $request->getParsedBody();
		$requestResult = $userController->createUser(["userName" => $bodyData["userName"], "password" => $bodyData["password"], "email" => $bodyData["email"]]);
		$responseBody = new StdClass;
		$responseBody->success = $requestResult;

		return $response->getBody()->write(json_encode($responseBody));;

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
	});

	$this->get('/{userName}/profile', function (Request $request, Response $response, array $args){
		// Create database connection and controller
		$currentDao = new DaoManager();
		$userController = new UserController($currentDao);
		$userContentController = new UserContentRelationController($currentDao);

		$currentUserId = $userController->getUserByUserName($args["userName"], ["userId"]);
		$resultSet = $userContentController->getUserContent($currentUserId);

		$response->getBody()->write(json_encode($resultSet));

		return $response;
	});

?>