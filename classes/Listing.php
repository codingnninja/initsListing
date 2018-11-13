<?php
require_once '../classes/init.php';

class Listing {
    private $_db;

	public function __construct(){
	    $this->_db = DB::getInstance();
	}

    public function search($term){
        $newTerm = "'%".$term."%'";
        
        $sql = "bl.biz_name LIKE $newTerm OR bl.biz_description LIKE $newTerm";
       $result = $this->_db->getCustomQuery($sql);
       return $this->_db->query($result);
    }

    public function handleCategories($categories, $biz_id){
        
        //choose a default category if no category is selected
        if(!$categories) { $categories = ['bizcat' => 1]; }

        //delete the categories for the current biz_id
        $sql = "DELETE FROM `biz_cat_pivot` WHERE biz_id = $biz_id";
        $this->_db->query($sql);

        // Loop to store and display values of individual checked checkbox.
        foreach ($categories as $key => $selected) {             
        //it is easier to insert and delete than to update M:N relationships
                $this->_db->insert('biz_cat_pivot', array(
                    'biz_id' => $biz_id,
                    'bizcat_id' => $selected
                ));
        }

    }

    public function handleImagesUpdate($imagesToUpdate){
        $upload = new Uploader();

        foreach ($imagesToUpdate as $key => $data) {
            //this keys come directly from the database[biz_images: primary key]
            $image_dummy_id = "image_dummy_id".$key;
            $image_selector = "image_id".$key;   
            $isSuccessful = $upload->image($image_selector, $key, 'update');
    
            if($isSuccessful) {
                unlink("../uploads/".$data);
            }
        }

    }

    public function handleNewImagesUpload($listing_images, $biz_id){
        //it is insane to do this
        $upload = new Uploader();
        $upload->images($listing_images, $biz_id);
    }
}

?>