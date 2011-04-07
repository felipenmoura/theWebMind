<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Lobe\sql;
/**
 * Description of sql
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class sql extends \Lobe\Neuron implements \neuron{
	
	public function __construct(Array $data)
	{
		$projectData= \Mind::$currentProject;
		\DQB\QueryFactory::$showHeader= true;
		\DQB\QueryFactory::setUp(\Mind::$currentProject['database_drive']);
		\DQB\QueryFactory::buildQuery('*');
		$qrs= \DQB\QueryFactory::getCompleteQuery(false, true, 'string');
		
		$file= \theos\ProjectFileManager::createFile('docs/create.sql');
		\fwrite($file, $qrs);
	}
}