<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 * @package Lobe
 * @subpackage db
 */
namespace Lobe\db;
/**
 * Database Generator.
 * Generates/manages the database according to the current project's configuration.
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package Lobe
 * @subpackage db
 */
class db extends \Lobe\Neuron implements \neuron{
	
	private $dbData= false;
	
	public function __construct(Array $data)
	{
		$projectData= \Mind::$currentProject;
		$this->generateDatabase($projectData);
	}
	
	public function generateDatabase(Array $projectData)
	{
		$ar= Array(
			'driver'=>$projectData['database_drive'],
			'dbName'=>$projectData['database_name'],
			'host'  =>$projectData['database_addr'],
			'port'  =>$projectData['database_port'],
			'user'  =>$projectData['database_user'],
			'pwd'   =>$projectData['database_pwd']
		);
		$this->dbData= $ar;
		$this->dbal= new \MindDBAL($ar);
		\DQB\QueryFactory::$showHeader= false;
		\DQB\QueryFactory::setUp($ar['driver']);
		\DQB\QueryFactory::buildQuery();
		$qrs= \DQB\QueryFactory::getCompleteQuery(false, true, 'array');
		//$qrs= \DQB\QueryFactory::buildRawQuery();
        
		$this->dbal->begin();
        $dealer= new resources\DBDealer($this->dbal);
        
		foreach($qrs as $tbName=>$qr)
		{
            if(!$dealer->createTable($qr))
            {
                return false;
            }
		}
		$this->dbal->commit();
		\Mind::write('theosDBQrOk');
		return true;
	}
}