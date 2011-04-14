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
			if(!parent::execute($input, $output))
				return false;
			if(!$pw= $input->getArgument('pwd'))
			{
				Mind::write('passwordRequired', true);
				$pw= Mind::readPassword(true);
			}

			$this->login= $input->getArgument('login');
			$this->pwd= $pw;
			if($this->runAction())
				Mind::write('autenticated', true, $input->getArgument('login'));
		}

		public function HTTPExecute()
		{
			if(!parent::HTTPExecute())
				return false;
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
			if($db = new SQLite3(_MINDSRC_.'/mind3rd/SQLite/mind'))
			{
				$result= $db->query("SELECT * FROM user where login='".$this->login.
									"' AND pwd='".sha1($this->pwd)."' AND status= 'A'");
				$row= $result->fetchArray();
				/*while()
				{
					$row = $result->current();*/
					$_SESSION['auth']= JSON_encode($row);
					$_SESSION['pk_user']= $row['pk_user'];
					$_SESSION['status']= $row['status'];
					$_SESSION['login']= $row['login'];
					/*break;
				}*/
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
			$ret= $this->action();
			parent::runAction();
			return $ret;
		}
	}
