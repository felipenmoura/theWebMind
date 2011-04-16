<?php
	/*
	 * This is an example of plugin you may create
	 * This plugin simply runs when the program auth is
	 * called, and shows a message
	 *
	 * Notice that plugins set to run AFTER a specific program
	 * will only execute if the program runs plugins on after
	 * event
	 */
	class PluginOne extends MindPlugin implements plugin
	{
        /**
         * These are the properties you will have to set
         */
		public $name= "Plugin One";
		public $version= "0.1";
		public $description = "This is a demo plugin, disabled by default";
		public $links= Array();

		// change this flag to true and execute the test program
		// to see this plugin running
		public $active= true;
        
		public function run()
		{
			echo "EXECUTING THE PLUGIN ONE!!!\n";
		}

		public function __construct()
		{
			$this->setTrigger('info');
			$this->setEvent('after');
		}
	}