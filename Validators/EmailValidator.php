<?php
    namespace App\Validators;

    use App\Core\Validator;

    class EmailValidator implements Validator {
        private $minLength;
        private $maxLength;

        public function __construct() {
        }

        public function &setMinLength(int $value): EmailValidator  {
            $this->minLength = $value;
            return $this;
        }

        public function &setMaxLength(int $value): EmailValidator {
            $this->maxLength = $value;
            return $this;
        }


        public function isValid(string $value): bool {
            $len = strlen($value);
            return ($this->minLength <= $len && $len <= $this->maxLength) && boolval(preg_match('|^([a-z1-9]+\.?+[a-z1-9]?){1,4}+@[a-z1-9]+\.[a-z1-9]+$|', $value));
        }
    }