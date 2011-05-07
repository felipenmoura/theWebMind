<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Lobe\testFacade;
/**
 * Description of DBGen
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class testFacade extends \Lobe\Neuron implements \neuron{
    
	// TODO: REMOVE THIS FILE AFTER THE REQUIRED TESTS
	public function __construct(Array $data)
	{
        echo "Here, a list of available commands from the \API package:\n";
        echo "\API\\\n";
        echo "     GET::";
        echo "plugins(true); // passing true, it echoes directly\n";
        echo "          projectData();\n";
        echo "          currentProject();\n";
        echo "          tables();\n";
        echo "          DDL();\n";
        echo "          DecoratedDDL();\n";
        echo "          lobes();\n";
        echo "          source();\n";
        echo "          idioms();\n";
        
        echo "     User::";
        echo "projectsList();\n";
        echo "           usersList();\n";
        
        echo "     FileManager::";
        echo "appendDataToFile(\$file, \$data);\n";
        echo "                  createDir('newDir/anotherNewDir/finalNewDir');\n";
        echo "                  createFile(\$uri); // also accepts nested directories\n";
        echo "                  createXMLFile(\$uri);\n";
        echo "                  writeToFile(\$file, \$data);\n";
        
        echo "     Project::";
        echo "data();\n";
        echo "              current();\n";
        echo "              getDDLCommand();\n";
        echo "              getDDLCommand(false); // not decorated\n";
        echo "              openProject(\$projectName);\n";
        echo "              projectExists(\$projectName);\n";
        echo "              source();\n";
        
	}
}