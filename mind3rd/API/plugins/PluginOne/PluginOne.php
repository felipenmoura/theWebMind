<?php
	/*
	 * This is an example of plugin you may create
	 * This plugin simply runs when the program auth is
	 * called, and shows a message
	 */
	class PluginOne extends MindPlugin implements plugin
	{
		public function run()
		{
			echo "EXECUTING THE PLUGIN ONE!!!\n";
		}

		public function  __construct() {
			$this->setTrigger('test');
			$this->setEvent('after');
		}
	}