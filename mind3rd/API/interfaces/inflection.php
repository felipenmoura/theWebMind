<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * Interface to be implemented by Inflect classes, for each language.
 * @interface
 */
interface inflection {
    
    /**
     * Verifies if a word in in its singular form
     */
	public static function isSingular($string);
    /**
     * Turns a word into its plural form
     */
	public static function toPlural($string);
    /**
     * Turns a word into its singular form
     */
	public static function toSingular($string);
}