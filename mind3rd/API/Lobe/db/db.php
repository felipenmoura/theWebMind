<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Lobe\db;
/**
 * Description of DBGen
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
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
		$qrs= \DQB\QueryFactory::buildRawQuery();
        
		$this->dbal->begin();
        $dealer= new resources\DBDealer($this->dbal);
        
		foreach($qrs['createTable'] as $tbName=>$qrObject)
		{
            if(!$dealer->createTable($qrObject))
            {
                return false;
            }
		}
		$this->dbal->commit();
		\Mind::write('theosDBQrOk');
		return true;
	}
}