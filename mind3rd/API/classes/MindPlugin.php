<?php
	/**
	 * Description of MindPlugin
	 *
	 * @author felipe
	 */
	abstract class MindPlugin
	{
		public $trigger= null;
		public $event= 'after';
		public $name;
		public $version;
		public $description;
		public $links= Array();

        public function listPlugins($echoes)
        {
            if($echoes)
            {
                $col1= 34;
                $col2= 25;
                $col34= 8;
                $header = "|".str_pad("Plugin", $col1, " ", STR_PAD_BOTH);
                $header.= "|".str_pad("Trigger", $col2, " ", STR_PAD_BOTH);
                $header.= "|".str_pad("Event", $col34, " ", STR_PAD_BOTH);
                $header.= "|".str_pad("Active", $col34, " ", STR_PAD_BOTH)."|\n";
                $line= "+".str_pad("", 78, '-')."+";
                $echoes= $line;
                $echoes.=$header;
                $echoes.=$line;
                foreach(Mind::$pluginList as $program)
                {
                    foreach($program as $event)
                    {
                        foreach($event as $plugin)
                        {
                            $echoes.="|".str_pad($plugin->name, $col1, ' ', STR_PAD_RIGHT);
                            $echoes.="|".str_pad($plugin->trigger, $col2, ' ', STR_PAD_RIGHT);
                            $echoes.="|".str_pad($plugin->event, $col34, ' ', STR_PAD_RIGHT);
                            $echoes.="|".str_pad(($plugin->active? 'Y': 'N'), $col34, ' ', STR_PAD_BOTH)."|\n";
                        }
                    }
                }
                $echoes.=$line;
                echo $echoes;
            }
            return Mind::$pluginList;
        }
        
		public function setTrigger($trg)
		{
			$this->trigger= $trg;
			return $this;
		}
		public function setEvent($evt)
		{
			$this->event= $evt=='before'? 'before':'after';
			return $this;
		}

		/**
		 * Adds a MindPlugin based object to the
		 * plugins and triggers list
		 *
		 * @param MindPlugin $plugin
		 */
		static function addPlugin(&$plugin)
		{
            //echo $plugin->name." - ".$plugin->trigger."\n\n";
            
            //print_r(Mind::$triggers);
			//if(in_array($plugin->trigger, Mind::$triggers))
			//{
				if(!isset(Mind::$pluginList[$plugin->trigger]))
					Mind::$pluginList[$plugin->trigger]= Array( 'before'=>Array(),
																'after'=>Array());
				Mind::$pluginList[$plugin->trigger][$plugin->event][]= $plugin;
			//}
            //print_r(Mind::$pluginList);
		}
	}
