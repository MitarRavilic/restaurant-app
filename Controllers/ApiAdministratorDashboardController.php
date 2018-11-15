<?php
    namespace App\Controllers;

    use App\Models\UserModel;
    use App\Models\ItemModel;
    use App\Models\IngredientModel;
    use App\Models\CategoryModel;

    class ApiAdministratorDashboardController extends \App\Core\ApiController {
        public function getAllUsers(){
            $userModel = new UserModel($this->getDatabaseConnection());
            $users = $userModel->getAllUsers();
            $this->set('users', $users);
        }

        public function getAllItems() {
            $itemModel = new ItemModel($this->getDatabaseConnection());
            $items = $itemModel->getAllItemsMerged();

            $this->set('items', $items);
        }

        public function getAllIngredients(){
            $ingredientModel = new IngredientModel($this->getDatabaseConnection());
            $ingredients = $ingredientModel->getAllIngredients();

            $this->set('ingredients', $ingredients);
        }

        public function getAllCategories(){
            $categoryModel = new CategoryModel($this->getDatabaseConnection());
            $categories = $categoryModel->getAll();

            $this->set('categories', $categories);
        }
    }