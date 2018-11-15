<?php
    namespace App\Validators;

    use App\Core\Validator;

    class UsernameValidator implements Validator {
        public function isValid(string $value): bool {
            return boolval(preg_match('|^[A-Za-z]{1}[a-z0-9]{5,9}[\_\.]{0,1}[a-z0-9]{0,9}$|', $value));
        }
    }