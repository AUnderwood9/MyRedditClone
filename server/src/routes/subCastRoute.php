<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	// $this->get('/casts', function (Request $request, Response $response, array $args) {
    //     $response->getBody()->write("Hello");

    //     return $response;
	// });

	$this->get('/casts', function (Request $request, Response $response, array $args) {
		$castController = new castController(new DaoManager());

		$response->getBody()->write(json_encode($castController->getCastList()));

        return $response;
	});

	$this->post('/casts', function (Request $request, Response $response, array $args) {
		$castController = new CastController(new DaoManager());
		$userController = new UserController(new DaoManager());
		
		$requestBody = $request->getParsedBody();
		$serverResponse = $castController->createCast((array)$requestBody);
		
		$responseBody = new StdClass;
		$responseBody->success = $serverResponse;

		$response->getBody()->write(json_encode($responseBody));

        return $response;
	});

	$this->post('/casts/update', function (Request $request, Response $response, array $args){
		$castController = new CastController(new DaoManager());
		$userController = new UserController(new DaoManager());

		$requestBody = $request->getParsedBody();
		$serverResponse = $castController->updateCast($requestBody["id"], (array)$requestBody["edits"]);
		
		$responseBody = new StdClass;
		$responseBody->success = $serverResponse;

		$response->getBody()->write(json_encode($responseBody));

        return $response;
	})
?>