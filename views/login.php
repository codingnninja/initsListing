<?php 
require_once '../classes/init.php';

if(Input::exists()){
    
    if(Token::check(Input::get('token'))) {
        $validate = new Validation();
        $validation = $validate->check($_POST, array(
            'admin_name'=> array('required' => true),
            'admin_pass'=> array('required' => true)
        ));

        if($validation->passed()) {
            //login user
            $user = new User();
            $login = $user->login( Input::get('admin_name'), Input::get('admin_pass'), true);
            if($login) {
                Redirect::to('index.php');
            }else{
                echo '<p>Sorry we are on able to log you in</p>';
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
        <label for="admin_name"> Admin Name </label>
        <input type="text" class="admin_name" name="admin_name" autocomplete="off" value="<?php echo escape(Input::get('admin_name')); ?>"> 
    </div>

    <div class="field">
        <label for="admin_pass"> Admin Password </label>
        <input type="password" class="admin_pass" name="admin_pass" autocomplete="off"> 
    </div>

    <input type="hidden" name="token" value="<?php echo Token::generate() ?>">

    <input type="submit" value="login"><br><hr>
     OR
    <a href="http://localhost/initstest/views/register">Signup</a>

</form>
