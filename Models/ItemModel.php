<?php
    namespace App\Models;

    use App\Core\Model;
    use App\Core\Field;
    use App\Validators\NumberValidator;
    use App\Validators\StringValidator;

    // item_id
    // title
    // description
    // mass
    // calorie_count
    // price
    // category_id


    class ItemModel extends Model {
        protected function getFieldList(): array {
            return [
                'item_id' => new Field((new NumberValidator())
                                        ->allowUnsigned()),
                'title' => new Field((new StringValidator())
                                        ->setMinLength(6)
                                        ->setMaxLength(255)),
                'description' => new Field((new StringValidator())
                                        ->setMinLength(8)
                                        ->setMaxLength(255)),
                'mass' => new Field((new NumberValidator())
                                    ->allowUnsigned()),
                'calorie_count' => new Field((new NumberValidator())
                                    ->allowUnsigned()),
                'price' => new Field((new NumberValidator)
                                    ->allowUnsigned()
                                    ->setMaxIntegerDigitCount(10)),
                'category_id' => new Field((new NumberValidator())
                                    ->allowUnsigned())
            ];
        }
        final public function getPortions(int $itemId){
            $sql = "CALL sp_list_item_portions(?)";
             $prep = $this->getConnection()->prepare($sql);
             $res = $prep->execute([$itemId]);
 
             if ( $res ) {
                 $portions = $prep->fetchAll(\PDO::FETCH_OBJ);
             }
 
             return $portions;
        }
        
        final public function getIngredientList(int $itemId){
            $sql = "CALL sp_list_item_ingredients(?)";
             $prep = $this->getConnection()->prepare($sql);
             $res = $prep->execute([$itemId]);
 
             if ( $res ) {
                 $ingredients = $prep->fetchAll(\PDO::FETCH_OBJ);
                 
             }
 
             return $ingredients;
        }

        final public function addItem(array $data) {
            $sql = 'CALL sp_createItem(?, ?, ?, ?, ?, ?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$data['title'], $data['description'], intval($data['mass']), intval($data['calorie_count']), floatval($data['price']), $data['category_title']]);
            $res ? 'Item successfully added.' : 'Item creation failed.';
        }

        // int $item_id, string $title, string $description, int $mass, int $calorie_count, float $price, string $category_title
        final public function updateItem(array $data) {
            $sql = 'CALL sp_updateItem(?, ?, ?, ?, ?, ?, ?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$data->item_id, $data->title, $data->description, $data->mass, $data->calorie_count, $data->price, $data->category_title]);
            
            $res ? $this->set('message','Item successfully updated.') : $this->set('message','Item update failed.');
        }

        final public function deleteItem(int $item_id){
            $sql = 'CALL sp_deleteItem(?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$item_id]);
            $res ? 'Item deleted.' : 'Item deletion failed.';
        }

        final public function updateItemImage(int $item_id, string $path){
            $sql = 'CALL sp_updateItemImage(?, ?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$item_id, $path]);
            $res ? $this->set('message','Item image updated') : $this->set('message','Item image update failed.');
        }
        # ovo treba da se proveri da li uopste moze ovako
        final public function addItemIngredientList(int $item_id, array $ingredientList){
            $sql = 'CALL sp_addItemIngredient(?, ?)';
            $prep = $this->getConnection()->prepare($sql);

          
            foreach($ingredientList as $ingredient){
                $res = $prep->execute([$item_id, $ingredient]);
                $res ? $this->set('message','Item ingredient added') : $this->set('message','Item ingredient adding failed.');
            }
        }
        final public function deleteItemIngredientList(int $item_id){
            $sql = 'CALL sp_deleteItemIngredient(?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$item_id]);
            $res ? $this->set('message','Item ingredient list deleted') : $this->set('message','Item ingredient list deletion failed.');
        }
        
        final public function updateItemIngredientList(int $item_id, array $ingredientList){
            deleteItemIngredientList($item_id);
            addItemIngredientList($item_id, $ingredientList);
        }

        final public function getAllItemsMerged(){
            $sql = 'CALL sp_getAllItemsMerged()';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute();
            
            if ( $res ) {
                $items = $prep->fetchAll(\PDO::FETCH_OBJ);
            }

            return $items;
        }

        final public function getLastInsertedItemID(){
            $sql = 'SELECT MAX(item_id) AS max_item_id FROM item';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute();
            
            if ( $res ) {
                $item_id = $prep->fetch(\PDO::FETCH_OBJ);
            }

            var_dump($item_id->max_item_id);

            return $item_id->max_item_id;
        }

        final public function createItemIngredientConnection(int $item_id, int $ingredient_id){
            $sql = 'CALL sp_addItemIngredient(?, ?)';

            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$item_id, $ingredient_id]);
        }

        final public function getAllItemsByCartID(int $cart_id){
            $sql = 'CALL sp_getAllItemsByCartID(?)';
            $prep = $this->getConnection()->prepare($sql);

            $res = $prep->execute([$cart_id]);

            if($res){
                $items = $prep->fetchAll(\PDO::FETCH_OBJ);
            }

            return $items;
        }
    }