<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	namespace scientia;
	/**
	 *
	 * @author felipe
     * @package scientia
	 */
	class Darwin {
        
        protected $tips      = Array();
        protected $decisions = Array();
        protected $doubts    = Array();

        public static function getTips()
        {
            return self::$tips;
        }
        public static function getDecisions()
        {
            return self::$decisions;
        }
        public static function getDoubts()
        {
            return self::$doubts;
        }

        public static function addTip($tipMessage, $context=false)
        {
            self::$tips[]= new Tip($tipMessage, $context);
        }
        
        public static function addDecision($decisionMessage, $context=false)
        {
            self::$decisions[]= new Decision($decisionMessage, $context);
        }
        
		public static function addDoubt($doubtMessage, $context)
		{
            self::$doubts[]= new Doubt($doubtMessage, $context);
		}
	}