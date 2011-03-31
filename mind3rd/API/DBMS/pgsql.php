<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */

/**
 * Description of pgsql
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package DBMS
 */
class pgsql implements DBMS{
	
	public function createDefault()
	{
		return "DEFAULT <defaultvalue>";
	}
	
	public function property()
	{
		return "<property><propertyname></property> <propertytype><propertysize> <propertydetails>";
	}
	
	public function createOptionsCheck()
	{
		return "CHECK (<options>)";
	}
	
	public function notNullDefinition()
	{
		return "NOT NULL";
	}
	
	public function autoIncrementType()
	{
		return "serial";
	}
	
	public function createUnique()
	{
		return "UNIQUE";
	}
	
	public function createFK()
	{
		return "
<command>ALTER </command><object>TABLE</object> <tablename>
  <command>ADD</command> <object>CONSTRAINT</object> <constraintname>
  <object>FOREIGN KEY (<column>)</object> <command>REFERENCES </command>
<referencetablename>(<referencecolumnname>)
";
	}
	
	public function createPrimaryKeys()
	{
		return "
    <object>CONSTRAINT</object> <fkname> <object>PRIMARY KEY</object> (<propertienames>)
";
	}
	
	public function createPK()
	{
		return "
<command>ALTER</command> <object>TABLE</object> <tablename>
  <command>ADD</command> <object>PRIMARY KEY</object> (<propertienames>);
";
	}
	
	public function createTable()
	{
		return "
<command>CREATE </command><object>TABLE</object> <tablename>
(
    <properties>
    <primarykeys>
);
";
	}
	
	public function getModel($command)
	{
		if(method_exists($this, $command))
			return $this->$command();
		return false;
	}
}