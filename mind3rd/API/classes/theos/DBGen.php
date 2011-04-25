<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
namespace theos;
/**
 * The database Generator.
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class DBGen{
	
	private $dbData= false;
	
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
		\DQB\QueryFactory::buildQuery('*');
		$qrs= \DQB\QueryFactory::getCompleteQuery(false, true, 'array');
		
		$this->dbal->begin();
		foreach($qrs as $qr)
		{
			$exec = $this->dbal->execute($qr);
			
			if($exec === false)
			{
				Mind::write('theosDBQrFail');
				echo $qr."\n";
				Mind::write('theosDBQrFailAbort');
				return false;
			}
		}
		$this->dbal->commit();
		Mind::write('theosDBQrOk');
		return true;
	}
}