<?php
    namespace App\Models;

    use App\Core\Model;
    use App\Core\Field;
    use App\Validators\UsernameValidator;
    use App\Validators\NumberValidator;
    use App\Validators\StringValidator;
    use App\Validators\PasswordValidator;
    use App\Validators\AlphaValidator;
    use App\Validators\EmailValidator;

    class UserModel extends Model {
        protected function getFieldList(): array {
            return [
                'user_id' => new Field((new StringValidator())
                                            ->setOnlyNum()),
                'username' => new Field(new UsernameValidator()),

                'password' => new Field(new PasswordValidator()),
                
                'role' => new Field((new StringValidator())
                                            ->setMinLength(10)
                                            ->setMaxLength(255)),

                'first_name' => new Field((new StringValidator())
                                            ->setMinLength(2)
                                            ->setMaxLength(32)
                                            ->setOnlyAlpha()),

                'last_name' => new Field((new StringValidator())
                                            ->setMinLength(2)
                                            ->setMaxLength(32)
                                            ->setOnlyAlpha()),

                'address1' => new Field((new StringValidator())
                                            ->setMinLength(10)
                                            ->setMaxLength(255)),

                //phone
                'phone'=> new Field((new StringValidator())
                                            ->setMinLength(5)
                                            ->setMaxLength(20)
                                            ->setOnlyNum()),
                
                'email' => new Field((new EmailValidator(7,64))
                                            ->setMinLength(7)
                                            ->setMaxLength(64))
                                            
            ];
        }
        public function getAllUsers(){
            $sql = "SELECT * FROM qry_getusers";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute();
            if ( $res ) {
                $users = $prep->fetchAll(\PDO::FETCH_OBJ);
            }
            
            return $users;
        }

        public function addUser(string $email, string $first_name, string $last_name,
                                 string $address1, string $address2, string $address3, 
                                 string $username, string $password, string $role, string $phone){
            $sql = "CALL sp_createUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $res = $this->getConnection()->prepare($sql);
            $res->execute([$email, $first_name, $last_name,
                                    $address1, $address2, $address3,
                                    $username, $password, $role, $phone]);
            
            return $res ? 'User successfully added.' : 'User creation failed.';

        }

        public function updateUser(string $user_id, string $email, string $first_name, string $last_name,
                                    string $address1, string $address2, string $address3, 
                                    string $username, string $password, string $role, string $phone){

            $sql = "CALL sp_updateUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $res = $this->getConnection()->prepare($sql);
            $res->execute([$user_id, $email, $first_name, $last_name,
                                $address1, $address2, $address3,
                                $username, $password, $role, $phone]);
            
            return $res ? 'User successfully updated.' : 'User update failed.';
        }

        public function deleteUser(string $user_id){
            $sql = "CALL sp_deleteUser(?)";
            $prep = $this->getConnection()->prepare($sql);
            $res = $prep->execute([intval($user_id)]);
            $res ? 'User successfully deleted.' : 'User deletion failed.';
        }
    }