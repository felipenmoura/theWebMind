<?php
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
