<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	/**
	 * The abstract class with generic methods Plugins may use.
     * Also, offers methods to the application itself, allowing it to deal with plugins.
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

        /**
         * Gets a list of all installed plugins.
         * 
         * @param boolean $echoes If the list should or not be sent to the output.
         * @return Array The list of plugins
         */
        public static function listPlugins($echoes=true)
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
                $line= "+".str_pad("", 78, '-')."+\n";
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
        
        /**
         * Informs which program/command will trigger the current plugin.
         * 
         * @param string $trg The name of the command which will trigger the plugin
         * @return MindPlugin 
         */
		public function setTrigger($trg)
		{
			$this->trigger= $trg;
			return $this;
		}
        /**
         * Sets WHEN the plugin will be run.
         * 
         * @param string $evt 'before' or 'after' the trigger command be executed.
         * @return MindPlugin 
         */
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
            if(!isset(Mind::$pluginList[$plugin->trigger]))
                Mind::$pluginList[$plugin->trigger]= Array( 'before'=>Array(),
                                                            'after'=>Array());
            Mind::$pluginList[$plugin->trigger][$plugin->event][]= $plugin;
		}
	}
