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
	 * This class represents a model for programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Set extends MindCommand implements program
	{
        public $whose    = '';
        public $attribute= '';
        public $value    = '';
        public $extra    = '';
        
        private function setUserData()
        {
            if($this->attribute=='pwd')
                $this->value= $this->prompt('pwd', 'The new password, please', true);
            if($this->extra && $this->value=='as')
            {
                
            }else
                \API\User::set($this->attribute, $this->value);
        }
        
        private function setProjectData()
        {
            echo "Project's data\n";
        }
        
        public function executableFunction()
        {
            $property= explode('.', $this->property);
            if(sizeof($property) <= 1){
                //\MindSpeaker::write('wrongParam', $this->property);
                \MindSpeaker::write('wrongParam', true, "property", $this->property);
                return false;
            }
            $entity= $property[0];
            $property= $property[1];
            if($entity == 'user'){
                if($property == 'pwd' && !$this->extra){
                    $this->prompt('pwd',  "What will be the password?", true);
                    if($this->value)
                        $this->extra= $this->value;
                    $this->value= $this->answers['pwd'];
                }
                if($this->extra){
                    \MindUser::set($property, $this->value, $this->extra);
                }else{
                    \MindUser::set($property, $this->value);
                }
            }else{
                \MindUser::set($property, $this->value);
            }
            /*if($this->whose == 'user')
            {
                $this->setUserData();
            }else{
                $this->setProjectData();
            }*/
        }
        
        public function __construct()
        {
            
            $this->setCommandName('set')
                 ->setDescription("Sets user's or project's data ")
                 ->setRestrict(true)
                 ->setHelp("You can set information about the current user or a project's data.
    if you are the admin, you can use the following line:
        set user :username: as :status:
    or
        set user :username: as :type:
    Where status may be 'A' or 'I', and type may be 'A' or 'N'")
                 ->setAction('executableFunction');
            
            $this->addRequiredArgument('property',
                                       'Who will suffer the update. Use entity.property(eg. user.pwd'/*,
                                       Array('user', 'project')*/);
            /*$this->addRequiredArgument('attribute',
                                       'The attribute you will change');*/
            $this->addOptionalArgument('value',
                                       'The value for that attribute(optional for pwd)');
            $this->addOptionalArgument('extra',
                                       'An extra data about the defined attribute(eg. if admin, the user to be changed)');

            $this->init();
        }
	}
