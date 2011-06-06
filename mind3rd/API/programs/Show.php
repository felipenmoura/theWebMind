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

	class Show extends MindCommand implements program
	{
		public $what= null;
        public $detailed= false;
        public $extra= '';

		public function __construct()
		{
			$this->setCommandName('show')
				 ->setDescription('Show many different kind of data')
                 ->setAction('action')
				 ->setHelp(<<<EOT
    You can use this command to see lists or details of a sort of components
EOT
					);
            
            $opts= Array(  'projects',
                           'users',
                           'entities',
                           'relations',
                           'version',
                           'idioms',
                           'plugins',
                           'ddl',
                           'info',
                           'lobes');
            asort($opts);
            $this->addRequiredArgument('what',
                                       'What to show',
                                        $opts);
            $this->addOptionalArgument('extra', 'Any extra information to be used');
            $this->addFlag('detailed', '-d', 'Show detailed data(or set scripts as decorated)');
            
            $this->init();
		}

		public function action()
		{
			GLOBAL $_REQ;
			GLOBAL $_MIND;
            
			switch($this->what)
			{
				case 'projects':
						$projs= $this->loadProjectList();
						$projectList= Array();
						foreach($projs as $k=>$proj)
						{
								$projectList[$k]= $proj;
						}
						if($_REQ['env']=='http')
						{
							echo JSON_encode($projectList);
						}else{
								//foreach($projectList as $proj)
								$this->printMatrix($projectList);
							 }
					break;
				case 'users':
						$users= $this->loadUsersList();
						$userList= Array();
						foreach($users as $k=>$user)
						{
								$userList[$k]= $user;
						}
						if($_REQ['env']=='http')
						{
							echo JSON_encode($userList);
						}else{
								$this->printMatrix($userList);
							 }
					break;
				case 'entities':
						$entities= Analyst::getUniverse();
						$entities= $entities['entities'];
						if(sizeof($entities) >0)
							if($this->detailed)
								Analyst::printWhatYouGet(true, true, false);
							else
							echo "  ".implode("\n  ", array_keys($entities));
						else
							echo "  No entities to show";
						echo "\n";
					break;
				case 'relations':
						$relations= Analyst::getUniverse();
						$relations= $relations['relations'];
						if(sizeof($relations) >0)
							if($this->detailed)
								Analyst::printWhatYouGet(true, false, true);
							else
							echo "  ".implode("\n  ", array_keys($relations));
						else
							echo "  No relations to show";
						echo "\n";
					break;
				case 'version':
					
						if(!isset($_SESSION['currentProject']))
						{
							Mind::write('currentProjectRequired');
							Mind::write('currentProjectRequiredTip');
							return false;
						}
								
						if($this->detailed)
						{
							$pF= new DAO\ProjectFactory(Mind::$currentProject,
														$this->extra);
							
							if($this->extra)
								$vsToShow= $this->extra;
							else
								$vsToShow= false;
							echo "Current version: ".$pF->data['version']."\n";
							
							echo "    Name: ".$pF->data['name']."\n";
							echo "    ID: ".$pF->data['pk_project']."\n";
							echo "    Title: ".$pF->data['title']."\n";
							echo "    Idiom: ".$pF->data['idiom']."\n";
							echo "    Technology: ".$pF->data['technology']."\n";
							echo "    DBMS: ".$pF->data['database_drive']."\n";
							echo "ENTITIES:\n";
							
							$entities= $pF->getCurrentEntities($vsToShow);
							
							foreach($entities as $en)
							{
								echo "    ".$en['name'].": ".$en['version']."\n";
								foreach($pF->getProperties($en) as $prop)
								{
									echo "        ".$prop['name']."\n";
								}
							}
						}else{
							echo "Current version: ".Mind::$currentProject['version']."\n";
						}
						break;
                case 'plugins':
                    MindPlugin::listPlugins($_REQ['env']!='http');
                    break;
                case 'idioms':
                    Mind::getIdiomsList();
                    break;
                case 'ddl':
                    $ddls= $this->detailed? \API\Get::DecoratedDDL():
                                            \API\Get::DDL();
                    foreach($ddls as $ddlCommand)
                        echo $ddlCommand;
                    break;
                case 'lobes':
                    echo implode("\n", \Lobe\Neuron::listLobes());
                    echo "\n";
                    break;
                case 'info':
                    print_r(\API\Project::data());
                    break;
				default:
					Mind::write('invalidOption', true, $this->what);
					return false;
					break;
			}
			return $this;
		}
		private function loadProjectList()
		{
			$db= new MindDB();
			if($this->detailed)
				$projs= $db->query('SELECT * from project');
			else
				$projs= $db->query('SELECT name from project');
			return $projs;
		}
		private function loadUsersList()
		{
			return MindUser::listUsers($this->detailed);
		}
		private function printList($list)
		{
			foreach($list as $k=>$item)
			{
				echo $item."\n";
			}
		}
		private function printMatrix($matrix)
		{
			if(sizeof($matrix) == 0)
			{
				echo "none\n";
				return false;
			}
			$ks= array_keys($matrix[0]);
			$validKeys= Array();
			foreach($ks as $item)
			{
				if(is_string($item))
				{
					echo substr(str_pad($item, 10, ' '), 0, 10)."  ";
				}
			}
			echo "\n";
			foreach($matrix as $itemList)
			{
				//echo sizeOf($itemList);

				foreach($itemList as $k=>$item)
				{
					if(is_string($k))
						echo substr(str_pad($item, 10, ' '), 0, 10)."  ";
				}
				echo "\n";
			}
		}
	}