<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

/**
 * Class responsable to create:
 * Projects
 * User
 *
 * @author felipe
 */
class Create extends MindCommand implements program
{
	public $what= null;
	public $argname= false;
	public $info= "";
	private $userType= null;

	public function configure()
	{
		$this->setName('create')
			 ->setDescription('Create structures, such as project or user')
			 ->setRestrict(true)
			 ->setDefinition(Array(
				 new InputArgument('what', InputArgument::REQUIRED, 'What to create'),
				 new InputArgument('name', InputArgument::REQUIRED, 'The refered name'),
				 new InputOption('info', '-i', InputOption::PARAMETER_OPTIONAL, 'Add extra information about the project')
			 ))
			 ->setHelp(<<<EOT
    You can create a new project by typing "create project name"
    You can create your users typing "create user name" and then, adding the user to any specific group.
    You need to be a super user to perform these actions
EOT
					);
	}

	public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output)
	{
		if(!parent::execute($input, $output))
			return false;
		$this->what= $input->getArgument('what');
		$this->argName= $input->getArgument('name');

		if($this->what == 'user')
		{
			$this->info= $input->getOption('info');
			echo "login: ";
			$this->login= trim(fgets(fopen("php://stdin", "r")));
			echo "pwd: ";
			$this->pwd= Mind::readPassword(true);
			echo "\n";
			while($this->userType!='N' && $this->userType!='A')
			{
				echo "Type (use A for admin, or N for normal): ";
				$this->userType= strtoupper(trim(fgets(fopen("php://stdin", "r"))));
			}
		}
		$this->runAction();
	}

	public function HTTPExecute()
	{
		if(!parent::HTTPExecute())
		{
			return false;
		}
		$this->what= $_REQ['data']['what'];
		$this->argName= $_REQ['data']['name'];
		if(isset($_REQ['data']['info']))
			$this->info= $_REQ['data']['info'];
		if($this->what == 'user')
		{
			if(!isset($_REQ['data']['login'])
				||
			   !isset($_REQ['data']['pwd'])
				||
			   !isset($_REQ['data']['userType']))
			{
				return false;
			}
			$this->login= $_REQ['data']['login'];
			$this->pwd= $_REQ['data']['pwd'];
			$this->userType= $_REQ['data']['userType'];
		}
		$this->runAction();
	}

	private function action()
	{
		switch($this->what)
		{
			case 'project':
					// insert into projects table
					// create a project folder
					$this->projectFileName= urlencode($this->argName);
					$this->projectfile= Mind::$projectsDir.$this->projectFileName;

					if(file_exists($this->projectfile))
					{
						Mind::write('projectAlreadyExists', true, $this->argName);
						return false;
					}
					if(!@mkdir($this->projectfile))
					{
						Mind::message("Couldn create the project", "[Fail]");
						echo "I had no rights to write in the mind3rd/projects directory!\n";
						return false;
					}

					Mind::copyDir(Mind::$modelsDir.'mind/', $this->projectfile);

					$db= new MindDB();
					$qr_newProj= "INSERT into project
										 (
											name,
											info,
											creator
										 )
										 values
										 (
											'".addslashes($this->argName)."',
											'".addslashes($this->info)."',
											'".$_SESSION['pk_user']."'
										 )";
					$db->execute("BEGIN");
					$db->execute($qr_newProj);
					$key= $db->lastInsertedId;
					$qr_userProj= "INSERT into project_user
										 (
											fk_project,
											fk_user
										 )
										 values
										 (
											".$key.",
											".$_SESSION['pk_user']."
										 )";
					$db->execute($qr_userProj);
					$db->execute("COMMIT");

					Mind::write('projectCreated', true, $this->argName);
					
					Mind::openProject(Array('pk_project'=>$key,
											 'name'=>$this->argName));

					echo "\n";
				break;
			case 'user':
					$db= new MindDB();
					$qr_newUser= "INSERT into user
										 (
											name,
											login,
											pwd,
											status,
											type
										 )
										 values
										 (
											'".addslashes($this->argName)."',
											'".$this->login."',
											'".sha1($this->pwd)."',
											'A',
											'".$this->userType."'
										 )";
					$db->execute($qr_newUser);
					Mind::write('userCreated', true, $this->argName);
					echo "\n";
				break;
			default:
				Mind::write('invalidOption', true, $this->what);
				return false;
				break;
		}
	}

	public function runAction()
	{
		$this->action();
	}
}
?>
