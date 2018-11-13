<?php
require_once '../classes/init.php';

Class Uploader {
    private $_target_dir = "../uploads/";
    private $_imageFileType;
    private $_target_file;
    private $_image_temp;
    private $_image_size;
    private $_image_name;
    private $_images;
    private $_imagePaths = [];
    private $error = false;
    
    public function images($input, $dbKey, $update=null) {

        if(empty($input)){ return false; } 

        $this->_images = Input::get($input);

        if(!$this->_images && $update !== null) {
             //I don't like using this, but I have to avoid some unnecessary difficulties 
            //insert default image
            $_db = DB::getInstance();
            $_db->insert('biz_images', array(
                'biz_id' => $dbKey,
                'img_path' => 'default.png'
            ));
            return true;
        }

        if (is_array($this->_images)) {
            foreach ($this->_images as $image) {
              $this->init($image, $dbKey, $update);
            }
            return $this->_imagePaths;     
        }     
    }

    public function image($input, $dbKey, $update) {
        $image = Input::getOneImage($input);
        if($image){
            $result = $this->init($image, $dbKey, $update);
            return true;
        }
    }

    protected function init($image, $dbKey, $update) {
           $this->config($image)
                ->isValid()
                ->exists()
                ->hasValidSize()
                ->hasValidFormat()
                ->rename()
                ->upload($dbKey, $update);
    }

    protected function config($image) {
	    //$_FILES[$item]["name"]
        $this->_image_name = $image[0];
		//$_FILES[$item]["tem_name"],
        $this->_image_tmp = $image[1];
        //$_FILES[$item]["size"]
        $this->_image_size = $image[2];
        $this->_target_file = $this->_target_dir . basename($this->_image_name);
        $this->_imageFileType = strtolower(pathinfo($this->_target_file, PATHINFO_EXTENSION));
        return $this;
    }

    public function isValid() {
        // Check if image file is a actual image or fake image
        $check = $this->_image_size;
        ;
        if($check !== false) {
            //image is valid
            $this->error = false;
        } else {
            //image is not valid
            $this->error = true;
        }
        return $this;
    }

    public function exists(){
        // Check if file already exists
        if (file_exists($this->_target_file)) {
            echo "Sorry, file already exists.";
            $this->error = true;
        }
        return $this;
    }

    public function hasValidSize() {
        // Check file size
        if ($this->_image_size > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = false;
        }
        return $this;
    }

    public function hasValidFormat() {
        // Allow certain file formats
        if($this->_imageFileType != "jpg" && $this->_imageFileType != "png" && $this->_imageFileType != "jpeg"
        && $this->_imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $this->error = true;;
        }
        return $this;
    }

    public function rename() {            
            // Get the fileextension
            $ext = pathinfo($this->_image_name, PATHINFO_EXTENSION);   
            // Get filename without extesion
            $filename_without_ext = basename($this->_image_name, '.'.$ext);
            // Generate new filename
            $new_filename = 'initsLimited_' . str_replace(' ', '_', $filename_without_ext) . '_' . time() . '.' . $ext;
            //reset file name
              $this->_image_name = $new_filename;
            // reset the target_file
            $this->_target_file = $this->_target_dir . $new_filename;
            return $this;
    }

    public function upload($dbKey, $update=null) {
        // Check if $uploadOk is set to 0 by an error
        if ($this->error) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($this->_image_tmp, $this->_target_file)) { 
                $_db = DB::getInstance();

                if($update === "update"){
                    $_db->update('biz_images', 'image_id', $dbKey, array(
                        'img_path' => $this->_image_name
                    ));

                } else {
                     $_db->insert('biz_images', array(
                        'biz_id' => $dbKey,
                        'img_path' => $this->_image_name
                     ));
                }

                array_push($this->_imagePaths, $this->_image_name);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
          
        }
    }
}

?>