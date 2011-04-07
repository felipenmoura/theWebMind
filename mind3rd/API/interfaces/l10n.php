<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */

/**
 * Interface for l10n
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
interface l10n {
	public function getMessage($msg);
	public function __construct();
}