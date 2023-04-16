<?php 

namespace App\crud;

use InvalidArgumentException;
use PDO;

class ProductCrud 
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): bool
    {
        $query = "INSERT INTO product VALUES (null, :name, :price, :description)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description']
      ]);
      if ($stmt == false) {
        throw new InvalidArgumentException("Produit non crée");
      }
      return ($stmt->rowCount() > 0);   
    }

    public function fetchAllProducts(): array 
    {
        $query = "SELECT * FROM product";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([]);
        $products = $stmt->fetchAll();
        return $products;
    }

    public function fetchOneProduct($id): array | false
    {
        $query = "SELECT * FROM product WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
      
        $product = $stmt->fetch();
        if ($product === false) {
            throw new InvalidArgumentException('Produit non trouvé');
          }
        return $product;
    }

    public function update($data,$id): bool
    {
      
      if (!isset($data['name'])) {
        throw new InvalidArgumentException('Le nom est requis');
        exit;
      }
        $query = "UPDATE product SET name=:name, price=:price, description=:description WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
          'name' => $data['name'],
          'price' => $data['price'],
          'description' => $data['description'],
          'id' => $id
        ]);
        return ($stmt->rowCount() > 0);   
    }
    
    public function delete($id): bool
    {
        $query = "DELETE FROM product WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() === 0) {
          throw new InvalidArgumentException('Produit non trouvé');
          exit;
        }
        return ($stmt->rowCount() > 0);   
    }
}