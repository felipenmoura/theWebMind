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
    class Decision {
        
        public $context     = false;
        public $doubtMessage= "";
        
        public function __construct($doubtMessage, $context)
        {
            
        }
    }