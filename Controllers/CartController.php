<?php
    namespace App\Controllers;

    use App\Core\Role\CustomerRoleController;
    use Configuration;
    use App\Models\CartModel;
    use App\Models\OrderModel;

    class CartController extends CustomerRoleController {
        public function showCart(){
            $cartItems = $this->getSession()->get('cartItems');
            $this->set('cartItems', $cartItems);
        }

        public function redirectCart(){

        }

        public function postCart(){
            $itemsFromPost = \filter_input(INPUT_POST, 'cartItems', FILTER_SANITIZE_STRING);
            $decodedData = json_decode(html_entity_decode($itemsFromPost), true);
            $splitted = explode('}', $decodedData);
            array_pop($splitted);


            for($i=0; $i < count($splitted);$i++){
                $splitted[$i] = str_replace('[', '', $splitted[$i]);
                $splitted[$i] = str_replace(']', '', $splitted[$i]);
                $splitted[$i] = $splitted[$i] . '}';
                $splitted[$i] = json_decode($splitted[$i]);
            }

            
            $session_id = $this->getSession()->get('__fingerprint');
            $user_id = intval($this->getSession()->get('user_id'));
            $cartModel = new CartModel($this->getDatabaseConnection());

            $vraceniKart = $cartModel->createCart($user_id, $session_id);
            

            $cartId = $cartModel->getCartId($session_id);
            $cartId = intval($cartId->cart_id);

            foreach($splitted as $item){
                $vraceniModel = $cartModel->createCartItemConnection($cartId, $item->item_id, $item->amount, $item->portion);
            }

            $orderModel = new OrderModel($this->getDatabaseConnection());
            $vraceniOrder = $orderModel->createOrder($user_id, $cartId);


        }
    }