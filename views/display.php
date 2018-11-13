<?php

require_once '../classes/init.php';
$_db = DB::getInstance();
$user = new User();

    if ($_db) {
        
        $id = makeIdFromUrl();
        $bizID = "bi.biz_id = $id";
        $results = $_db->query($_db->getCustomQuery($bizID));
        $normalizedResults = rewriteRows($results->results());
    }
?>
<form action="" method="post" enctype="multipart/form-data">
    <div class="listing-display">
        <?php
            foreach ($normalizedResults as $key => $data) { 
                $images = array_unique($data['images']);
                $imgHTML = '';
                foreach ($images as $image) {
                    $imgHTML .= '<img src="http://localhost/initstest/uploads/'.$image.'" style ="width:300px;"><br>';
                }

                $categories = array_unique($data['categories']);
                $categoriesHTML = '';
                foreach ($categories as $category) {
                    $categoriesHTML .= ''.$category.',';
                }

                echo '<h3>Name:'.$data['biz_name'].'</h3>
                     <div class="listing-texts">'
                        .$imgHTML.'
                    </div>
                    <div>
                        Address:'.$data['biz_address'].'<br><br>
                        Website:'.$data['biz_website'].'<br><br>
                        Description:'.$data['biz_description'].'<br><br>
                        Email:'.$data['biz_email'].'<br><br>
                        Categories:'.$categoriesHTML.'<br><br>
                    </div>';

                    if ($user->isLoggedIn()) {
                        echo '<h2>Admin sections</h2><div>Views:<span></span>'.$data['views'].'<br></div>
                            <a href="http://localhost/initstest/views/update_listing/'.$data['biz_id'].'">Update post</a>
                            <br>
                            <a href="http://localhost/initstest/views/delete/'.$data['biz_id'].'">Delete post</a><hr>';
                    }else {
                        echo '<hr>';
                        $id = $data['biz_id'];
                        $sql = "UPDATE biz_analytics SET views = views + 1 WHERE biz_id = $id";
                        $a =  $_db->query($sql);
                    }

            }

        ?>
    </div>
    
    <br>
    <a href="http://localhost/initstest/views/listings">go to all listing</a>
</form>

