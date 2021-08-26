<?php 

    class Validate {

        
        public static function isEmpty($key, $string) {
            if(empty($string)) {
                Message::addError($key, 'Field can\'t be empty');
                return;
            }
        }

        /**
         * if string is empty add error message
         * if string does not match the pattern add error message
         * allowed alfanumeric characters, spaces and / . -
         *
         * @param [string] $key
         * @param [string] $string
         * @return void
         */
        public static function isAlphanumeric($key, $string) {
            if(empty($string)) {
                Message::addError($key, 'Field can\'t be empty');
                return;
            }
            if(!preg_match('/^[a-zA-Z\p{L}0-9\-\/\s,\.\?]*$/u', $string)) {
                Message::addError($key, 'Field can only contain alphanumeric characters and . / ? -');
                return;
            }
        }

        /**
         * validate if user input is numeric
         *
         * @param [string] $key
         * @param [int] $number
         * @return void
         */
        public static function isNumber($key, $number){
            if(!is_numeric($number)) {
                Message::addError($key, 'Unknown value');
                return;
            }
        }

        /**
         * validate length of second argument
         * @param string $key
         * @param string $value
         * @param int $length
         * @return void
         */

        public static function validatelength($key, $value, $length) {
            $numberOfChars = strlen($value);
            if($numberOfChars > $length) {
                Message::addError($key, 'Dozvoljeni broj karaktera je ' . $length);
                return;
            }
        }

        public static function isValidFile($key, $k){
            if($_FILES[$k]['error'] === 4) {
                return;
            }
            $allowed = array('jpg', 'jpeg', 'png');
            $extension = pathinfo($_FILES[$k]["name"], PATHINFO_EXTENSION);
            $extension = strtolower($extension);
            if(!in_array($extension, $allowed) && !empty($extension)) {
                Message::addError($key, 'Sorry, only JPG, JPEG, PNG files are allowed');
                return;
            }
            $mimeType = mime_content_type($_FILES[$k]['tmp_name']);
            $mimeTypeAllowed = array('image/jpg', 'image/jpeg', 'image/png');
            if(!in_array($mimeType, $mimeTypeAllowed) && !empty($mimeType)) {
                Message::addError($key, 'Sorry, only JPG, JPEG, PNG files are allowed');
                return;
            }
            if($_FILES[$k]['error'] != 4 && $_FILES[$k]['error'] != 0) {
                Message::addError($key, 'Something went wrong, try again');
                return;
            }
            if (($_FILES[$k]["size"] > 2000000)) {
                Message::addError($key, 'Image size exceeds 2MB');
                return;
            }
        }
        

    } // end of class
?>