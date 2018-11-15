<?php
    namespace App\Controllers;

    use App\Core\Controller;
    use App\Core\DatabaseConnection;
    use App\Models\ItemModel;
    use Configuration;

    class ItemController extends Controller {
        public function home() {
            $itemModel = new ItemModel($this->getDatabaseConnection());
            $items = $itemModel->getAll();
            $this->set('items', $items);
        
        }

        public function itemDetails($itemId) {
            $itemModel = new ItemModel($this->getDatabaseConnection());
            $item = $itemModel->getById($itemId);
            $portions = $itemModel->getPortions($itemId);
            $ingredientList = $itemModel->getIngredientList($itemId);
            
            if(!$item) {
                header('Location: ' . Configuration::BASE);
                exit;
            }
            
            $this->set('item', $item);
            $this->set('portions', $portions);
            $this->set('ingredientList',$ingredientList);
        }
    }