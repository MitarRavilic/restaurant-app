<?php
    namespace App\Controllers;

    use App\Validators\PasswordValidator;
    use App\Validators\StringValidator;
    use App\Validators\EmailValidator;
    use App\Validators\UsernameValidator;
    use App\Validators\NumberValidator;
    use App\Models\UserModel;
    use App\Models\ItemModel;
    use App\Models\IngredientModel;
    use App\Models\CategoryModel;
    use App\Utility\JsonUtility;

    use App\Core\Role\AdministratorRoleController;

    class AdministratorDashboardController extends AdministratorRoleController {
        public function home() {
            
        }

        public function administratorGetUserRegistration(){}

        public function administratorGetItemRegistration(){}

        public function administratorGetCategoryRegistration(){}

        public function administratorGetIngredientRegistration(){}

        public function administratorPostRegistration(){
            // $username =  \filter_input(INPUT_POST, 'reg_username', FILTER_SANITIZE_STRING);
            // $password1 =  \filter_input(INPUT_POST, 'reg_password1', FILTER_SANITIZE_STRING);
            // $password2 =  \filter_input(INPUT_POST, 'reg_password2', FILTER_SANITIZE_STRING);
            // $first_name =  \filter_input(INPUT_POST, 'reg_first_name', FILTER_SANITIZE_STRING);
            // $last_name =  \filter_input(INPUT_POST, 'reg_last_name', FILTER_SANITIZE_STRING);
            // $address1 =  \filter_input(INPUT_POST, 'reg_address1', FILTER_SANITIZE_STRING);
            // //NE FILTRIRAM TRENUTNO
            // //$address2 =  \filter_input(INPUT_POST, 'reg_address2');
            // //NE FILTRIRAM TRENUTNO
            // //$address3 =  \filter_input(INPUT_POST, 'reg_address3');
            // //NE VALIDIRA SE TRENUTNO
            // $phone =  \filter_input(INPUT_POST, 'reg_phone', FILTER_SANITIZE_STRING);
            // $email =  \filter_input(INPUT_POST, 'reg_email', FILTER_SANITIZE_EMAIL);
            // $role = \filter_input(INPUT_POST, 'reg_role');
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('user'));

            if(!(new UsernameValidator())->isValid($object->username)){
                $this->set('message', 'Greska: Korisnicko ime nije ispravnog formata.');
                return;
            }

            if($object->password1 != $object->password2) {
                $this->set('message', 'Greska: Lozinke se moraju poklapati.');
                return;
            }

            if(!(new PasswordValidator())->isValid($object->password1)){
                $this->set('message', 'Greska: Lozinka nije ispravnog formata.');
                return;
            }

            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($object->first_name)){
                $this->set('message', 'Greska: Ime nije ispravnog formata.');
                return;
            }


            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($object->last_name)){
                $this->set('message', 'Greska: Prezime nije ispravnog formata.');
                return;
            }

            if(!(new EmailValidator())
                    ->setMinLength(7)
                    ->setMaxLength(64)
                    ->isValid($object->email)){
                $this->set('message', 'Greska: Email nije ispravan.');
                return;
            }


            $passwordHash = \password_hash($object->password1, PASSWORD_DEFAULT);
            $userModel = new UserModel($this->getDatabaseConnection());
            
            $user = $userModel->getByFieldName('username', $object->username);
            if($user){
                $this->set('message', 'Greska: Vec postoji korisnik sa tim korisnickim imenom.');
                return;
            }

            $userId = $userModel->add([
                'username' => $object->username,
                'password' => $object->passwordHash,
                'role'        => $object->role,  
                'first_name'=> $object->first_name,
                'last_name'=> $object->last_name,
                'address1'=> $object->address1,
                'phone'=> $object->phone,
                'email'=> $object->email
                
            ]);
            
            if(!$userId){
                $this->set('message', 'Greska: registrovanje nije bilo uspesno.');
                return;
            }

            $this->set('message', 'Napravljen je novi nalog.');
        }

        public function administratorCreateUser(){
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('user'));
            

           
            #validacija
            if(!(new UsernameValidator())->isValid($object->username)){
                $this->set('message', 'Greska: Korisnicko ime nije ispravnog formata.');
                return;
            }

            if($object->password1 != $object->password2) {
                $this->set('message', 'Greska: Lozinke se moraju poklapati.');
                return;
            }

            if(!(new PasswordValidator())->isValid($object->password1)){
                $this->set('message', 'Greska: Lozinka nije ispravnog formata.');
                return;
            }

            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($object->first_name)){
                $this->set('message', 'Greska: Ime nije ispravnog formata.');
                return;
            }


            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($object->last_name)){
                $this->set('message', 'Greska: Prezime nije ispravnog formata.');
                return;
            }

            if(!(new EmailValidator())
                    ->setMinLength(7)
                    ->setMaxLength(64)
                    ->isValid($object->email)){
                $this->set('message', 'Greska: Email nije ispravan.');
                return;
            }

            $passwordHash = \password_hash($object->password1, PASSWORD_DEFAULT);
            $userModel = new UserModel($this->getDatabaseConnection());
            
            $check_user = $userModel->getByFieldName('username', $object->username);
            if($check_user){
                $this->set('message', 'Greska: Vec postoji korisnik sa tim korisnickim imenom.');
                return;
            }
           
           $message = $userModel->addUser(
                $object->email,
                $object->first_name,
                $object->last_name,
                $object->address1,
                $object->address2,
                $object->address3, 
                $object->username,
                $passwordHash,
                $object->role,  
                $object->phone
            );
            $this->set('message', $message);
        }

        public function administratorUpdateUser(){
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('user'));
           
            
            #validacija
            if(!(new UsernameValidator())->isValid($object->username)){
                $this->set('message', 'Greska: Korisnicko ime nije ispravnog formata.');
                return;
            }

            if($object->password1 != $object->password2) {
                $this->set('message', 'Greska: Lozinke se moraju poklapati.');
                return;
            }

            if(!(new PasswordValidator())->isValid($object->password1)){
                $this->set('message', 'Greska: Lozinka nije ispravnog formata.');
                return;
            }

            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($object->first_name)){
                $this->set('message', 'Greska: Ime nije ispravnog formata.');
                return;
            }


            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($object->last_name)){
                $this->set('message', 'Greska: Prezime nije ispravnog formata.');
                return;
            }

            if(!(new EmailValidator())
                    ->setMinLength(7)
                    ->setMaxLength(64)
                    ->isValid($object->email)){
                $this->set('message', 'Greska: Email nije ispravan.');
                return;
            }

            $passwordHash = \password_hash($object->password1, PASSWORD_DEFAULT);
            $userModel = new UserModel($this->getDatabaseConnection());
            
            $check_user = $userModel->getByFieldName('username', $object->username);
            if(!$check_user){
                $this->set('message', 'ERROR: User doesn\'t exist in the database');
                return;
            }

           
           
            $userModel->updateUser(
                $object->user_id,
                $object->email,
                $object->first_name,
                $object->last_name,
                $object->address1,
                $object->address2,
                $object->address3, 
                $object->username,
                $passwordHash,
                $object->role,  
                $object->phone
            );
        }
        public function administratorDeleteUser(string $user_id){
            $userModel = new UserModel($this->getDatabaseConnection());
            $message = $userModel->deleteUser($user_id);
            $this->set('message',$message);
        }

        public function administratorCreateItem(){
            
            // $title = \filter_input(INPUT_POST, 'reg_title',FILTER_SANITIZE_STRING);
            // $description = \filter_input(INPUT_POST, 'reg_description',FILTER_SANITIZE_STRING);
            // $mass = \filter_input(INPUT_POST, 'reg_mass',FILTER_SANITIZE_INT);
            // $calorie_count = \filter_input(INPUT_POST, 'reg_calorie_count',FILTER_SANITIZE_INT);
            // $price = \filter_input(INPUT_POST, 'reg_price',FILTER_SANITIZE_FLOAT);
            // $category_title = \filter_input(INPUT_POST, 'reg_category_title',FILTER_SANITIZE_STRING);
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('item'));
            $ingredientsObject = $utility->unpackNestedFromJson($utility->loadRawFromJson('ingredients'));
         

            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(64)
            ->isValid($object->title)){
            $this->set('message', 'Greska: Ime nije ispravnog formata.');
            return;
            }

            if(!(new StringValidator())
            ->setMinLength(0)
            ->setMaxLength(254)
            ->isValid($object->description)){
            $this->set('message', 'Greska: Opis nije ispravnog formata.');
            return;
            }

            if(!(new NumberValidator())
            ->isValid($object->mass)){
                $this->set('message', 'Greska: Masa nije ispravnog formata.');
            return;
            }

            if(!(new NumberValidator())
            ->setMaxIntegerDigitCount(4)
            ->isValid($object->calorie_count)){
                $this->set('message', 'Greska: Kalorije nisu ispravnog formata.');
            return;
            }

            if(!(new NumberValidator())
            ->setMaxIntegerDigitCount(8)
            ->isValid($object->price)){
                $this->set('message', 'Greska: Cena nije ispravnog formata.');
            return;
            }

            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(64)
            ->isValid($object->category_title)){
                $this->set('message', 'Greska: Ime kategorije nije ispravnog formata.');
            return;
            }

            $itemModel = new ItemModel($this->getDatabaseConnection());
            $ingredientModel = new IngredientModel($this->getDatabaseConnection());

            $item = $itemModel->getByFieldName('title', $object->title);
            if($item){
                $this->set('message', 'Greska: Vec postoji item sa tim imenom.');
                return;
            }
            
            $message = $itemModel->addItem([
                'title' =>$object->title,
                'description' =>$object->description,
                'mass'         =>$object->mass,
                'calorie_count' =>$object->calorie_count,
                'price' =>$object->price,
                'category_title' =>$object->category_title
            ]);

            
            $itemID = $itemModel->getLastInsertedItemID();
            foreach($ingredientsObject as $ingredient){
                $ingredientMessage = $ingredientModel->addIngredient(
                    $ingredient->title,
                    $ingredient->allergens
                );

                $itemModel->createItemIngredientConnection($itemID, intval($ingredient->ingredient_id));
            }

            $this->set('message', "Item status:" . $message . "\nIngredient status:" . $ingredientMessage);
        }

        public function administratorUpdateItem(){
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('item'));

            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(64)
            ->isValid($object->title)){
            $this->set('message', 'Greska: Ime nije ispravnog formata.');
            return;
            }

            if(!(new StringValidator())
            ->setMinLength(0)
            ->setMaxLength(254)
            ->isValid($object->description)){
            $this->set('message', 'Greska: Opis nije ispravnog formata.');
            return;
            }

            if(!(new NumberValidator())
            ->isValid($object->mass)){
                $this->set('message', 'Greska: Masa nije ispravnog formata.');
            return;
            }

            if(!(new NumberValidator())
            ->setMaxIntegerDigitCount(4)
            ->isValid($object->calorie_count)){
                $this->set('message', 'Greska: Kalorije nisu ispravnog formata.');
            return;
            }

            if(!(new NumberValidator())
            ->allowDecimal()
            ->setMaxIntegerDigitCount(8)
            ->setMaxDecimalDigitCOunt(2)
            ->isValid($object->price)){
                $this->set('message', 'Greska: Cena nije ispravnog formata.');
            return;
            }

            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(64)
            ->isValid($object->category_title)){
                $this->set('message', 'Greska: Ime kategorije nije ispravnog formata.');
            return;
            }

            $itemModel = new ItemModel($this->getDatabaseConnection());

            $item = $itemModel->getByFieldName('title', $object->title);
            if($item){
                $this->set('message', 'Greska: Vec postoji item sa tim imenom.');
                return;
            }
            
            $itemModel->updateItem([
                'item_id' =>$object->item_id,
                'title' =>$object->title,
                'description' =>$object->description,
                'mass'         =>$object->mass,
                'calorie_count' =>$object->calorie_count,
                'price'     =>$object->price,
                'category_title' =>$object->category_title
            ]);

        }
        public function administratorDeleteItem(string $item_id){
            $itemModel = new ItemModel($this->getDatabaseConnection());
            
            $message = $itemModel->deleteItem(intval($item_id));
                $this->set('message',$message);
        }

        public function administratorUpdateItemImage(){
            // $item_id = intval(\filter_input(INPUT_POST, 'reg_item_id'));
            // $path = \filter_input(INPUT_POST, 'reg_image_path');
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('image'));
            
            $itemModel = new ItemModel($this->getDatabaseConnection());
            $itemModel->updateItemImage([
                'item_id' => $object->item_id,
                'path' => $object->path
            ]);
        }

        public function administratorCreateCategory(){
            //$title = filter_input(INPUT_POST, 'reg_category_title', FILTER_SANITIZE_STRING);
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('category'));
            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(30)
            ->isValid($object->title)){
            $this->set('message', 'Greska: Naziv kategorije nije ispravnog formata.');
            return;
            }

            $categoryModel = new CategoryModel($this->getDatabaseConnection());

            $category = $categoryModel->getByFieldName('title', $object->title);
            if($category){
                $this->set('message', 'Greska: Vec postoji kategorija sa tim nazivom.');
                return;
            }

            $categoryModel->add([
                'title'=>$object->title
            ]);

            $this->set('message', 'Uspesno uneta kategorija');
        }
        
        public function administratorUpdateCategory(){
            // $category_id= intval(filter_input(INPUT_POST, 'reg_category_id'));
            // $title = filter_input(INPUT_POST, 'reg_category_title', FILTER_SANITIZE_STRING);
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('category'));

            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(30)
            ->isValid($object->title)){
            $this->set('message', 'Greska: Naziv kategorije nije ispravnog formata.');
            return;
            }

            $categoryModel = new CategoryModel($this->getDatabaseConnection());

            $category = $categoryModel->getByFieldName('title', $object->title);
            if($category){
                $this->set('message', 'Greska: Vec postoji kategorija sa tim nazivom.');
                return;
            }

            $message = $categoryModel->updateCategory(
                intval($object->category_id),
                $object->title
            );

            $this->set('message', $message);
        }
        
        public function administratorDeleteCategory(string $category_id){
            $category_id = intval($category_id);
            
            $categoryModel=  new CategoryModel($this->getDatabaseConnection());
            $message = $categoryModel->deleteCategory(intval($category_id));
            $this->set('message', $message);
        }

        public function administratorCreateIngredient(){
            //$title = filter_input(INPUT_POST, 'reg_ingredient_title', FILTER_SANITIZE_STRING);
            //$alergens = filter_input(INPUT_POST, 'reg_ingredient_alergens', FILTER_SANITIZE_STRING);
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('ingredient'));
            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(30)
            ->isValid($object->title)){
            $this->set('message', 'Greska: Naziv sastojka nije ispravnog formata.');
            return;
            }

            $ingredientModel = new IngredientModel($this->getDatabaseConnection());

            $ingredient = $ingredientModel->getByFieldName('title', $object->title);
            if($ingredient){
                $this->set('message', 'Greska: Vec postoji sastojak sa tim nazivom.');
                return;
            }

            $message = $ingredientModel->addIngredient(
                $object->title,
                $object->allergens
            );

            $this->set('message', $message);
        }

        public function administratorUpdateIngredient(){
            // $ingredient_id= intval(filter_input(INPUT_POST, 'reg_ingredient_id'));
            // $title = filter_input(INPUT_POST, 'reg_ingredient_title', FILTER_SANITIZE_STRING);
            // $alergens = filter_input(INPUT_POST, 'reg_ingredient_alergens', FILTER_SANITIZE_STRING);
            $utility = new JsonUtility();
            $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('ingredient'));

            if(!(new StringValidator())
            ->setMinLength(2)
            ->setMaxLength(30)
            ->isValid($object->title)){
            $this->set('message', 'Greska: Naziv sastojka nije ispravnog formata.');
            return;
            }

            $ingredientModel = new IngredientModel($this->getDatabaseConnection());

            $ingredient = $ingredientModel->getByFieldName('title', $object->title);
            if(!$ingredient){
                $this->set('message', 'Greska: Ne postoji sastojak sa tim nazivom.');
                return;
            }

            $message = $ingredientModel->updateIngredient(
                intval($object->ingredient_id),
                $object->title,
                $object->allergens
            );

            $this->set('message', $message);
        }

        
        public function administratorDeleteIngredient(string $ingredient_id){
            $ingredientModel=  new IngredientModel($this->getDatabaseConnection());
            $message = $ingredientModel->deleteIngredient(intval($ingredient_id));

            $this->set('message',$message);
        }

        public function adminAddItemIngredientList(){
            // $item_id = intval(\filter_input(INPUT_POST,'reg_item_id'));
            // $ingredientList= \filter_input(INPUT_POST,'reg_ingredientList');
            $object = unpackSingleFromJson(loadRawFromJson('ingredient'));

            $itemModel = new ItemModel($this->getDatabaseConnection());
           $itemModel->addItemIngredientList([
               'item_id'=> $item_id,
               'ingredientList' => $ingredientList
           ]);
        }
        public function adminUpdateItemIngredientList(){
            $item_id = intval(\filter_input(INPUT_POST,'reg_item_id'));
            $ingredientList= \filter_input(INPUT_POST,'reg_ingredientList');
            
            $itemModel = new ItemModel($this->getDatabaseConnection());
           $itemModel->updateItemIngredientList([
               'item_id'=> $item_id,
               'ingredientList' => $ingredientList
           ]);
        }

    }