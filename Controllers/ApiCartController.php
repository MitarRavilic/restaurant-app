<?php
    namespace App\Controllers;

    class ApiCartController extends \App\Core\ApiController {
        public function getCartItems(){
            $cartItems = $this->getSession()->get('cartItems',   []);
            $this->set('cartItems', $cartItems);
        }

        // public function addItemToCart($itemId) {
        //     $itemModel = new \App\Models\ItemModel($this->getDatabaseConnection());
        //     $item = $itemModel->getById($itemId);

        //     if(!$item) {
        //         $this->set('error', 'Item ne postoji');
        //         return;
        //     }

        //     $cartItems = $this->getSession()->get('cartItems', []);

        //     // <TODO:>Item amount handling</TODO:>
        //     //Provera da li postoji vec Item
        //     foreach($cartItem as $cartItems){
        //         if($cartItem->item_id == $itemId){
        //             $this->set('error', 'Vec postoji');
        //             return;
        //         }
        //     }

        //     /* cartItems[
        //         {
        //             item: {item_id: "6",
        //             title: "Piletina sa bambusom i pečurkama",
        //             description: "lorem ipsum",
        //             mass: "500",
        //             calorie_count: "800",
        //             price: "500.00",
        //             category_id: "1"}
        //             amount: {
        //                 3
        //             }
        //             portion: {
        //                 small
        //             }
        //         }
        //     ];  */
        //     $cartItems[] = $item;
        //     $this->getSession()->put('cartItems', $cartItems);
        //     $this->set('error', 'Uspesno dodavanje u sesiju');
        // }

        public function addItemToCart($itemId, $portion, $amount) {
            $itemModel = new \App\Models\ItemModel($this->getDatabaseConnection());
            $item = $itemModel->getById($itemId[0]);

            if(!$item) {
                $this->set('error', $portion);
                return;
            }

            $cartItems = $this->getSession()->get('cartItems', []);

            // <TODO:>Item amount handling</TODO:>
            //Provera da li postoji vec Item
            foreach($cartItem as $cartItems){
                if($cartItem->item_id == $itemId){
                    $this->set('error', 'Vec postoji');
                    return;
                }
            }

            /* cartItems[
                {
                    item: {item_id: "6",
                    title: "Piletina sa bambusom i pečurkama",
                    description: "lorem ipsum",
                    mass: "500",
                    calorie_count: "800",
                    price: "500.00",
                    category_id: "1"}
                    amount: {
                        3
                    }
                    portion: {
                        small
                    }
                }
            ];  */
            $item->portion = $portion;
            $item->amount = $amount;
            $cartItems[] = $item;
            $this->getSession()->put('cartItems', $cartItems);
            $this->getSession()->save();
            $this->set('error', 'Uspesno dodavanje u sesiju');
        }

        public function clearCart() {
            $this->getSession()->put('cartItems', []);
            $this->getSession()->save();

            $this->set('error', 'Svi itemi iz carta su uspesno obrisani');
        }
    }