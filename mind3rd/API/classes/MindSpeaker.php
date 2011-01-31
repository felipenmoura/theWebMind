<?php
class MindSpeaker
{
    /**
	 * This method returns or outputs messages using the L10N library
	 * You can pass a rich string with %s, %i, etc, sending extra parameters
	 * If the boolean flag $echo is sent, it prints it to the output, otherwise,
	 * only returns it
	 *
	 * @param String $k
	 * @param Bolean $echo
	 * @param mixed... extra parameter to be treated in the string
	 * @return String
	 */
	public static function write($k, $echo=true, $args=false)
	{
		$msg= Mind::$l10n->getMessage($k);
		if(!$msg)
		{
			$msg= Mind::message("L10N: Message $k does not exist", '[Fail]', false);
		}
		if(!is_array($args))
			$args= func_get_args();
		$parms= "";
		if(sizeof($args)>2)
		{
			for($i=2; $i<sizeof($args); $i++)
			{
				$parms.= ', "'.$args[$i].'"';
			}
			$parms= '"'.$msg.'"'.$parms;
			eval("\$print= sprintf(".$parms.");");
		}else{
				$print= $msg;
			 }
		$count= 1;
		while(strlen($print) >= _CONSOLE_LINE_LENGTH_ && strpos($print, '..')>-1)
		{
			$print= preg_replace("/\.\./", '.', $print, $count);
		}
		if($echo)
			echo $print;
		return $msg;
	}

	/**
	 * This method returns or prints a message formated to represent failures, passes
	 * or any kind of alert
	 *
	 * @param String $message The message itself
	 * @param String $status The status to be shown in the end of the message
	 * @param Boolean $echo if it should be printed or not
	 * @return string
	 */
	public static function message($message, $status, $echo=true)
	{
		$msg= str_pad($message, _CONSOLE_LINE_LENGTH_ - strlen($status), '.').$status."\n";
		if($echo)
			echo $msg;
		return $msg;
	}
}