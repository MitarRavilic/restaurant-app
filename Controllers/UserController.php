<?php
    namespace App\Controllers;

    use App\Validators\PasswordValidator;
    use App\Validators\StringValidator;
    use App\Validators\EmailValidator;
    use App\Validators\UsernameValidator;
    use App\Models\UserModel;
    use App\Utility\JsonUtility;
    use App\Core\Controller;
    
    class UserController extends Controller {
        public function userGetRegister() {

        }

        public function userPostRegister() {
            $username =  \filter_input(INPUT_POST, 'reg_username', FILTER_SANITIZE_STRING);
            $password1 =  \filter_input(INPUT_POST, 'reg_password1', FILTER_SANITIZE_STRING);
            $password2 =  \filter_input(INPUT_POST, 'reg_password2', FILTER_SANITIZE_STRING);
            $first_name =  \filter_input(INPUT_POST, 'reg_first_name', FILTER_SANITIZE_STRING);
            $last_name =  \filter_input(INPUT_POST, 'reg_last_name', FILTER_SANITIZE_STRING);
            $address1 =  \filter_input(INPUT_POST, 'reg_address1', FILTER_SANITIZE_STRING);
            //NE FILTRIRAM TRENUTNO
            //$address2 =  \filter_input(INPUT_POST, 'reg_address2');
            //NE FILTRIRAM TRENUTNO
            //$address3 =  \filter_input(INPUT_POST, 'reg_address3');
            //NE VALIDIRA SE TRENUTNO
            $phone =  \filter_input(INPUT_POST, 'reg_phone', FILTER_SANITIZE_STRING);
            $email =  \filter_input(INPUT_POST, 'reg_email', FILTER_SANITIZE_EMAIL);
            // $utility = new JsonUtility();
            // $object = $utility->unpackSingleFromJson($utility->loadRawFromJson('user'));
            if(!(new UsernameValidator())->isValid($username)){
                $this->set('message', 'Greska: Korisnicko ime nije ispravnog formata.');
                return;
            }

            if($password1 != $password2) {
                $this->set('message', 'Greska: Lozinke se moraju poklapati.');
                return;
            }

            if(!(new PasswordValidator())->isValid($password1)){
                $this->set('message', 'Greska: Lozinka nije ispravnog formata.');
                return;
            }

            if(!(new StringValidator())
                    ->setMinLength(5)
                    ->setMaxLength(255)
                    ->isValid($address1)){
                $this->set('message', 'Greska: Adresa1 nije ispravnog formata.');
                return;
            }

            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($first_name)){
                $this->set('message', 'Greska: Ime nije ispravnog formata.');
                return;
            }


            if(!(new StringValidator())
                    ->setMinLength(2)
                    ->setMaxLength(32)
                    ->isValid($last_name)){
                $this->set('message', 'Greska: Prezime nije ispravnog formata.');
                return;
            }

            if(!(new EmailValidator())
                    ->setMinLength(7)
                    ->setMaxLength(64)
                    ->isValid($email)){
                $this->set('message', 'Greska: Email nije ispravan.');
                return;
            }


            $passwordHash = \password_hash($password1, PASSWORD_DEFAULT);
            $userModel = new UserModel($this->getDatabaseConnection());
            
            $user = $userModel->getByFieldName('username', $username);
            if($user){
                $this->set('message', 'Greska: Vec postoji korisnik sa tim korisnickim imenom.');
                return;
            }

            $userId = $userModel->add([
                'username' => $username,
                'password' => $passwordHash,
                'role'        => 'customer',  
                'first_name'=> $first_name,
                'last_name'=> $last_name,
                'address1'=> $address1,
                'phone'=> $phone,
                'email'=> $email
            ]);

            if(!$userId){
                $this->set('message', 'Greska: registrovanje nije bilo uspesno.');
                return;
            }

            $this->set('message', 'Napravljen je novi nalog.');
        }

        public function userGetLogin(){

        }

        public function userPostLogin(){
            $username =  \filter_input(INPUT_POST, 'login_username', FILTER_SANITIZE_STRING);
            $password =  \filter_input(INPUT_POST, 'login_password', FILTER_SANITIZE_STRING);

            if(!(new UsernameValidator())->isValid($username)){
                $this->set('message', 'Greska: Korisnicko ime nije ispravnog formata.');
                return;
            }

            if(!(new PasswordValidator())->isValid($password)){
                sleep(1);
                $this->set('message', 'Greska: Lozinka nije ispravnog formata.');
                return;
            }

            $userModel = new UserModel($this->getDatabaseConnection());
            $user = $userModel->getByFieldName('username', $username);
            if(!$user){
                $this->set('message', 'Greska: Ne postoji korisnik sa tim korisnickim imenom.');
                return;
            }

            // Enkriptovana sifra
            $passwordHash = $user->password;
            if(!password_verify($password, $passwordHash)){
                sleep(1);
                $this->set('message', 'Greska: Lozinka nije ispravna');
                return;
            }

            // Sirova sifra
            //$passwordHash = $user->password;
            //if($password !== $passwordHash){
            //    sleep(1);
            //    $this->set('message', 'Greska: Lozinka nije ispravna');
            //    return;
            //}

            $this->getSession()->put('user_id', $user->user_id);
            $this->getSession()->save();
            
            if($user->role == 'administrator'){
                $this->redirect('http://localhost/pivt/admin/dashboard');
            }

            if($user->role == 'customer'){
                $this->set('message', 'Successful login');
            }

        }
    }