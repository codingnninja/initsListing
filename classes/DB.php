<?php 

class DB {
  private static $_instance = null;
  private $_pdo,
          $_query,
          $_error = false,
          $_result,
          $_count = 0;

  private function __construct(){

        try {

             $this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').'; dbname='.Config::get('mysql/db').'', Config::get('mysql/user'), Config::get('mysql/passw'));
          
        }catch(PDOExeption $e){
        	die($e->getMessage());
        }

  }
  
  public static function getInstance(){
                  if(!isset(self::$_instance)){
                  	self::$_instance = new DB();
                  }
               return self::$_instance;   
 
  }

  public function query( $sql, $params = array() ){
  	    $this->_error = false;
  	    if($this->_query = $this->_pdo->prepare($sql)){
  	    	$x = 1;
  	    	
  	    	if(count($params)){
               
  	    		foreach ($params as $param) {
                    //syncing values provided behind the scene to prevent revealing them
  	    	        $this->_query->bindValue($x, $param);

  	    	        $x++;
  	    		}
  	    	}
              
  	    	if($this->_query->execute()){
  	    		$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
  	    		$this->_count = $this->_query->rowCount();
  	    	}else{
  	    		$this->_error = true;
  	    	}

  	    }

  	    return $this;
  }

  public function action($action, $table, $where = array())
  {
       if(count($where === 3)){
              
              $operators = array('=', '>', '<', '>=', '<=');

              $field = $where[0];
              $operator = $where[1];
              $value = $where[2]; 
            
        if (in_array($operator, $operators)) {
          $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

          if(!$this->query($sql, array($value))->error()){

          }
          return $this;
        }
      }
    return false;

  }

  public function update( $table, $table_id, $id, $fields = array()){
  	                $set = '';
  	                $where = '';
  	                $x = 1;

  	              foreach ($fields as $name => $value) {
  	                  $set .= "{$name} = ? ";
  	                  if($x < (count($fields))){
  	                  	$set .= ", ";
  	                  }
                        $x++;
  	               }     
  	           	   $sql = "UPDATE {$table} SET {$set} WHERE  {$table_id} = {$id} " ;
  	           if(!$this->query($sql, $fields)->error()){
              	  return true;
              } 
  	              
            return false;
  }

  public function insert($table, $fields = array()){
          if (count($fields)) {
              $key = array_keys($fields);
              $values = '';
              $x = 1;

              foreach ($fields as $field) {
                 $values .=  '?';
                 if($x < count($fields)){
                 	$values .=', ';
                 }
                 $x++;
              }
            $sql = "INSERT INTO {$table} (`".implode('` , `', $key)."`) VALUES({$values})";
            $inserted = $this->query($sql, $fields);
              if(!$inserted->error()){
                  return $this->_pdo->lastInsertId();
              }

          }
       return false;
  }
//To do: This is hardcoded; it can be better
  public function getCustomQuery($id='bi.biz_id = bl.biz_id'){
              return "SELECT  *  
                      FROM 
                          biz_images bi
                      INNER JOIN
                          biz_listings  bl
                      ON 
                          bi.biz_id = bl.biz_id      
                      INNER JOIN
                          biz_addresses ba
                      ON
                          ba.address_id = bl.address_id
                      INNER JOIN
                          biz_phones  bp
                      ON
                          bi.biz_id = bp.biz_id
                      INNER JOIN
                          biz_analytics ban
                      ON
                          bl.biz_id = ban.biz_id
                      INNER JOIN
                          biz_cat_pivot  bcp
                      ON 
                          bp.biz_id = bcp.biz_id  
                      INNER JOIN 
                              biz_categories bc 
                        ON
                              bcp.bizcat_id = bc.bizcat_id      
                      WHERE 
                            $id";
  }

  public function get($table, $where){
              return $this->action('SELECT *', $table, $where );
  }

  public function delete($table, $where){
              return $this->action('DELETE', $table, $where );
  }

  public function error() {
                    return $this->_error;
  }

  public function count(){
  	          return $this->_count;
  }

  public function results(){
  	return $this->_result;
  }

  public function first(){
  	       return $this->results()[0];
  }

  public function last(){
  	       return $this->results()[$this->count() - 1];
  }

}


?>