<?php

require_once '../classes/init.php';
$_db = DB::getInstance();
$user = new User();

if (!$user->isLoggedIn()) {
    Redirect::to('login');
}else{
    
        $id = makeIdFromUrl();
        $bizID = "bi.biz_id = $id";
        
        $results = $_db->query($_db->getCustomQuery($bizID));
        $normalizedResults = rewriteRows($results->results());

        $results = $_db->query($_db->getCustomQuery($bizID));
        $normalizedResults = rewriteRows($results->results());
        $getCategories = $_db->get('biz_categories', array('bizcat_id', '>', 0));
        $getCategoriesR = $getCategories->results();
}

if (Input::exists()) {
    //Check token to prevent cross site request forgery

    if (Token::check(Input::get('token'))) {
        $validate = new Validation();
        $validation = $validate->check($_POST, array(
        
        'listing_name' => array(
               'required' => true
            ),
        'listing_description' => array(
               'required' => true,
               'max' => 200
            ),
        'listing_address' => array(
               'required' => true
            ),
        'listing_web' => array(
               'required' => true
            ),
        'listing_email' => array(
               'required' => true
            ),
        'listing_first_phone' => array(
               'required' => true,
               'max' => 15
            ),
        'listing_second_phone' => array(
               'required' => true,
               'max' => 15
            )
        ));

        if ($validation->passed()) {
            $admin_id = (int) $user->data()->admin_id;

            Session::flash('success', 'You validation was successful');
            try {
                //To do: Adding automated transaction and backup excecution
                 $_db->update(
                    'biz_addresses', 
                    'address_id', 
                    $normalizedResults[1]['address_id'], 
                    array('biz_address' => Input::get('listing_address')));
                
                //get the current $biz_id
                $biz_id = $normalizedResults[1]['biz_id'];

                $result = $_db->update('biz_listings', 'biz_id', 
                        $biz_id,
                        array(
                        'biz_name' => Input::get('listing_name'),
                        'biz_description' => Input::get('listing_description'),
                        'biz_email' => Input::get('listing_email'),
                        'biz_website' => Input::get('listing_web')
                    ));
            
                if ($result) {
            
                    $_db->update('biz_phones', 'biz_id',
                         (int)$biz_id, array(
                        'first_phone' => Input::get('listing_first_phone'),
                        'second_phone' => Input::get('listing_second_phone'),
                    ));

                    //get the selected categories
                    $categories = Input::get('biz_categories'); 
                    $listing = new Listing();
                    $listing->handleCategories($categories, $biz_id);
                    //get images from the data got from the db
                    $imagesToUpdate = $normalizedResults[1]['images'];
                    $listing->handleImagesUpdate($imagesToUpdate);
                    
                    //upload chosen images
                    $listing->handleNewImagesUpload('listing_images', $biz_id);
     
                    Session::flash('home', 'You have updated the business listing successfully');
                    Redirect::to('http://localhost/initstest/views/index
');
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } else {
            foreach ($validation->errors() as $error) {
                echo $error, '<br>';
            }
        }
    }
}
?>
<form action="" method="post" enctype="multipart/form-data">
    <div class="field">
        <label for="listing_name"> Listing name </label>
        <input type="text" class="listing_name" name="listing_name" autocomplete="off" value="<?php echo escape($normalizedResults[1]['biz_name']); ?>"> 
    </div>
    <div class="field">
        <label for="listing_description"> Listing description </label>
        <input type="text" class="listing_description" name="listing_description" autocomplete="off" value="<?php echo escape($normalizedResults[1]['biz_description']); ?>"> 
    </div>
    <div class="field">
        <label for="listing_web"> Listing website </label>
        <input type="text" class="listing_web" name="listing_web" autocomplete="off" value="<?php echo escape($normalizedResults[1]['biz_website']); ?>"> 
    </div>
    <div class="field">
        <label for="listing_email"> Listing email</label>
        <input type="email" class="listing_email" name="listing_email" autocomplete="off" value="<?php echo escape($normalizedResults[1]['biz_email']);
 ?>"> 
    </div>
    <div class="field">
        <label for="listing_first_phone"> Listing first phone </label>
        <input type="number" class="listing_first_phone" name="listing_first_phone" autocomplete="off" value="<?php echo escape($normalizedResults[1]['first_phone']); ?>"> 
    </div>
    <div class="field">
        <label for="listing_second_phone"> Listing second phone </label>
        <input type="number" class="listing_second_phone" name="listing_second_phone" autocomplete="off" value="<?php echo escape($normalizedResults[1]['first_phone']); ?>"> 
    </div>
    <div class="field">
        <label for="listing_address"> Listing address </label>
        <input type="text" class="listing_address" name="listing_address" autocomplete="off" value="<?php echo escape($normalizedResults[1]['biz_address']); ?>"> 
    </div>
    <div class="field">
        <label for="listing_category"> Choose categories </label><br/>
        <?php   

            if(count($getCategoriesR)){
                foreach ($getCategoriesR as $data) {
                    $found = in_array($data->category_name, $normalizedResults[1]['categories']);
                    if ($found) {
                        echo "<input type='checkbox' name='biz_categories[]' value='{$data->bizcat_id}' checked='true'><label>
                        {$data->category_name}</label><br/>";
                    } else {
                        echo "<input type='checkbox' name='biz_categories[]' value='{(int)$data->bizcat_id}'>
                        <label>{$data->category_name}</label><br/>";
                    }
                }
            }
        ?>
    </div>
     <div class="field"><br>
        <label for="listing_images"> <h5>Listing images to update</h5> </label><br>
        <?php  
            foreach ($normalizedResults[1]['images'] as $key => $data) {
                echo "<img src='http://localhost/initstest/uploads/{$data}' style ='width:150px;'>
                <input type='file' class='listing_images' name='image_id{$key}'><br>";
            }
        ?>
    </div>

    <div class="field">
        <label for="listing_images"> <h5>Add more images</h5> </label><br>
        <input type="file" class="listing_images" name="listing_images[]" multiple> 
    </div>
    
    <input type="hidden" name="token" value="<?php echo Token::generate() ?>"><br>
    <input type="submit" value="Update listing">

    <br><br>
    <a href="index">go back to index</a>
</form>

