<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     *
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	$_REQ= Array();
	$_REQ['request_method']= $_SERVER['REQUEST_METHOD'];
	$_REQ['env']= 'http';
	define('_MINDSRC_', getcwd());
    
	switch($_REQ['request_method'])
	{
		case 'GET' : $_REQ['data']= $_GET;
					 break;
		case 'POST': $_REQ['data']= $_POST;
					 break;
		case 'PUT' : parse_str(file_get_contents('php://input'), $put_vars);
		             $_REQ['data'] = $put_vars;
		             break;
		default    :
					 $_REQ['data']= null;
	}
	include('mind3rd/API/utils/utils.php');