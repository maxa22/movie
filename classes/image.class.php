<?php 

    class Image {

        public $imagePath;
        public $filePath;
        private $uploadedFileKey;

        public function __construct($key) {
               
            $this->uploadedFileKey = $key;
            $this->filePath = $this->setFilePath();
            $this->setImagePath($this->filePath);
            $this->saveImageFile();
        }

        /**
         * set file path depending on fileName
         * if fileName exists add time() to fileName
         * @return $filePath[string] - path image
         */
 
        public function setFilePath() {

            $fileName = $_FILES[$this->uploadedFileKey]['name'];

            if(file_exists('../images/' . $fileName)) {
                $filePath = '../images/' . time() . $fileName;
            } else {
                $filePath = '../images/' . $fileName;
            }
            return $filePath;
        }

        /**
         * save image file to images folder
         */

        public function saveImageFile() {
            move_uploaded_file($_FILES[$this->uploadedFileKey]['tmp_name'], $this->filePath);
        }

        /**
         * set image name that should be located inside images folder
         */

        public function setImagePath() {
            $this->imagePath = explode('/', $this->filePath)[2];
        }
    }

?>