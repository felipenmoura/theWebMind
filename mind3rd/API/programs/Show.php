<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

	class Show extends MindCommand implements program
	{
		private $whatToShow= null;

		public function configure()
		{
			$this->setName('show')
				 ->setDescription('Show many different kind of data')
				 ->setDefinition(array(
						new InputArgument('what', InputArgument::REQUIRED, 'What to show'),
						new InputOption('detailed', '-d', InputOption::PARAMETER_NONE, 'Show detailed data'),
						new InputArgument('extra', InputArgument::OPTIONAL, 'Extra information to be used'),
					))
				 ->setHelp(<<<EOT
    You can use this command to see lists or details of a sort of components
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
		{
			if(!parent::execute($input, $output))
				return false;
			$this->whatToShow= $input->getArgument('what');
			//echo $input->getOption('detailed')."\n";
			$this->detailed= $input->getOption('detailed');
			$this->extra= $input->getArgument('extra');
			$this->runAction();
		}

		public function HTTPExecute()
		{
			if(!parent::HTTPExecute())
				return false;
			GLOBAL $_REQ;
			$this->whatToShow= $_REQ['data']['what'];
			if(isset($_REQ['data']['detailed']) && $_REQ['data']['detailed'])
				$this->detailed= 1;
			else
				$this->detailed= false;
			if(isset($_REQ['data']['extra']))
				$this->extra= $_REQ['data']['extra'];
			else
				$this->extra= false;
			$this->runAction();
		}

		private function action()
		{
			GLOBAL $_REQ;
			GLOBAL $_MIND;
			switch($this->whatToShow)
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
								//foreach($projectList as $proj)
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
				default:
					Mind::write('invalidOption', true, $this->whatToShow);
					return false;
					break;
			}
			return $this;
		}

		public function runAction()
		{
			$ret= $this->action();
			parent::runAction();
			return $ret;
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

			$db= new MindDB();
			if($this->detailed)
				$projs= $db->query('SELECT * from user');
			else
				$projs= $db->query('SELECT login from user');
			return $projs;
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
