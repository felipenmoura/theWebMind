<?php
/**
* Generate by TheWebMind 2.0 Software
* @license	http://www.gnu.org/licenses/gpl.html
* @link http://thewebmind.org
* @author	Jaydson Gomes <jaydson@thewebmind.org>
* @author	Felipe N. Moura <felipe@thewebmind.org>
* Base class PDOConnectionFactory
* @version  1.0
*/
class PDOConnectionFactory{
	
	/**
     * @var Connection
     */
	public $con = null;
	
	/**
     * @var String
     */
	public $dbType 	= "<dbType>";
  
	/**
     * @var String
     */
	public $host 	= "<host>";
	
	/**
     * @var String
     */
	public $user 	= "<user>";
	
	/**
     * @var String
     */
	public $senha 	= "<pass>";
	
	/**
     * @var String
     */
	public $db		= "<db>";
	
	/**
     * @var int
     */
	public $port	= "<port>";
 
	/**
     * @var Boolean
     */
	public $persistent = false;
 	
	/**
	* PDO Factory
	@param Boolean $persistent
	*/
	public function PDOConnectionFactory( $persistent=false ){
		if( $persistent != false){ $this->persistent = true; }
	}
 	
	/**
	* Connection
	@return Object 	
	*/
	public function getConnection(){
		try{
			$this->con = new PDO($this->dbType.":host=".$this->host.";dbname=".$this->db, $this->user, $this->senha, 
			array( PDO::ATTR_PERSISTENT => $this->persistent ));
			return $this->con;
		}catch ( PDOException $ex ){  echo "Error: ".$ex->getMessage(); }
	}
 
	/**
	* Disconnect
	*/
	public function Close(){
		if( $this->con != null )
			$this->con = null;
	}
}
?>