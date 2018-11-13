<?php
require_once '../classes/init.php';
    $admin = new User();
    if($admin->isLoggedIn()){
        $_db = DB::getInstance();
        //makeIdFromUrl() is declared in functions.php
        $id = makeIdFromUrl();

        $listings = $_db->get('biz_listings', array('biz_id', '=', $id));
        if($listings->results()){

            //To do: this may be done with foreign keys constraint
            $address_id = $listings->results()[0]->address_id;
            $sql2 = "DELETE FROM `biz_addresses` WHERE address_id = $address_id";
            $result = $_db->query($sql2);

            $images = $_db->get('biz_images', array('biz_id', '=', $id));
            foreach ($images->results() as $image) {
                unlink("../uploads/".$image->img_path);
            }

            $sql1 = "DELETE FROM `biz_listings` WHERE biz_id = $id";
            $result = $_db->query($sql1);
            Session::flash('delete', 'You have deleted the business listing successfully');
            
        }else{ 
            Session::flash('delete', 'This business listing doesn\'t match any of our records');
        }
        if(Session::exists('delete')){
             echo '<p>' . Session::flash('delete','') . '</p>
             <a href="http://localhost/initstest/views/listings">go back to listings</a>';
        }   
        
    }
?>