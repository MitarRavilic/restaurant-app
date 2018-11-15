<?php
    namespace App\Core\Role;

    use App\Core\Controller;
    use App\Models\UserModel;
    use Configuration;

    class CustomerRoleController extends Controller {
        public function __pre(){
            if($this->getSession()->get('user_id') === null){
                $this->redirect(Configuration::BASE);
            }

            $userModel = new UserModel($this->getDatabaseConnection());
            $user = $userModel->getById($this->getSession()->get('user_id'));

            if($user->role !== 'customer'){
                $this->redirect(Configuration::BASE);
            }
        }

    }