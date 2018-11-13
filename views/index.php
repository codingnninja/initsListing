<?php 
   
require_once '../classes/init.php';

if(Session::exists('home')){
  echo '<p>' . Session::flash('home','This is successful') . '</p>';
}
 
  $user = new User();
  if($user->isLoggedIn()) {
?>
  <p>Hello <a href="#"><?php echo escape($user->data()->admin_name) ?></a></p>
  <div>
     <div> 
        <h4> Available categories</h4>
        <div> <!--whatever --></div>
         <a href="category">Create a new category</a>
      </div>

      <div>
        <a href="http://localhost/initstest/views/create_listing">Create listing</a>
        <br>
        <a href="http://localhost/initstest/views/logout">log out</a>
        <br>
        <a href="http://localhost/initstest/views/listings">Check all listing</a>
        
      </div>
  </div>
<?php    
  }else{
    echo '<p>You are not logged in.</p> <a href="http://localhost/initstest/views/login">log in </a>';
  }
?>


