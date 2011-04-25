<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
namespace theos;
/**
 * The creator.
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