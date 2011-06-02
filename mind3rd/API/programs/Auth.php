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
	 * This class represents the program auth, receiving the user and
	 * may also receive the password. It will start your session
	 * allowing you to run the restricted programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Auth extends MindCommand implements program
	{
        public $login= null;
        public $pwd= null;
        
		public function action()
		{
            if(!$this->pwd)
                $this->pwd= $this->prompt('pwd',
                                          Mind::write('passwordRequired', false),
                                          true);
            
			if($db = new SQLite3(_MINDSRC_.'/mind3rd/SQLite/mind'))
			{
				$result= $db->query("SELECT * FROM user where login='".$this->login.
									"' AND pwd='".sha1($this->pwd)."' AND status= 'A'");
				$row= $result->fetchArray();
                
                $_SESSION['auth']= JSON_encode($row);
                $_SESSION['pk_user']= $row['pk_user'];
                $_SESSION['status']= $row['status'];
                $_SESSION['login']= $row['login'];
                $_SESSION['type']= $row['type'];
                    
				if(!$row)
				{
					Mind::write('auth_fail', true);
					return false;
				}
			}else{
					 die('Database not found!');
				 }
            Mind::write('autenticated', true, $this->login);
			return $this;
		}
        
        public function __construct()
        {
            
			$this->setCommandName('auth')
				 ->setDescription('Autenticate a user')
				 ->setRestrict(false)
                 ->setAction('action')
				 ->setHelp(<<<EOT
	Sets the user with a password.
	It is required to autenticate, to run most of the commands
EOT
					);
            $this->addRequiredArgument('login', 'Login to access');
            $this->addOptionalArgument('pwd', 'The password may optionaly be passed');
            
            $this->init();
        }
	}
