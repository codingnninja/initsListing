<?php
require_once '../classes/init.php';


if(Input::exists()){
	
   if (Token::check(Input::get('token'))) {
  
	$validate = new Validation();
	$validation = $validate->check($_POST, array(
        
        'category' => array(
               'required' => true,
               'unique' => 'categories'
        	)    
		));

	if ($validation->passed()) {
		Session::flash('success', 'You validation was successful');
        $_db = DB::getInstance();

        try {
        	$_db->insert('biz_categories', array(
                 'category_name' => Input::get('category')
        		));
        	Session::flash('home', 'You have created the category successfully');
        	Redirect::to('index.php');

        } catch (Exception $e){
        	die($e->getMessage());
        }
	}else{
		foreach ($validation->errors() as $error) {			
			echo $error, '<br>';
		}
	}
  }
}
?>
<form action="" method="post">
    <div class="field">
        <label for="category"> Category name </label>
        <input type="text" class="category" name="category" autocomplete="off"> 
    </div>
    <input type="hidden" name="token" value="<?php echo Token::generate() ?>">
    <input type="submit" value="Add category">

    <br>
    <a href="http://localhost/initstest/views/index">go back to index</a>
</form>