<?php
    namespace App\Controllers;

    use App\Validators\PasswordValidator;
    use App\Validators\StringValidator;
    use App\Validators\EmailValidator;
    use App\Validators\UsernameValidator;
    use App\Models\UserModel;
    use \App\Models\OrderModel;
    use \App\Models\ItemModel;
    use \App\Utility\JsonUtility;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    use App\Core\Role\DispatcherRoleController;

    class DispatcherDashboardController extends DispatcherRoleController {
        public function home() { 

        }

        public function processOrder() {
            $utility = new JsonUtility();
            $order_process = $utility->unpackSingleFromJson($utility->loadRawFromJson('order_process'));
            $orderModel = new OrderModel($this->getDatabaseConnection());
            $orderModel->updateOrderIsAccepted(intval($order_process->order_id), intval($order_process->is_accepted), $order_process->delivery_at);
            $this->notifyUser($order_process->order_id, $order_process->is_accepted, $order_process->delivery_at);
        }


        private function notifyUser(int $order_id, int $is_accepted, string $delivery_at){
           
            $orderModel = new OrderModel($this->getDatabaseConnection());
            $email = $orderModel->getEmailFromOrder($order_id);
            $mailer = new PHPMailer(true);

            //primalac
            $mailer->addAddress($email);

            //sadrzaj
            //todo: prikazi sadrzaj porudzbine
            
            $subject = 'Vasa porudzbina je prihvacena';
            $body = '<p>Vasa porudzbina je <b>prihvacena</b>. Dostava u ' . $delivery_at . '</p>';
            $alt_body = 'Vasa porudzbina je prihvacena';
            if(!$is_accepted){
                $subject = 'Vasa porudzbina je odbijena';
                $body = '<p>Vasa porudzbina je <b>odbijena</b>.</p>';
                $alt_body = 'Vasa porudzbina je odbijena';
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $alt_body;
            $mail->send();
        }
        
        public function markOrderAsDelivered(int $order_id){
            $orderModel = new OrderModel($this->getDatabaseConnection());
            $orderModel->updateOrderIsDelivered($order_id);
        }
        
    }