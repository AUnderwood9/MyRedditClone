<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	// $this->get('/casts', function (Request $request, Response $response, array $args) {
    //     $response->getBody()->write("Hello");

    //     return $response;
	// });

	$this->get('/post/{method}/{id}', function (Request $request, Response $response, array $args) {
		$postController = new PostController(new DaoManager());

		$response->getBody()->write(json_encode($postController->getPosts($args["method"], $args["id"])));

        return $response;
	});

	$this->post('/post', function (Request $request, Response $response, array $args) {
		$castController = new CastController(new DaoManager());
		$userController = new UserController(new DaoManager());
		
		$requestBody = $request->getParsedBody();
		$serverResponse = $castController->createCast((array)$requestBody);
		
		$responseBody = new StdClass;
		$responseBody->success = $serverResponse;

		$response->getBody()->write(json_encode($responseBody));

        return $response;
	});
?>