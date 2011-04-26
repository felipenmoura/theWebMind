<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
	 * This is the server file which will receive the requisition
	 * All the HTTP requests are goning to reach this file, so,
	 * it will treat the POST data before routing the requisition
	 * With this, you can send by post, the program variable, saying
	 * the program you want to execute, and the parameters you want
	 * to pass
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
	 */
	header('Content-type: text/html; charset=utf-8');
	if(!isset($_REQ))
	{
		Mind::write("http_invalid_requisition");
		exit;
	}
	if(!isset($_REQ['data']))
		$_REQ['data']= Array();
	
	foreach($_POST as $k=>$value)
	{
		$_REQ['data'][$k]= preg_replace("/['\"\\\.\/]/", '', $value);
	}

	if(isset($_SESSION['currentProject']))
	{
		$p= Array();
		$p['pk_project']= $_SESSION['currentProject'];
		$p['name']= $_SESSION['currentProjectName'];
		Mind::openProject($p);
	}

	if(isset($app))
	{
		if(!isset($_REQ['data']) || !isset($_REQ['data']['program']))
		{
			Mind::write('programRequired');
			return false;
		}
		$program= $app->findCommand($_REQ['data']['program']);
		$program= $program->getFileName();
		$program = new $program();
		$program->HTTPExecute();
	}