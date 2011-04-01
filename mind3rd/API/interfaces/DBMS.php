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
	public function createReferences();
	public function property();
	public function createOptionsCheck();
	public function notNullDefinition();
	public function autoIncrementType();
	public function createUnique();
	public function getHeader();
	public function createFK();
	public function createPrimaryKeys();
	public function createPK();
	public function createAutoIncrement();
	public function mustSort();
	public function getModel($keyword);
}