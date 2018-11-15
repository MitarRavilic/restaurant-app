<?php
    namespace App\Validators;

    use App\Core\Validator;

    class NumberValidator implements Validator {
        private $unsigned;
        private $real;
        private $maxIntegerDigitCount;
        private $maxDecimalDigitCount;

        public function __construct() {
            $this->unsigned = true;
            $this->real     = false;
            $this->maxIntegerDigitCount = 11;
            $this->maxDecimalDigitCount = 2;
        }

        public function &allowUnsigned(): NumberValidator {
            $this->unsigned = true;
            return $this;
        }

        public function &allowSigned(): NumberValidator {
            $this->unsigned = false;
            return $this;
        }

        public function &allowInteger(): NumberValidator {
            $this->real = false;
            return $this;
        }

        public function &allowDecimal(): NumberValidator {
            $this->real = true;
            return $this;
        }

        public function &setMaxIntegerDigitCount(int $value): NumberValidator {
            $this->maxIntegerDigitCount = max($value, 1);
            return $this;
        }

        public function &setMaxDecimalDigitCount(int $value): NumberValidator {
            $this->maxDecimalDigitCount = max($value, 0);
            return $this;
        }

        public function isValid(string $value): bool {
            $pattern = '|^';

            if ($this->unsigned === false) {
                $pattern .= '\-?';
            }

            $pattern .= '[1-9][0-9]{0,' . ($this->maxIntegerDigitCount-1) . '}';

            if ( $this->real === true ) {
                $pattern .= '\.[0-9]{0,' . $this->maxDecimalDigitCount . '}';
            }

            $pattern .= '$|';

            return boolval(preg_match($pattern, $value));
        }
    }
