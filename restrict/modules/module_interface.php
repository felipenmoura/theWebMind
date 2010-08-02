<?php
	interface module_interface
	{
		public function getStructure();
		public function onStart();
		public function onFinish();
		public function applyCRUD($entity);
		public function callExtra();
	}
?>