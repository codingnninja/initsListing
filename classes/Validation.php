<?php

class Validation    {
	private $_passed = false,
	        $_errors = array(),
	        $_db = null;


	public function __construct() {
	    $this->_db = DB::getInstance();
	}

	public function check( $source, $items = array()){
		foreach ($items as $item => $rules) {
		        foreach ($rules as $rule => $rule_value) {

		            $value = trim($source[$item]);
		            $item = escape($item);

		            if($rule === 'required' && empty($value)){
		            	$this->addError("{$item} is required");
		            }elseif(!empty($value)){
		            	switch ($rule) {
		            		case 'min':
		            			if (strlen($value) < $rule_value) {
		            				$this->addError("{$item} must not be less than {$rule_value}");
		            			}
		            			break;

		            		case 'max':
		            			
		            			if (strlen($value) > $rule_value) {
		            				$this->addError("{$item} must not be more than 20");
		            			}
		            			break;
		            		
		            		case 'matches':
		            			
		            			if ($value != $source[$rule_value]) {
		            				$this->addError("{$item} must not be equal to the {$rule_value}");
		            			}
		            			break;

		            		 case 'unique':
		            		 $check = $this->_db->get($rule_value, array($item, '=', $value));
		            		    if($check->count()){
		            		    	$this->addError("{$item} already exists in the database");
		            		    }
		            		default:
		            			# code...
		            			break;
		            	}
		            }
		        }
		   }

		   if (empty($this->_errors)) {
		   	   $this->_passed = true;
		   }

		   return $this;
	}

	public function addError($error){
		$this->_errors[] = $error;
	}

	public function errors(){
		return $this->_errors;
	}

	public function passed(){
		return $this->_passed;
	}
}

?>