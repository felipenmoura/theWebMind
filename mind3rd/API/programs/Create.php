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
 * Class responsable to create:
 * Projects
 * User
 *
 * @author felipe
 */
class Create extends MindCommand implements program
{
	public $what= null;
	public $argName= false;
	public $info= "";
	private $userType= null;

	public function __construct()
	{
		$this->setCommandName('create')
			 ->setDescription('Create structures, such as project or user')
			 ->setRestrict(true)
             ->setAction('action')
			 ->setHelp(<<<EOT
    You can create a new project by typing "create project name"
    You can create your users typing "create user name" and then, adding the user to any specific project.
    You need to be a super user to perform these actions
EOT
					);
        
        $this->addRequiredArgument('what', 'What to create');
        $this->addRequiredArgument('argName', 'The refered name');
        $this->addOptionalOption('into', '-i', 'Add extra information about what is being created');
        
        $this->init();
	}

    public function projectExists($projectName)
    {
        $db= new MindDB();
        $data= $db->query("SELECT count(1) as count
                             from project
                            where name='".$projectName."'");
        return $data[0]['count'];
    }
    
	public function action()
	{
		GLOBAL $_MIND;
		switch($this->what)
		{
			case 'project':
					// insert into projects table
					// create a project folder
					$this->projectFileName= urlencode($this->argName);
					$this->projectfile= Mind::$projectsDir.$this->projectFileName;

					if($this->projectExists($this->argName))
					{
						Mind::write('projectAlreadyExists', true, $this->argName);
						return false;
					}
					if(!file_exists($this->projectfile) && !@mkdir($this->projectfile))
					{
						Mind::message("Couldn create the project", "[Fail]");
						echo "I had no rights to write in the mind3rd/projects directory!\n";
						return false;
					}

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
					
					$iniSource= Mind::$projectsDir.$this->argName.'/mind.ini';
					$cP= $_MIND->defaults;
					
					$qr_vsProj= "INSERT into version
										 (
											version,
											tag,
											obs,
											originalcode,
											machine_lang,
											framework,
											database,
											fk_project,
											fk_user
										 )
										 values
										 (
											'0',
											'Project Started',
											'',
											'',
											'".$cP['default_machine_language']."',
											'',
											'".$cP['default_dbms']."',
											".$key.",
											".$_SESSION['pk_user']."
										 )";

					$db->execute($qr_vsProj);

                    if(!file_exists($iniSource))
                    {
                        Mind::copyDir(Mind::$modelsDir.'mind/', $this->projectfile);
                        chmod($this->projectfile, 0777);

                        $ini= file_get_contents($iniSource);
                        $ini= str_replace('<idiom>',
                                          $cP['default_human_language'],
                                          $ini);
                        $ini= str_replace('<technology>',
                                          $cP['default_machine_language'],
                                          $ini);
                        $ini= str_replace('<dbms>',
                                          $cP['default_dbms'],
                                          $ini);
                        file_put_contents(Mind::$projectsDir.
                                                $this->argName.
                                                '/mind.ini',
                                          $ini);
                    }
                    
                    Mind::write('projectCreated', true, $this->argName);
					$db->execute("COMMIT");
                    
					Mind::openProject(Array('pk_project'=>$key,
											 'name'=>$this->argName));
                    \MindLog::log(\MindLog::LOG_TYPE_PROJECT, " OK   - Project '".$this->argName."' create by ".$_SESSION['login']);
				break;
			case 'user':
					$db= new MindDB();
                    $this->prompt('name', "What is the new user's name?");
                    $this->prompt('pwd',  "What will be the password?", true);
                    $this->prompt('type',
                                  "\nWill this user be an administrator?",
                                  Array('Y'=>'Yes',
                                        'N'=>'No'));
                    $this->prompt('email', "What is the user's e-mail?");
                    
                    $email= $this->answers['email'];
                    
					$qr_newUser= "INSERT into user
										 (
											name,
											login,
											pwd,
											status,
											type,
                                            email
										 )
										 values
										 (
											'".$this->answers['name']."',
											'".addslashes($this->argName)."',
											'".sha1($this->answers['pwd'])."',
											'A',
											'".(strtoupper(
                                                    substr($this->answers['type'],
                                                            0,
                                                            1))=='Y'? 'A': 'N')."',
                                            '".$email."'
										 )";
					$db->execute($qr_newUser);
					Mind::write('userCreated', true, $this->argName);
                    \MindLog::log(\MindLog::LOG_TYPE_USER, " OK   - User '".$this->argName."' create by ".$_SESSION['login']);
					echo "\n";
				break;
			default:
				Mind::write('invalidOption', true, $this->what);
				return false;
				break;
		}
	}
}