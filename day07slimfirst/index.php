<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

DB::$dbName = 'day01';
DB::$user = 'day01';
DB::$password = '123uvehY(2GwX5]4';
DB::$host = 'localhost';

// Create Container
$container = new Container();
AppFactory::setContainer($container);

// Set view in Container
$container->set('view', function() {
    return Twig::create(__DIR__ .'/templates', ['cache' => __DIR__ .'/tmplcache', 'debug' => true]);
});

// Create App
$app = AppFactory::create();

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));


//URL HANDLER GOES BELOW

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/hello/{name}/{age}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $age =$args['age'];
    //$response->getBody()->write("Hello, $name you are $age y/o");
    //return $response;    
    return $this->get('view')->render($response, 'hello.html.twig', ['nameVal'=> $name, 'ageVal' => $age]);
    
});

// STATE 1: first display of the form 
$app->get('/addperson', function ($request, $response, $args) {
    return $this->get('view')->render($response, 'addperson.html.twig'); 
});

// SATE 2&3: receiving a submission
$app->post('/addperson', function ($request, $response, $args) {
    // extract values submitted
    $data = $request->getParsedBody();
    $name = $data['name'];
    $age = $data['age'];
    // validate
    $errorList = [];
    if (strlen($name) < 2 || strlen($name) > 100) {
        $errorList []= "Name must be 2-100 characters long";
        $name = "";
    }
    if (filter_var($age, FILTER_VALIDATE_INT) === false || $age < 0 || $age > 150) {
        $errorList[] = "Age must be an integer number between 0 and 150";
        $age = "";
    }
    print_r($errorList);
    //
    if ($errorList) { // STATE 2: errors
        $valuesList = ['name' => $name, 'age' => $age];
        return $this->get('view')->render($response, 'addperson.html.twig', ['errorList' => $errorList, 'v' => $valuesList]);
    } else { // STATE 3: sucess
        DB::insert('friends', ['name' => $name, 'age' => $age]);
        return $this->get('view')->render($response, 'addperson_success.html.twig');
    }
});


$app->run();