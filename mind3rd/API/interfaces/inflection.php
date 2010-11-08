<?php

/**
 *
 * @author felipe
 */
interface inflection {
    public static function isFemale($string);
	public static function isSingular($string);
	public static function toPlural($string);
	public static function toSingular($string);
	public static function toFemale($string);
	public static function toMale($string);
}
?>
