<?php
    namespace App\Core\Fingerprint;

    class BasicFingerprintProviderFactory {
        public function getInstance(string $arraySource) : BasicFingerprintProvider {
            switch($arraySource) {
                // U Configuration.php se prosledjuje samo argument [ 'SERVER' ] kako bi aktivirao ovaj case
                case 'SERVER':
                    return new BasicFingerprintProvider($_SERVER);
            }

            return new BasicFingerprintProvider($_SERVER);
        }
    }