<?php
    namespace App\Controllers;

    use \App\Models\OrderModel;
    use \App\Models\ItemModel;

    class ApiDispatcherDashboardController extends \App\Core\ApiController {

        public function displayOrders(){
            $orderModel = new OrderModel($this->getDatabaseConnection());
            $itemModel = new ItemModel($this->getDatabaseConnection());

            $orders = $orderModel->getAllOrders();

            $singleOrderPackage = [];
            $multipleOrderPackage = [];

            foreach($orders as $order){
                $singleorderPackage = [];
                $singleOrderPackage['order_id']= $order->order_id;
                $singleOrderPackage['email']= $orderModel->getEmailFromOrder($order->order_id)->email;
                $singleOrderPackage['delivery_at'] = $order->delivery_at;
                $singleOrderPackage['delivery_address'] = $order->delivery_address;
                $singleOrderPackage['personal_preference'] = $order->personal_preference;
                $singleOrderPackage['items'] = $itemModel->getAllItemsByCartID($order->cart_id);
                array_push($multipleOrderPackage, $singleOrderPackage);
                unset($singleorderPackage);
            }
            $this->set('orders', $multipleOrderPackage);
        }
    }