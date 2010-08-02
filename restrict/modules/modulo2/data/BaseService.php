<?php
/**
* Generate by TheWebMind 2.0 Software
* @license	http://www.gnu.org/licenses/gpl.html
* @link http://thewebmind.org
* @author	Jaydson Gomes <jaydson@thewebmind.org>
* @author	Felipe N. Moura <felipe@thewebmind.org>
* BaseService class
* @version  1.0
*/

abstract class BaseService{ 
	
	/**
     * @var Object DAO - Dinamic DAO Object for a class passed to constructor
     */	
	private $DAO;
	
	/**
	 * Abstract methods
     * Must be implemented by the child
     */	
	abstract public function GetById($id);
	abstract public function GetCollection();
	
	/**
	* Constructor
	* @param String $class - The name of class that the Service must instantiate DAO Object
	*/
	public function BaseService($class=null){
		require_once("DAO/".$class."DAO.php");
		$class = ucfirst($class)."DAO";
		$this->DAO = new $class();
	}
	
	/**
	* Get a dinamic DAO Object
	@return DAO
	*/	
	public function DAO(){
		return $this->DAO;
	}
	
	/**
	* Load an Object dynamically, based on the fetched array passed by parameter
	@param Array $arr - The array fetched
	@param String $object - The name of the object class
	@return Object - Object Loaded
	*/
	public function Load($arr,$object){
		$object = ucfirst($object);
		$obj = new $object;
		reset($arr);
		while($cur = current($arr)){
			if(!is_numeric(key($arr)))
				$k = key($arr);
			$obj->$k($cur);
			next($arr);
		}
		return $obj;
	}
}
?>