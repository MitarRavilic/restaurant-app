<?php
    namespace App\Models;

    use App\Core\Model;
    use App\Core\Field;
    use App\Validators\NumberValidator;
    use App\Validators\StringValidator;

    class IngredientModel extends Model {
        protected function getFieldList(): array {
            return [
                'item_id' => new Field((new NumberValidator())
                                        ->allowUnsigned()),
                'title' => new Field((new StringValidator())
                                        ->setMinLength(1)
                                        ->setMaxLength(255)),
                'alergens' => new Field((new StringValidator())
                                        ->setMinLength(1)
                                        ->setMaxLength(64))
            ];
        }

        final public function getAllIngredients(){
            $sql = 'SELECT * FROM ingredient';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute();
            
            if ( $res ) {
                $ingredients = $prep->fetchAll(\PDO::FETCH_OBJ);
            }

            return $ingredients;
        }

        final public function addIngredient(string $title, string $allergens){
            $sql = 'CALL sp_createIngredient(?, ?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$title, $allergens]);

            $res ? 'Ingredient successfully added.' : 'Ingredient creation failed.';
        }

        final public function updateIngredient(int $ingredient_id, string $title, string $alergens){
            $sql = 'CALL sp_updateIngredient(?, ?, ?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$ingredient_id, $title, $alergens]);

            $res ? 'Ingredient successfully updated.' : 'Ingredient update failed.';
        }
        
        final public function deleteIngredient(int $ingredient_id){
            $sql = 'CALL sp_deleteIngredient(?)';
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$ingredient_id]);

            $res ? 'Ingredient successfully deleted.' : 'Ingredient deletion failed.';
        }
    }