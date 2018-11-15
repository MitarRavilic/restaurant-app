<?php
    namespace App\Controllers;

    use App\Core\Controller;
    use App\Core\DatabaseConnection;
    use App\Models\CategoryModel;

    class CategoryController extends Controller {
        public function listAllCategories() {
            $categoryModel = new CategoryModel($this->getDatabaseConnection());
            $categories = $categoryModel->getAll();
            $this->set('categories', $categories);
        }
    }