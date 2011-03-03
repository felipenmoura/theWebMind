<?php
	/**
	 * Represents a relation between two Entities
	 *
	 * @author felipe
	 */
	class MindRelation {

		public  $name;
		private $linkTypes		= Array('possibility', 'must', 'action');
		private $linkType		= 'action';
		private $quantifiers	= Array(0, 1, 'n');
		private $min			= 0;
		private $max			= 'n';
		private $verb			= '';
		private $focus			= null;
		private $rel			= null;
		public  $opposite		= null;
		public  $treated		= false; //to be used by Normalizer

		public function rename($newName)
		{
			Analyst::$relations[$newName]= Analyst::$relations[$this->name];
			Analyst::$relations[$this->name]= false;
			$this->name= (string)$newName;
			return $this;
		}
		
		public function setRel(MindEntity &$rel)
		{
			$this->rel= $rel;
			return $this;
		}
		public function setFocus(MindEntity &$focus)
		{
			$this->focus= $focus;
			return $this;
		}
		
		/**
		 * Return properties from the current relation
		 * These properties were set to private due to set limitations
		 * but they may be open for get operations
		 *
		 * @param String $what
		 * @return Mixed
		 */
		public function __get($what)
		{
			if(isset($this->$what))
				return $this->$what;
		}

		/**
		 * Sets the type of link(possibility, action or must)
		 * @param string $linkType
		 * @return MindRelation
		 */
		public function setLinkType($linkType)
		{
			if(in_array($linkType, $this->linkTypes))
			{
				$this->linkType= $linkType;
				return $this;
			}
			return false;
		}

		/**
		 * Sets the minimun value of the relation(0 or 1)
		 * @param Mixed $min
		 * @return MindRelation
		 */
		public function setMin($min)
		{
			if($min === 0 || $min === 1)
			{
				$this->min= $min;
				return $this;
			}
			throw new Exception("Invalid minimum quantifier: ".$min, 0);
			return false;
		}

		/**
		 * Sets the maximun value for the relation(1 or n)
		 * @param mixed $max
		 * @return MindRelation
		 */
		public function setMax($max)
		{
			if($max == 1 || $max == 'n')
			{
				$this->max= $max;
				return $this;
			}
			throw new Exception("Invalid maximum quantifier: ".$max, 0);
			return false;
		}

		/**
		 * Defines which verb was used to define the current instruction
		 *
		 * @param string $verb
		 * @return MindRelation
		 */
		public function setUsedVerb($verb)
		{
			$this->verb= (string)$verb;
			return $this;
		}

		/**
		 * Specifies which entities are envolved in this relation
		 *
		 * @param MindEntity $focus
		 * @param MindEntity $rel
		 * @return MindRelation
		 */
		public function setEntities(MindEntity &$focus, MindEntity &$rel)
		{
			$this->focus= &$focus;
			$this->rel= &$rel;
			return $this;
		}

		/**
		 * The constructor, receiving the name of the relation
		 * @param string $relName
		 */
		public function MindRelation($relName)
		{
			$this->name= (string)$relName;
		}
	}