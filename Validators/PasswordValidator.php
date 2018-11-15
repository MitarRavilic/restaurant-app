<?php
    namespace App\Validators;

    use App\Core\Validator;

    class PasswordValidator implements Validator {
        public function isValid(string $value): bool {
            return boolval(preg_match('|^[a-zA-Z0-9\_\.\$\ \-\!\#]{8,32}$|', $value));
        }
    }