<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

	/**
	 * This class represents the program auth, receiving the user and
	 * may also receive the password. It will start your session
	 * allowing you to run the restricted programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Analyze extends MindCommand implements program
	{
		private $nameSpace= false;
		public function configure()
		{
			$this->setName('analyze')
				 ->setDescription('Analyze the the code for your application')
				 ->setRestrict(true)
				 ->setDefinition(Array(
					new InputArgument('namespace', InputArgument::OPTIONAL, 'Analyze an specific namespace')
				 ))
				 ->setHelp(<<<EOT
	This program will analyze your code, typed on your application directory, on all the files .mnd starting for the main.mnd
	You may have as many files as you want, each of then will be treated as namespaces and will be analyzed too,
	unless you have sent an specific namespace to parse, as argument
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output)
		{
			if(!parent::execute($input, $output))
				return false;
			$this->nameSpace= $input->getArgument('namespace');
			$this->runAction();
		}

		public function HTTPExecute()
		{
			GLOBAL $_REQ;
			if(!parent::HTTPExecute())
				return false;
			if(isset($_REQ['data']['namespace']))
			$this->nameSpace= $_REQ['data']['namespace'];
			$this->runAction();
		}

		private function action()
		{
			if(!isset($_SESSION['currentProject']))
			{
				Mind::write('currentProjectRequired');
				Mind::write('currentProjectRequiredTip');
				return false;
			}
			$srcs= Mind::$currentProject['sources'];
			$main= file_get_contents($srcs.'/main.mnd');

			// search for special/unknown characters
			if(!Mind::$lexer->sweep($main))
				return false;
			// keep substantives and verbs on their canonical form
			// on male singular
			if(!Mind::$canonic->sweep())
				return false;
			// mark specific tokens
			if(!Mind::$tokenizer->sweep())
				return false;
			// prepares the model to be used to process data
			// it transforms the original text into the mind code
			// itself
			//if(!Mind::sintaxer::sweep($main))
				return false;
			// removes the tokens, added before
			//if(!Mind::tokenizer::clear($main))
				return false;

			return $this;
		}

		public function runAction()
		{
			return $this->action();
		}
	}
