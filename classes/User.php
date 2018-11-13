<?php

class User {
	private  $_db,
	        $_data,
	        $_sessionName,
	        $_cookieName,
			$_isLoggedIn;

	public function __construct($user = null){
	    $this->_db = DB::getInstance();

	    $this->_sessionName = Config::get('session/session_name');
	    $this->_cookieName = Config::get('remember/cookie_name');

	    if (!$user) {
	    	  if(Session::exists($this->_sessionName)){
	    	  	$user = Session::get($this->_sessionName);
	    	  	if ($this->find($user)) {
	    	  		$this->_isLoggedIn = true;
	    	  	}else{
	    	  		//process logout
	    	  	}

	    	  }
	    }else{
	    	$this->find($user);
	    };
	}

	public function update($fields = array(), $id = null){
        
        if (!$id && $this->isLoggedIn()) {
        	$id = $this->data()->id;
        }

		if ($this->_db->update('biz_admins', $id, $fileds)) {
		    throw new Exception("Error Processing Request");
		    
		}
	}

	/*public function hasPermission($key){
		$group = $this->db->get('group', array('id', '=', $this->data()->group));
		if ($group->count()) {
			$permissions = json_decode($this->$group->first()->permissions, true);  
		}

		if ($permissions[$key] == true) {
			return true;
		}
		return false;
	}*/

	public function create($fields = array()){
		if(!$this->_db->insert('biz_admins', $fields)){
			throw new Exception("There is problem creating an accout for you");
		}
	}

	public function find($user = null){
		if($user){
			$field = (is_numeric($user)) ? 'admin_id' : 'admin_name';
			$data = $this->_db->get('biz_admins', array($field, '=', $user));

			if($data->count()){
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function login($username = null, $password = null, $remember = false){
	  if (!$username && !$password && $this->exists()) {
	      Session::put($this->_sessionName, $this->data()->admin_id);
	  }else{
		
		$user = $this->find($username);

		if($user){
			if($this->data()->admin_pass === Hash::make($password, $this->data()->salt)){
				Session::put($this->_sessionName, $this->data()->admin_id);
				$hash = Cookie::get(Config::get('remember/cookie_name'));
						if ($remember && !$hash) {
						$hash = Hash::unique();
						$hashCheck = $this->_db->get('biz_admins', array('admin_id', '=', $this->data()->admin_id));
						if($hashCheck->count()){
							$session = $this->_db->insert('admin_sessions', array(
								'admin_id' => $this->data()->admin_id,
								'hash' => $hash,
								));
							if(!$session){ return false; }
					   
						}else{
							$hash = $hashCheck->first()->hash;
						}

						Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
						}

				return true;
			}

		}
   
	}
	return false;
 }
	public function logout(){
        $this->_db->delete('admin_session', array('admin_id', '=', $this->data()->admin_id));

		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);

		return true;
	}

	public function exists(){
		return (!empty($this->_data)) ? true : false;
	}

	public function data(){
		return $this->_data;
	}

	public function isLoggedIn(){
		return $this->_isLoggedIn;
	}
}

?>