<?php
    namespace App\Validators;

    use App\Core\Validator;

    class StringValidator implements Validator {
        private $minLength;
        private $maxLength;
        private $onlyAlpha;
        private $onlyNum;

        public function __construct() {
            $this->onlyAlpha = false;
            $this->onlyNum   = false;
        }

        public function &setMinLength(int $value): StringValidator  {
            $this->minLength = $value;
            return $this;
        }

        public function &setMaxLength(int $value): StringValidator {
            $this->maxLength = $value;
            return $this;
        }
        public function &setOnlyAlpha(): StringValidator{
           // if($this->onlyAlpha==false && $this->onlyNum==false){
                $this->onlyAlpha=true;
                return $this;
           // }
        }
        public function &setOnlyNum(): StringValidator{
           // if($this->onlyAlpha==false && $this->onlyNum==false){
                $this->onlyNum=true;
                return $this;
           // }
        }

        public function isValid(string $value): bool {
           $value = filter_var($value, FILTER_SANITIZE_STRING);
            $len = strlen($value);
            if($this->onlyAlpha==true) {
                return ($this->minLength <= $len && $len <= $this->maxLength) && \boolval(ctype_alpha(str_replace(' ', '', $value)));
            }
            if($this->onlyNum==true) {
                return ($this->minLength <= $len && $len <= $this->maxLength) && \boolval(ctype_digit(str_replace(' ', '', $value)));
            }
           
            return $this->minLength <= $len && $len <= $this->maxLength;
        }
    }