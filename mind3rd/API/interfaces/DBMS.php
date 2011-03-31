<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBMS
 *
 * @author felipe
 */
interface DBMS {
	public function createTable();
	public function createPK();
	public function createPrimaryKeys();
}