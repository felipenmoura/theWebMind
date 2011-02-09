<?php
	/**
	 * Represents a relation between two Entities
	 *
	 * @author felipe
	 */
	class MindRelation {

		public $name;
		private $linkTypes		= Array('possibility', 'must', 'action');
		private $linkType		= 'action';
		private $quantifiers	= Array(0, 1, 'n');
		private $min			= 0;
		private $max			= 'n';
		private $verb			= '';
		private $focus			= null;
		private $rel			= null;

		public function setLinkType($linkType)
		{
			if(in_array($linkType, $this->linkTypes))
			{
				$this->linkType= $linkType;
				return $this;
			}
			return false;
		}

		public function setMin($min)
		{
			if(in_array($min, $this->quantifiers))
			{
				$this->min= $min;
				return $this;
			}
			return false;
		}
		
		public function setMax($max)
		{
			if(in_array($max, $this->quantifiers))
			{
				$this->max= $max;
				return $this;
			}
			return false;
		}
		
		public function setUsedVerb($verb)
		{
			$this->verb= (string)$verb;
			return $this;
		}

		public function setEntities(MindEntity &$focus, MindEntity &$rel)
		{
			$this->focus= &$focus;
			$this->rel= &$rel;
			return $this;
		}

		public function MindRelation($relName)
		{
			$this->name= (string)$relName;
		}
	}