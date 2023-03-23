<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

DB::$dbName = 'day07slimauction';
DB::$user = 'day07slimauction';
DB::$password = 'E5FzNt9Y-[b4UIW]';
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
    //return $this->get('view')->render($response, 'hello.html.twig', ['nameVal'=> $name, 'ageVal' => $age]); 
    $result = $this->get('view')->render($response, 'hello.html.twig', ['nameVal'=> $name, 'ageVal' => $age]);
    //Note: simulating line 36, here we cannot write  $rsponse -> $this->get('view')->render($response, 'hello.html.twig', ['nameVal'=> $name, 'ageVal' => $age]);
    // because $response is a Response object which is given by render to 'hello.html.twig with parameter $name and $age    
    return $result;   
});

//------------------------------ new auction ---------------------------------
// STATE 1: first display of the form
$app->get('/newauction', function ($request, $response, $args) {
    return $this->get('view')->render($response, 'newauction.html.twig');
});

// SATE 2&3: receiving a submission
$app->post('/newauction', function ($request, $response, $args) {
    // extract values submitted
    $data = $request->getParsedBody();
    $itemDesc=$data['itemDesc'];    
    $sellerName=$data['sellerName'];
    $sellerEmail=$data['sellerEmail'];
    $initialBid=$data['initialBid']; 

    $errors = [];

    $fileName = $_FILES['imageToUpload']['name'];  
    $acceptableFileFormats = ['jpg', 'gif', 'png', 'bmp'];
    $fileNameExplode = explode('.',$fileName); 
    $fileNameOriginal = $fileNameExplode[0];
    //echo  $fileNameOriginal;
       
    $fileExtention = end($fileNameExplode);
    $fileFormat = strtolower($fileExtention); 
    //echo $fileFormat;

    if(strlen($itemDesc)<2|| strlen($itemDesc)>1000){
        $errors[]= "Item description must be 2-1000 characters long";        
        }
        //To check file format 
        if(!in_array($fileFormat,$acceptableFileFormats)){
        $errors[]="File format of image uploaded  is not allowed, please choose a JPG or GIF or PNG or BMP file.";
        }

        if(strlen($sellerName)<2|| strlen($sellerName)>100){
        $errors[]= "Seller name must be 2-100 characters long";
        }        
        if(preg_match('/^[a-zA-Z][a-zA-Z0-9,.\s\- ]{1,99}$/', $sellerName) !=1){
        $errors[] = "Seller name  must contain only letters (upper/lower-case), space, dash, dot, comma and numbers";        
        }
        if (filter_var($sellerEmail, FILTER_VALIDATE_EMAIL) === false) {
        $errors[]="Please verify your email address, it doesn't look valid.";        
        }
        if(!is_numeric($initialBid)){
            $errors[]="Initial bid price must be a numeric value";
        }
        // $fileNameReplaced = preg_replace("/s\-.,/", "_", $fileNameOriginal); // this is not working
        $fileNameReplaced =$fileNameOriginal;
        //echo  $fileNameReplaced;
        // Create the uploads folder if missing
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) { 
        mkdir( $targetDir,0777,false);  
        }
        //print_r($errors);

        if(!$errors){ 
            if(file_exists( "uploads/".$fileNameReplaced.".".$fileFormat)){

                function generateRandomString() {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $charactersLength = strlen($characters);
                    $randomString = '';
                    for ($i = 0; $i <10; $i++) {
                        $randomString .= $characters[random_int(0, $charactersLength - 1)];
                    }
                    return $randomString;
                }
                $uniqueSuffix = generateRandomString();
                //echo $uniqueSuffix;
                $targetFileName = $fileNameReplaced.$uniqueSuffix.".".$fileFormat;  
                //echo $targetFileName;

            }else{
              $targetFileName = $fileNameReplaced.".".$fileFormat;
            }
            
            //echo($targetFileName);
            DB::insert('auctions',['itemDescription'=>$itemDesc, 'itemImagePath'=>'uploads/'.$targetFileName, 'sellerName'=>$sellerName, 'sellerEmail'=>$sellerEmail, 'lastBidPrice'=>$initialBid]);
            move_uploaded_file($_FILES['imageToUpload']['tmp_name'],'uploads/'.$targetFileName);
            return $this->get('view')->render($response, 'newauction_success.html.twig');            
        } else{            
            $valuesList = ['description'=>$itemDesc, 'seller'=>$sellerName, 'sellerEmail'=>$sellerEmail, 'initialBid'=>$initialBid];
            //print_r($valuesList);        
                     
            return $this->get('view')->render($response, 'newauction.html.twig', ['errorList' => $errors, 'v' => $valuesList]);
        } //end of isset($_POST['create']))     
});

