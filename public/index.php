<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use App\Config\ExceptionHandler;
use App\Config\DbInitializer;
use App\crud\CommandeCrud;
use App\crud\ProductCrud;
use App\http\HttpCode;

header('Content-type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');

$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

ExceptionHandler::registerGlobalExceptionHandler();

// Initialisation BDD
$pdo = DbInitializer::getPdoInstance();


$uri = $_SERVER['REQUEST_URI'];
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uriParts = explode('/', $uri);
$isThreePartsUri = count($uriParts) === 3;
$ProductCrud = new ProductCrud($pdo);
$CommandeCrud = new CommandeCrud($pdo);

// Affichage des produits
if ($uri === '/product' && $httpMethod === 'GET') {
    echo json_encode($ProductCrud->fetchAllProducts());
    exit;
  }

// Affichage des commandes
if ($uri === '/commande' && $httpMethod === 'GET') {
  echo json_encode($CommandeCrud->fetchAllCommandes());
  exit;
}

// Creation d'un produit
if ($uri === '/product' && $httpMethod === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
    try {
      echo json_encode($ProductCrud->create($data));
    } catch (InvalidArgumentException $e) {
      echo json_encode([ 
        'error' => $e->getMessage(),
      ]);
      http_response_code(HttpCode::UNPROCESSABLE_CONTENT);
    } finally{
      exit;
    }
  }

// Creation d'une commande
if ($uri === '/commande' && $httpMethod === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
    try {
      echo json_encode($CommandeCrud->create($data));
    } catch (InvalidArgumentException $e) {
      echo json_encode([ 
        'error' => $e->getMessage(),
      ]);
      http_response_code(HttpCode::UNPROCESSABLE_CONTENT);
    } finally{
      exit;
    }
  }

// Gestion d'erreur quand l'ID est introuvable
$resourceName = $uriParts[1];
$id = intval($uriParts[2]);
if ($id === 0) {
  http_response_code(HttpCode::BAD_REQUEST);
  echo json_encode([
    'error' => 'ID non valide'
  ]);
}

// Selectionnez un produit par son ID
if ($resourceName === 'product' && $isThreePartsUri && $httpMethod === 'GET') {
  try {
    echo json_encode($ProductCrud->fetchOneProduct($id));
    http_response_code(HttpCode::OK);
  } catch (InvalidArgumentException $e) {
    echo json_encode([ 
      'error' => $e->getMessage(),
    ]);
    http_response_code(HttpCode::NOT_FOUND);
  }finally{
    exit;
  }
}

// Selectionnez une commaned par son ID
if ($resourceName === 'commande' && $isThreePartsUri && $httpMethod === 'GET') {
  try {
    echo json_encode($CommandeCrud->fetchOneCommande($id));
    http_response_code(HttpCode::OK);
  } catch (InvalidArgumentException $e) {
    echo json_encode([ 
      'error' => $e->getMessage(),
    ]);
    http_response_code(HttpCode::NOT_FOUND);
  }finally{
    exit;
  }
}


// Modification d'un produit par son ID
if ($resourceName === 'product' && $isThreePartsUri && $httpMethod === 'PUT') {
  $data = json_decode(file_get_contents('php://input'), true);
  try {
    echo json_encode($ProductCrud->update($data,$id));
    http_response_code(HttpCode::OK);
  } catch (InvalidArgumentException $e) {
    echo json_encode([ 
      'error' => $e->getMessage(),
    ]);
    http_response_code(HttpCode::BAD_REQUEST);
  }finally{
    exit;
  }
}

// Modification d'une commande par son ID
if ($resourceName === 'commande' && $isThreePartsUri && $httpMethod === 'PUT') {
  $data = json_decode(file_get_contents('php://input'), true);
  try {
    echo json_encode($CommandeCrud->update($data,$id));
    http_response_code(HttpCode::OK);
  } catch (InvalidArgumentException $e) {
    echo json_encode([ 
      'error' => $e->getMessage(),
    ]);
    http_response_code(HttpCode::BAD_REQUEST);
  }finally{
    exit;
  }
}

// Suppression d'un produit par son ID 
if ($resourceName === 'product' && $isThreePartsUri && $httpMethod === 'DELETE') {
  try {
    echo json_encode($ProductCrud->delete($id));
    http_response_code(HttpCode::OK);
  } catch (InvalidArgumentException $e) {
    echo json_encode([ 
      'error' => $e->getMessage(),
    ]);
    http_response_code(HttpCode::BAD_REQUEST);
  }finally{
    exit;
  }
}

// Suppression d'une commande par son ID 
if ($resourceName === 'commande' && $isThreePartsUri && $httpMethod === 'DELETE') {
  try {
    echo json_encode($CommandeCrud->delete($id));
    http_response_code(HttpCode::OK);
  } catch (InvalidArgumentException $e) {
    echo json_encode([ 
      'error' => $e->getMessage(),
    ]);
    http_response_code(HttpCode::BAD_REQUEST);
  }finally{
    exit;
  }
}

