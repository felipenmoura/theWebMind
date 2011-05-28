<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

	/**
	 * This class represents a model for programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class modeloTeste extends MindCommand implements program
	{
        /*
         * The properties you will use as argument MUST be declared, and public
         */
        public $firstArgument= '';
        
        public function executableFunction()
        {
            // in this example, we will simply show a message using one argument
            echo "   This command has just been executed!\n";
            echo "   With argument: ".$this->firstArgument."\n\n";
            $this->prompt('name', "What's your name?");
            echo "Hello ".$this->answers['name']."\n";
            $this->prompt('pwd', 'Say a magic word!', true);
            $this->prompt('genre', "\nwhat is your genre?",
                          Array('F'=>'Female',
                                'M'=>'Male'));
            echo "\nOW, ok :)\n\n";
        }
        
        public function __construct()
        {
            /**
             * You can use the following structure to set the program behavior
             */
            $this->setCommandName('modeloteste')
                 ->setDescription("This is a model command, only")
                 //->setFileName('modeloTeste') // use this if your class has NOT the same name as its file
                 ->setRestrict(false)
                 ->setHelp("A longer text, explaining the command")
                 ->setAction(function($class){
                     $class->executableFunction();
                   });
            /**
             * Or the following...
             */
            /*
            $this->setCommandName('modeloteste')
                 ->setDescription("This is a model command, only")
                 ->setRestrict(false)
                 ->setHelp("A longer text, explaining the command")
                 ->setAction('executableFunction'); // in this case, the method MUST be public
            */
            
            /**
             * The next commands shows you how to set the signature of you program, such as
             * parameters, options or flags.
             * Your class will receive a property for each parameter, which can be accessed
             * by its argument name(in this example, 'firstArgument'.
             */
            $this->addRequiredArgument('firstArgument',
                                       'first, and required argument',
                                       Array('X', 'Y', 'Z'));
            //$this->addOptionalArgument('secondArgument', 'This is the second and optional argument');
            //$this->addRequiredOption('user', '-u', 'The user who will be passed for any reason', 'root');
            //$this->addOptionalOption('detailed', '-d', 'Should perform its action detailed?', null);
            //$this->addFlag('silent', '-s', 'Executes the command quietly');

            // after all the definition, you MUST initiate your program.
            $this->init();
        }
    }