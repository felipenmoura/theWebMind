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
        echo "  GET::\n";
        echo "    \API\Get::plugins(true); // passing true, it echoes directly\n";
        echo "    \API\Get::projectData();\n";
        echo "    \API\Get::currentProject();\n";
        echo "    \API\Get::tables();\n";
        echo "    \API\Get::DDL();\n";
        echo "    \API\Get::DecoratedDDL();\n";
        echo "    \API\Get::lobes();\n";
        echo "    \API\Get::source();\n";
        echo "    \API\Get::idioms();\n";
        
        echo "  Project::\n";
        echo "    \API\Project::data();\n";
        echo "    \API\Project::current();\n";
        echo "    \API\Project::getDDLCommand();\n";
        echo "    \API\Project::getDDLCommand(false);\n";
        echo "    \API\Project::openProject('demo_en');\n";
        echo "    \API\Project::projectExists('demo_en');\n";
        echo "    \API\Project::source();\n";
        
        echo "  User::\n";
        echo "    \API\User::projectsList();\n";
	}
}