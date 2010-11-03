<?php
	/**
	 * Description of MindPlugin
	 *
	 * @author felipe
	 */
	class MindPlugin
	{
		public $trigger= null;
		public $event= 'after';

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
	}
