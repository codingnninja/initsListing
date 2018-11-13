<?php
require_once 'init.php';

function escape($string){
	return HTMLENTITIES($string, ENT_QUOTES, 'UTF-8');
}
/*
 * create a  simple route
 @param $request string
 @return void
 */
function route($request){
	$pathFragments = array_filter(explode('/', $request));
	//get array length
	$lastFragmentPos = count($pathFragments);
	$check = $pathFragments[$lastFragmentPos];
	$secondToLastFrag = $pathFragments[$lastFragmentPos - 1];
           
	if($secondToLastFrag == "delete" || $secondToLastFrag == "update_listing" || $secondToLastFrag == "display"){
		if($check !== "index"){
			array_pop($pathFragments);
			$lastFragmentPos = $lastFragmentPos - 1;
		}
	}
 
	$pathFragment = $pathFragments[$lastFragmentPos];
	
	$whiteListedRoutes = [
		'index' => '../views/index.php',
		'login' => '../views/login.php',
		'logout' => '../views/logout.php',
		'register' => '../views/register.php',
		'create_listing' => '../views/create_listing.php',
		'category' => '../views/category.php',
		'display' => '../views/display.php',
		'update_listing' => '../views/update_listing.php',
		'delete' => '../views/delete.php',
		'listings' => '../views/listings.php',
		'search' => '../views/search.php'
	];

	$isPresent = array_key_exists($pathFragment, $whiteListedRoutes);
	$error404 = '../views/404.php';
	$errorFreePath = ( !$isPresent ) ? $error404 : $whiteListedRoutes[$pathFragment];

	require_once $errorFreePath;
}

/*
*convert table rows to multidimensional array
*@param $tablaRows
*@return array
*/

function rewriteRows($rows){
	$newRowInfo = [];
	$newRowKey = [];
	$newKey = 0;

	foreach($rows as $rowKey => $rowValue){

		if(!in_array($rowValue->biz_id, $newRowKey)){
			++$newKey;
			$newRowInfo[$newKey]["biz_id"] = $rowValue->biz_id;
			$newRowInfo[$newKey]["biz_name"] = $rowValue->biz_name;
			$newRowInfo[$newKey]["biz_description"] = $rowValue->biz_description;
			$newRowInfo[$newKey]["biz_website"] = $rowValue->biz_website;
			$newRowInfo[$newKey]["biz_address"] = $rowValue->biz_address;
			$newRowInfo[$newKey]["address_id"] = $rowValue->address_id;
            $newRowInfo[$newKey]["first_phone"] = $rowValue->first_phone;
            $newRowInfo[$newKey]["biz_email"] = $rowValue->biz_email;
            $newRowInfo[$newKey]["views"] = $rowValue->views;
				
		}
		
		$newRowInfo[$newKey]["images"][$rowValue->image_id] = $rowValue->img_path;
		$newRowInfo[$newKey]["categories"][$rowValue->bizcat_id] = $rowValue->category_name;
		$newRowKey[]  = $rowValue->biz_id;
	}
	return $newRowInfo;
}

function makeIdFromUrl(){
        $request = $_SERVER['REDIRECT_URL'];
        $requestToArray = explode('/', $request);
        $id = array_pop($requestToArray);
		return $id;
}

?>