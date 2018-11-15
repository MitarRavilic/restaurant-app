<?php
    namespace App\Core\Role;

    use App\Core\Controller;
    use App\Models\UserModel;

    class DispatcherRoleController extends Controller {
        public function __pre(){
            if($this->getSession()->get('user_id') === null){
                $this->redirect('http://localhost/pivt/user/login');
            }

            $userModel = new UserModel($this->getDatabaseConnection());
            $user = $userModel->getById($this->getSession()->get('user_id'));
            
            if($user->role !== 'dispatcher' || $user->role !== 'administrator'){
                $this->redirect('http://localhost/pivt/');
            }
        }

    }