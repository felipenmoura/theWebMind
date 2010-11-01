<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
	class Auth extends Symfony\Component\Console\Command\Command
	{
		public function configure()
		{
			$this->setName('auth')
				 ->setDescription('Autenticate a user')
				 ->setDefinition(Array(
				 	new InputArgument('login', InputArgument::REQUIRED, 'Login to access')
				 ))
				 ->setHelp(<<<EOT
	Sets the user with a password.
	It is required to run most of the commands
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
		{
			Mind::write('passwordRequired', true);
			$pw= $this->getPassword(true);
			Mind::write('autenticated', true, $input->getArgument('login'));
			Mind::write("xxxxxx");
			//echo "\n[OK] ".$input->getArgument('login')." ".Mind::write('autenticated')."\n";
		}
		
		/**
		* function taken from: http://www.dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli
		* this method should read the user's password not showing any character of their password
		* @param Booladn $stars if true, show an * for each typed char
		* @return String password
		*/
		private function getPassword($stars = false)
		{
			// Get current style
			$oldStyle = shell_exec('stty -g');

			if ($stars === false) {
				shell_exec('stty -echo');
				$password = rtrim(fgets(STDIN), "\n");
			} else {
				shell_exec('stty -icanon -echo min 1 time 0');

				$password = '';
				while (true) {
				    $char = fgetc(STDIN);

				    if ($char === "\n") {
				        break;
				    } else if (ord($char) === 127) {
				        if (strlen($password) > 0) {
				            fwrite(STDOUT, "\x08 \x08");
				            $password = substr($password, 0, -1);
				        }
				    } else {
				        fwrite(STDOUT, "*");
				        $password .= $char;
				    }
				}
			}

			// Reset old style
			shell_exec('stty ' . $oldStyle);

			// Return the password
			return $password;
		}
	}