//------------------------------ listitems.html.twig ---------------------------------
$app->get('/listitems', function ($request, $response, $args) {
    $results = DB::query("SELECT * FROM auctions ORDER BY id DESC");
    //print_r($results);
   
    // $itemList=[];    
    // foreach ($results as $row) {
    //     $itemList[]=$row;        
    //   }    
   // return $this->get('view')->render($response, 'addperson.html.twig', ['errorList' => $errorList, 'v' => $itemList[]]);
//    return $this->get('view')->render($response, 'listitems.html.twig', ['v' => $itemList]); 
    return $this->get('view')->render($response, 'listitems.html.twig', ['v' => $results]);    
});

//------------------------------ place bid ---------------------------------
$app->get('/placebid', function ($request, $response, $args) {    
    $id = $_GET['id'];    
    $result = DB::queryFirstRow("SELECT * FROM auctions WHERE id=$id");    
    //print_r($result);   
   return $this->get('view')->render($response, 'placebid.html.twig', ['v' => $result]);     
});

$app->post('/placebid', function ($request, $response, $args) {
    // extract values submitted
    $id = $_GET['id'];    
    $result = DB::queryFirstRow("SELECT * FROM auctions WHERE id=$id");
    $lastBid=$result['lastBidPrice'];
    
    $data = $request->getParsedBody();
    $bidderName = $_POST['bidderName'];
    $bidderEmail = $_POST['bidderEmail'];
    $newBidPrice = $_POST['newBidPrice'];

    $errors = [];
    if($newBidPrice<=$lastBid){
        $errors[]= "You bid price is not greater than last bid, so you cannot bid with this price";
    }

    if(strlen($bidderName)<2|| strlen($bidderName)>100){
        $errors[]= "Seller name must be 2-100 characters long";
    }        
    if(preg_match('/^[a-zA-Z][a-zA-Z0-9,.\s\- ]{1,99}$/', $bidderName) !=1){
        $errors[] = "Seller name  must contain only letters (upper/lower-case), space, dash, dot, comma and numbers";        
    }
    if (filter_var($bidderEmail, FILTER_VALIDATE_EMAIL) === false) {
        $errors[]="Please verify your email address, it doesn't look valid.";        
    }

    if($errors){ 
        $seller = $result['sellerName'];
        $lastBid = $result['lastBidPrice'];
        $imagePath = $result['itemImagePath'];
        $desc = $result['itemDescription'];

        $valuesList = ['bidder'=>$bidderName, 'email'=>$bidderEmail, 'newBid'=>$newBidPrice, 'sellerName'=>$seller, 
        'lastBidPrice'=>$lastBid, 'itemImagePath'=>$imagePath, 'itemDescription'=>$desc];
        //key text should match the text value inputs in place.html.twing                 
        return $this->get('view')->render($response, 'placebid.html.twig', ['errorList' => $errors, 'v' => $valuesList]);

    }else{
        DB::update('auctions',['lastBidPrice'=>$newBidPrice, 'lastBidderName'=>$bidderName, 'lastBidderEmail'=>$bidderEmail], 'id=%d', $id);        
        return $this->get('view')->render($response, 'newauction_success.html.twig'); 
    }
    });
$app->run();