<?php
    namespace App\Models;
    use \App\Core\Model;

    class OrderModel extends Model{

// delivery_at
// is_accepted
// is_canceled
// is_delivered
// rating
// personal_preference
// comment
// created_at
// delivered_at
// user_id
// cart_id

        protected function getFieldList(): array {
            return [
                'order_id' => new Field((new NumberValidator())
                                        ->allowUnsigned()),
                'delivery_at' => new Field((new StringValidator())
                                        ->setMinLength(0)
                                        ->setMaxLength(4)),
                'is_accepted' => new Field((new StringValidator())
                                        ->setMinLength(0)
                                        ->setMaxLength(255)),
                'is_canceled' => new Field((new NumberValidator())
                                    ->allowUnsigned()),
                'is_delivered' => new Field((new NumberValidator())
                                    ->allowUnsigned()),
                'rating' => new Field((new NumberValidator)
                                    ->allowUnsigned()
                                    ->setMaxIntegerDigitCount(1)),
                'personal_preference' => new Field((new StringValidator())
                                    ->setMinLength(0)
                                    ->setMaxLength(255)),
                'comment' => new Field((new StringValidator())
                                    ->setMinLength(0)
                                    ->setMaxLength(255)),
                'delivered_at' => new Field((new StringValidator())
                                    ->setMinLength(0)
                                    ->setMaxLength(4)),
                'user_id' => new Field((new NumberValidator())
                                    ->allowUnsigned()),
                'cart_id' => new Field((new NumberValidator())
                                    ->allowUnsigned()),
                'delivery_address' => new Field((new StringValidator())
                                    ->setMinLength(0)
                                    ->setMaxLength(255))
            ];
        }
        #todo pretvori u proceduru!
        final public function createOrder(int $user_id,int $cart_id, string $delivery_address, string $delivery_time, string $personal_preference){
            $sql = "CALL sp_createOrder(?, ?, ?, ?, ?);";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$user_id, $cart_id, $delivery_address, $delivery_time, $personal_preference]);
            return $res ? 'Order created successfuly' : 'Order creation failed';
        }
        
        final public function updateOrderIsDelivered(int $order_id){
            $sql = "CALL sp_updateOrderIsDelivered(?)";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$order_id]);
           return $res ? 'Order marked as delivered' : 'Order delivery marking failed';
        }

        final public function updateOrderIsCancelled(int $order_id){
            $sql = "CALL sp_updateOrderIsCanceled(?)";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$order_id]);
            return $res ? 'Order successfuly cancelled' : 'Order cancelation failed';
        }
        
        final public function updateOrderIsAccepted(int $order_id, int $is_accepted, string $delivery_at){
            $sql = "CALL sp_updateOrderIsAccepted(?, ?, ?)";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$order_id, $is_accepted, $delivery_at]);
        return $res ? 'Order accepted' : 'Order cancelation failed';
        }

        final public function updateOrderRatingComment(int $order_id, int $rating, string $comment){
            $sql = "CALL sp_updateOrderRatingComment(?, ?, ?)";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$order_id, $rating, $comment]);
        return $res ? 'Order rating and comment successful' : 'Order rating and comment failed';   
        }

        final public function getOrderItems(int $order_id){
            $sql = "CALL sp_listOrderItems(?)";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$order_id]);
            if ( $res ) {
                $order_items = $prep->fetchAll(\PDO::FETCH_OBJ);
            }
            return $order_items;
        }

        final public function getEmailFromOrder(int $order_id){
            $sql = "CALL sp_getEmailFromOrder(?)";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([$order_id]);
            if ( $res ) {
                $email = $prep->fetch(\PDO::FETCH_OBJ);
            }
            var_dump($email);
            return $email;
        }

        final public function getAllOrders(){
            $sql = "SELECT * FROM `order` WHERE is_accepted <> 'yes' AND is_accepted <> 'no'";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute();

            if($res){
                $orders = $prep->fetchAll(\PDO::FETCH_OBJ);
            }

            return $orders;
        }
    }