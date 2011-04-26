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
	interface program
	{
		public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output);
		public function HTTPExecute();
		public function configure();
		public function runAction();
	}
