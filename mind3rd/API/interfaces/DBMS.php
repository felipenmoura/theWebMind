<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBMS
 * Possible tags for markup:
 *   object
 *   element
 *   value
 *   property
 *   keyword
 *   comment
 *
 * @author felipe
 */
interface DBMS {
	public function createTable();
	public function createPK();
	public function createPrimaryKeys();
}