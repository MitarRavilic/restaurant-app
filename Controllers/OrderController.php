<?php
    namespace App\Controllers;

    use App\Core\Controller;
    use App\Core\Role\DispatcherRoleController;
    use Configuration;
    use App\Models\OrderModel;
    use App\Models\CartModel;
    use App\Validators\NumberValidator;
    use App\Validators\StringValidator;
    use App\Utility\JsonUtility;


    class OrderController extends Controller{
        #order fields:
        // delivery_at
        // is_accepted
        // is_canceled
        // is_delivered
        // rating
        // personal_preference
        // comment
        // created_at
        // user_id
        // cart_id

       
        public function orderDetails($orderId){
            $orderModel = new OrderModel($this->getDatabaseConnection());
            $order = $orderModel->getById($orderId);
            $this->set('order', $order);
        }

        public function createOrder(){
            $utility = new JsonUtility();
            $orderDetails = $utility->unpackSingleFromJson($utility->loadRawFromJson('orderDetails'));
            $orderItems = $utility->unpackNestedFromJson($utility->loadRawFromJson('orderItems'));

            $cartModel = new CartModel($this->getDatabaseConnection());
            $orderModel = new OrderModel($this->getDatabaseConnection());

            if(!(new StringValidator())
                    ->setMinLength(0)
                    ->setMaxLength(254)
                    ->isValid($orderDetails->personal_preference)){
                $this->set('message', 'Error: Personal preference text is invalid');
                return;
            }
            if(!(new StringValidator())
            ->setMinLength(0)
            ->setMaxLength(254)
            ->isValid($orderDetails->delivery_address)){
            $this->set('message', 'Error: Delivery address is invalid');
            return;
            }

            // CART MAKING
            $userID = $this->getSession()->get('user_id');
            $sessionID = $this->getSession()->get('__fingerprint');
            
            $cartModel->createCart(intval($userID), $sessionID);

            //CART_ITEM MAKING
            $cartID = $cartModel->getCartId($userID);

            foreach($orderItems as $item){
                var_dump($item);
                var_dump($cartID->cart_id);
                $cartModel->createCartItemConnection($cartID->cart_id, $item->item_id, $item->amount, $item->portion);
            }

            $message = $orderModel->createOrder(intval($userID), intval($cartID->cart_id), $orderDetails->delivery_address, $orderDetails->delivery_time, $orderDetails->personal_preference);

            if($message == 'Order created successfuly'){
                $this->getSession()->put('cartItems', []);
            }
        }
    }