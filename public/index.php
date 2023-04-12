<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use App\Config\ExceptionHandler;
use App\Config\DbInitializer;

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
$isItemOperation = count($uriParts) === 3;

if ($uri === '/product') {
  $data = json_decode(file_get_contents("php://input"), true);
  if ($httpMethod === 'GET') {
    $query = "SELECT * FROM product";

    $stmt = $pdo->prepare($query);

    try {
      $stmt->execute([]);
      $products = $stmt->fetchAll();
      echo json_encode($products);
    } catch (PDOException $e) {
      var_dump($e);
    } finally{
      exit;
    }
  }
  if ($httpMethod === 'POST') {
    $query = "INSERT INTO product VALUES (null, :name, :price, :description)";

    $stmt = $pdo->prepare($query);

    try {
      $stmt->execute([
        'name' => $data['name'],
        'price' => $data['price'],
        'description' => $data['description']
      ]);
      http_response_code(500);
    } catch (PDOException $e) {
      var_dump($e);
    } finally{
      exit;
    }
  }
}

// Gestion d'erreur quand l'ID est introuvable
$resourceName = $uriParts[1];
$id = intval($uriParts[2]);
if ($id === 0) {
  http_response_code(400);
  echo json_encode([
    'error' => 'ID non valide'
  ]);
  exit;
}

// Selectionnez un produit par son ID
if ($resourceName === 'product' && $isItemOperation && $httpMethod === 'GET') {
  $query = "SELECT * FROM product WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute(['id' => $id]);

  $product = $stmt->fetch();

  if ($product === false) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Produit non trouvé'
    ]);
    exit;
  }

  echo json_encode($product);
}


// Modification des produits
if ($resourceName === 'product' && $isItemOperation && $httpMethod === 'PUT') {
  $data = json_decode(file_get_contents('php://input'), true);

  if (!isset($data['name']) || !isset($data['price'])) {
    http_response_code(422);
    echo json_encode([
      'error' => 'Name and base price are required'
    ]);
    exit;
  }

  $query = "UPDATE product SET name=:name, price=:price, description=:description WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'name' => $data['name'],
    'price' => $data['price'],
    'description' => $data['description'],
    'id' => $id
  ]);
  if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Produit non trouvé'
    ]);
    exit;
  }
  http_response_code(204);
}

// Suppression des produits 
if ($resourceName === 'product' && $isItemOperation && $httpMethod === 'DELETE') {
  $query = "DELETE FROM product WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute(['id' => $id]);
  if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Produit non trouvé'
    ]);
    exit;
  }
  http_response_code(204);
}