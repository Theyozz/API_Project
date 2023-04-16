<?php 

namespace App\crud;

use InvalidArgumentException;
use PDO;

class CommandeCrud 
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): bool
    {
        $query = "INSERT INTO commande VALUES (null, null, :quantite, :product_id)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'quantite' => $data['quantite'],
            'product_id' => $data['product_id']
      ]);
      if ($stmt == false) {
        throw new InvalidArgumentException("commande non crée");
      }
      return ($stmt->rowCount() > 0);   
    }

    public function fetchAllCommandes(): array 
    {
        $query = "SELECT * FROM commande";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([]);
        $products = $stmt->fetchAll();
        return $products;
    }

    public function fetchOneCommande($id): array | false
    {
        $query = "SELECT * FROM commande WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
      
        $product = $stmt->fetch();
        if ($product === false) {
            throw new InvalidArgumentException('Commande non trouvé');
          }
        return $product;
    }

    public function update($data,$id): bool
    {
      
      if (!isset($data['id'])) {
        throw new InvalidArgumentException('Lid de la commande est requis');
        exit;
      }
        $query = "UPDATE commande SET quantite=:quantite, product_id=:product_id WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
          'quantite' => $data['quantite'],
          'product_id' => $data['product_id'],
          'id' => $id
        ]);
        return ($stmt->rowCount() > 0);   
    }
    
    public function delete($id): bool
    {
        $query = "DELETE FROM commande WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() === 0) {
          throw new InvalidArgumentException('Commmande non trouvé');
          exit;
        }
        return ($stmt->rowCount() > 0);   
    }
}