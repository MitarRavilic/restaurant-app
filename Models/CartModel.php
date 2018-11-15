<?php
    namespace App\Models;

    use App\Core\Model;

    class CartModel extends Model {
        protected function getFieldList(): array {
            return [
                'cart_id' => new Field((new NumberValidator())
                                        ->allowUnsigned()),
                'user_id' => new Field((new NumberValidator())
                                        ->allowUnsigned()),
                'session_id' => new Field((new StringValidator())
                                        ->setMinLength(32)
                                        ->setMaxLength(32)),
                
            ];
        }

        final public function createCart(int $userId, string $sessionId){
            $sql = "INSERT INTO cart(user_id, session_id) VALUES(?, ?);";
            $prep = $this->getConnection()->prepare($sql);
            return $prep->execute([$userId, $sessionId]);
        }

        final public function createCartItemConnection($cartId, $itemId, $amount, $portion){
            $sql = "INSERT INTO cart_item(cart_id, item_id, amount, portion) VALUES(?, ?, ?, ?);";
            $prep = $this->getConnection()->prepare($sql);

            return $prep->execute([$cartId, $itemId, $amount, $portion]);
        }

        final public function getCartId(int $userId){
            $sql = "SELECT cart_id FROM cart WHERE user_id=?";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$userId]);
            $id = NULL;
            if ( $res ) {
               $id = $prep->fetch(\PDO::FETCH_OBJ);
            }
            return $id;
        }
    }