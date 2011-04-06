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
	
	public function generate()
	{
		$this->dbGen->generateDatabase(\Mind::$currentProject);
	}
	
	public function __construct() {
		$this->dbGen= new DBGen;
	}
}