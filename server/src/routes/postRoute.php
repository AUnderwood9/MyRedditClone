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
		$postController = new PostController(new DaoManager());
		
		$requestBody = $request->getParsedBody();
		// var_dump($requestBody);
		$serverResponse = $postController->createPost($requestBody["id"], (array)$requestBody["insertRecord"]);
		$responseBody = new StdClass;
		$responseBody->success = $serverResponse;

		$response->getBody()->write(json_encode($responseBody));

        return $response;
	});

	$this->post('/post/update', function (Request $request, Response $response, array $args) {
		$postController = new PostController(new DaoManager());
		
		$requestBody = $request->getParsedBody();
		// var_dump($requestBody);
		$serverResponse = $postController->editPostDescription($requestBody["id"], $requestBody["edit"]);
		$responseBody = new StdClass;
		$responseBody->success = $serverResponse;

		$response->getBody()->write(json_encode($responseBody));

        return $response;
	});

	$this->post('/post/setaffinity', function (Request $request, Response $response, array $args) {
		$postController = new PostController(new DaoManager());
		
		$requestBody = $request->getParsedBody();
		// var_dump($requestBody);
		$serverResponse = $postController->setUserPostAffinity($requestBody["castId"], $requestBody["postId"],$requestBody["userId"], $requestBody["affinity"]);
		$responseBody = new StdClass;
		$responseBody->success = $serverResponse;

		$response->getBody()->write(json_encode($responseBody));

        return $response;
	});
?>