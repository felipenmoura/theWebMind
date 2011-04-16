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
