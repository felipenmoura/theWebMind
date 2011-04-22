<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace theos;
/**
 * Description of Gosh
 *
 * @author felipe
 */
class Gosh {
	public $bdGen= null;
    
    public static function getLobesDir()
    {
        return _MINDSRC_."/mind3rd/API/Lobe/";
    }
	
	public function generate($data)
	{
		//$this->dbGen->generateDatabase(\Mind::$currentProject);
		$program = strtolower(array_shift($data));
		$program = 'Lobe\\'.$program.'\\'.$program;
		if(\class_exists($program))
			new $program($data);
		else
			throw new \MindException("Invalid lobe program: ".$program);
	}
	
	public function __construct(){
	}
}