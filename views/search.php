<?php

require_once '../classes/init.php';
$_db = DB::getInstance();
$user = new User();

    if ($_db) {
        
        $request = $_GET['search'];
        $searcher = new Listing();

        $isSuccessful = $searcher->search($request);

        if ($isSuccessful) {
            $normalizedResults = rewriteRows($isSuccessful->results());
        }
    }
?>
    <form action="/initstest/views/search" method="get">
        <input type="text" name="search" autocomplete="off">
        <input type="submit" value="search">
    </form>

    <div class="listing-display">
        <?php
            foreach ($normalizedResults as $key => $data) {
                $images = array_unique($data['images']);
                $imgHTML = '';
                foreach ($images as $image) {
                    $imgHTML .= '<br><img src="http://localhost/initstest/uploads/'.$image.'" style ="width:300px;">';
                }

                echo '<div class="listing-texts"><h3>Business name:'.$data['biz_name'].'</h3><br>'
                        .$imgHTML.'
                    </div>
                    <div><br>
                        <a href="http://localhost/initstest/views/display/'.$data['biz_id'].'">view</a>
                    </div><hr>';
            }

        ?>
    </div>
    
    <br>
    <a href="http://localhost/initstest/views/index">go to admin dashboard</a>

