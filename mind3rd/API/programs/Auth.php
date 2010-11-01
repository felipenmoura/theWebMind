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
	class Auth extends MindCommand implements program
	{
		public function configure()
		{
			$this->setName('auth')
				 ->setDescription('Autenticate a user')
				 ->setRestrict(false)
				 ->setDefinition(Array(
				 	new InputArgument('login', InputArgument::REQUIRED, 'Login to access'),
					new InputArgument('pwd', InputArgument::OPTIONAL, 'The user password')
				 ))
				 ->setHelp(<<<EOT
	Sets the user with a password.
	It is required to autenticate, to run most of the commands
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output)
		{
			if(!$pw= $input->getArgument('pwd'))
			{
				Mind::write('passwordRequired', true);
				$pw= $this->getPassword(true);
			}

			$this->login= $input->getArgument('login');
			$this->pwd= $pw;
			if($this->runAction())
				Mind::write('autenticated', true, $input->getArgument('login'));
		}

		public function HTTPExecute()
		{
			GLOBAL $_REQ;
			if(!isset($_REQ['data']))
			{
				Mind::write('loginRequired');
				return false;
			}elseif(!isset($_REQ['data']['pwd']) || !isset($_REQ['data']['login']))
				{
					Mind::write('loginRequired');
					return false;
				}
			$this->pwd=   $_REQ['data']['pwd'];
			$this->login= $_REQ['data']['pwd'];
			
			if($this->runAction())
				Mind::write('autenticated', true, $_REQ['data']['login']);
		}

		private function action()
		{
			if($db = new SQLiteDatabase(_MINDSRC_.'/mind3rd/SQLite/mind'))
			{
				$result= $db->query("SELECT * FROM user where login='".$this->login.
									"' AND pwd='".sha1($this->pwd)."' AND status= 'A'");
				$row= false;
				while ($result->valid())
				{
					$row = $result->current();
					$_SESSION['auth']= JSON_encode($row);
					$_SESSION['login']= $row['login'];
					break;
				}
				if(!$row)
				{
					Mind::write('auth_fail', true);
					return false;
				}
			}else{
					 die('Database not found!');
				 }
			return $this;
		}

		public function runAction()
		{
			return $this->action();
		}

		/**
		* function taken from: http://www.dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli
		* this method should read the user's password not showing any character of their password
		* @param Boolan $stars if true, show an * for each typed char
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
