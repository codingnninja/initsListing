<?php

require_once '../classes/init.php';
$_db = DB::getInstance();
$user = new User();

if(!$user->isLoggedIn()){
    Redirect::to('login');
}{
    $hasCategories = $_db->get('biz_categories', array('bizcat_id', '>', 0))->results();
    
    if(!$hasCategories) {
        Session::flash('success', 'You validation was successful');
        Redirect::to('category');
    }
}

if (Input::exists() && $user->isLoggedIn()) {
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
                $result1 = $_db->insert('biz_addresses', array(
                    'biz_address' => Input::get('listing_address')   
                    ));
                if(!$result1) { return; }
                $result2 = $_db->insert('biz_listings', array(
                    'admin_id' =>  $admin_id ,  
                    'address_id' => $result1,  
                    'biz_name' => Input::get('listing_name'), 
                    'biz_description' => Input::get('listing_description'),
                    'biz_email' => Input::get('listing_email'),
                    'biz_website' => Input::get('listing_web')
                    ));

                if ($result2) {
                    $biz_id = (int) $result2;

                    $result1 = $_db->insert('biz_phones', array(
                        'biz_id' => $biz_id ,
                        'first_phone' => Input::get('listing_first_phone'),
                        'second_phone' => Input::get('listing_second_phone'),
                    ));

                    $categories = Input::get('biz_categories');
                    if (!$categories) {
                        $categories = ['bizcat' => 1];
                    }
                    // Loop to store and display values of individual checked checkbox.
                    foreach ($categories as $selected) {
                        var_dump($categories);
                        $_db->insert('biz_cat_pivot', array(
                            'biz_id' => $biz_id,
                            'bizcat_id' => $selected
                        ));
                    }

                    $upload = new Uploader();
                    $upload->images('listing_images', $biz_id, 'notNull');
                
                    // Loop to store and display values of individual path.
                    /* if(!$paths) { $paths = ['path' => '../upload/default.jpg']; }
                      foreach ($paths as $path) {
                          $_db->insert('biz_images', array(
                              'biz_id' => $biz_id,
                              'img_path' => $path
                          ));*/

                    $_db->insert('biz_analytics', array(
                        'biz_id' => $biz_id,
                        'views' => 0
                    ));
    
                    Session::flash('home', 'You have created the business listing successfully');
                    Redirect::to('http://localhost/initstest/views/index');
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
        <input type="text" class="listing_name" name="listing_name" autocomplete="off" value="<?php echo escape(Input::get('listing_name')); ?>"> 
    </div>
    <div class="field">
        <label for="listing_description"> Listing description </label>
        <input type="text" class="listing_description" name="listing_description" autocomplete="off" value="<?php echo escape(Input::get('listing_description')); ?>"> 
    </div>
    <div class="field">
        <label for="listing_web"> Listing website </label>
        <input type="text" class="listing_web" name="listing_web" autocomplete="off" value="<?php echo escape(Input::get('listing_web')); ?>"> 
    </div>
    <div class="field">
        <label for="listing_email"> Listing email</label>
        <input type="email" class="listing_email" name="listing_email" autocomplete="off" value="<?php echo escape(Input::get('listing_email')); ?>"> 
    </div>
    <div class="field">
        <label for="listing_first_phone"> Listing first phone </label>
        <input type="number" class="listing_first_phone" name="listing_first_phone" autocomplete="off" value="<?php echo escape(Input::get('listing_first_phone')); ?>"> 
    </div>
    <div class="field">
        <label for="listing_second_phone"> Listing second phone </label>
        <input type="number" class="listing_second_phone" name="listing_second_phone" autocomplete="off" value="<?php echo escape(Input::get('listing_second_phone')); ?>"> 
    </div>
    <div class="field">
        <label for="listing_address"> Listing address </label>
        <input type="text" class="listing_address" name="listing_address" autocomplete="off" value="<?php echo escape(Input::get('listing_address')); ?>"> 
    </div>
    <div class="field">
        <label for="listing_category"> Choose categories </label><br/>
        <?php

        if(count($hasCategories)){
            foreach ($hasCategories as $data) {
                echo "<input type='checkbox' name='biz_categories[]' value='$data->bizcat_id'>
                <label>{$data->category_name}</label><br/>";
            }
        }
        ?>
    </div>
     <div class="field">
        <label for="listing_images"> Listing images </label>
        <input type="file" class="listing_images" name="listing_images[]" multiple> 
    </div>
    
    <input type="hidden" name="token" value="<?php echo Token::generate() ?>">
    <input type="submit" value="Create listing">

    <br><br>
    <a href="index">go back to index</a>
</form>

