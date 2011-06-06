<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
namespace Lobe\sql;
/**
 * SQL file generator.
 * Generates the .sql file into docs directory of the project.
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package Lobe
 * @subpackage sql
 */
class sql extends \Lobe\Neuron implements \neuron{
	
	public function __construct(Array $data)
	{
        if(\API\FileManager::writeToFile('docs/create.sql', \API\Get::DDL()))
        {
            echo ".sql file created in docs dir for the current project, with all the DDL commands";
            return true;
        }
        return false;
	}
}