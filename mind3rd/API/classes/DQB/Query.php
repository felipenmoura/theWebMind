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

namespace DQB;
/**
 * Description of Query
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package DQB
 */
class Query {
	
	const TABLE_NAME     = '/<tablename(\/)?>/';
	const PROPS          = '/<properties(\/)?>/';
	const PROPS_NAME     = '/<propertienames(\/)?>/';
	const PROP_NAME      = '/<propertyname(\/)?>/';
	const PROP_TYPE      = '/<propertytype(\/)?>/';
	const PROP_SIZE      = '/<propertysize(\/)?>/';
	const PROP_DETAILS   = '/<propertydetails(\/)?>/';
	const PROP_DEFAULT   = '/<defaultvalue(\/)?>/';
	const PROP_OPTIONS   = '/<options(\/)?>/';
	const PRIMARY_KEYS   = '/<primarykeys(\/)?>/';
	const CONSTRAINT_NAME= '/<constraintname(\/)?>/';
	const REF_TAB_NAME   = '/<referencetablename(\/)?>/';
	const REF_COL_NAME   = '/<referencecolumnname(\/)?>/';
	const REFERENCES   = '/<references(\/)?>/';
	const FK_NAME= '/<fkname(\/)?>/';
	public $query= "";
	public $closingQuery= Array();
	private $pks= Array(); // temporary variable
	private $fks= Array(); // temporary variable
	
	public function setUp()
	{
		$this->pks= false;
		$this->pks= Array();
		$this->fks= false;
		$this->fks= Array();
		$this->closingQuery= false;
		$this->closingQuery= Array();
	}
	
	private function parseDetails(Array $prop, $table)
	{
		$details= Array();
		
		// parsing te default value
		if(!empty($prop['default_value']))
		{
			if(\AUTOINCREMENT_DEFVAL == $prop['default_value'])
			{
				$details[]= QueryFactory::getQueryString('createAutoIncrement');
				$prop['default_value']= false;
			}else{
					$default= QueryFactory::getQueryString('createDefault');
					$def= \addslashes($prop['default_value']);
					$def= preg_replace(\BETWEEN_QUOTES, "'", trim($def));
					$details[]= preg_replace(self::PROP_DEFAULT,
											 $def,
											 $default);
				 }
		}
		
		// checking the not null attribute
		if($prop['required'])
			$details[]= QueryFactory::getQueryString('notNullDefinition');
		
		if($prop['ref_to_property'] && QueryFactory::$mustSort)
		{
			$tb= explode('.', $prop['ref_to_property']);
			$col= $tb[1];
			$tb= $tb[0];
			$reference= QueryFactory::getQueryString('createReferences');
			$reference= preg_replace(self::REF_TAB_NAME, $tb, $reference);
			$reference= preg_replace(self::REF_COL_NAME, $col, $reference);
			$details[]= $reference;
		}
			
		// 
		if($prop['options'])
			$prop['options']= JSON_decode($prop['options']);
		if(sizeof($prop['options'])>0)
		{
			$optionsTplt= QueryFactory::getQueryString('createOptionsCheck');
			$options= Array();
			foreach($prop['options'] as $opt)
			{
				$opt= \is_string($opt[0])? "'".$opt[0]."'": $opt[0];
				$options[]= $opt;
			}
			$details[]= preg_replace(self::PROP_OPTIONS,
									 $prop['name']."=".
												   implode(" OR ".$prop['name']
																 ."=", 
														   $options),
									 $optionsTplt);
		}
		
		if($prop['unique_value'])
		{
			$details[]= QueryFactory::getQueryString('createUnique');
		}
		if($prop['ref_to_property'])
		{
			$this->fks[]= $prop;
		}
		return implode(' ', $details);
	}
	
	private function createProperties(&$query, $table)
	{
		$template= QueryFactory::getQueryString('property');
		$tmpQuery= "";
		foreach($table['properties'] as $prop)
		{
			if($prop['is_pk'])
				$this->pks[]= $prop['name'];
			
			$propQuery= "";
			$propQuery= preg_replace(self::PROP_NAME, $prop['name'], $template);
			
			if($prop['default_value'] == \AUTOINCREMENT_DEFVAL)
			{
				$prop['type']= QueryFactory::getQueryString('autoIncrementType');
			}
			
			if($prop['size'])
				$propQuery= preg_replace(self::PROP_SIZE, "(<value>".$prop['size']."</value>)", $propQuery);
			else
				$propQuery= preg_replace(self::PROP_SIZE, "", $propQuery);
			$propQuery= preg_replace(self::PROP_NAME, $prop['name'], $propQuery);
			
			$propQuery= preg_replace(self::PROP_DETAILS,
									 $this->parseDetails($prop, $table),
									 $propQuery);
			
			$propQuery= preg_replace(self::PROP_TYPE, $prop['type'], $propQuery);
			$propQuery= preg_replace(self::REFERENCES, '', $propQuery);
			
			$tmpQuery.= trim($propQuery).",\n    ";
		}
		
		$query= preg_replace(self::PROPS, trim($tmpQuery), $query);
	}

	public function createPrimaryKeys(&$query, $table)
	{
		$tmplt= QueryFactory::getQueryString('createPrimaryKeys');
		//print_r($this->pks);
		$tmpQuery= preg_replace(self::PROPS_NAME,
								implode(', ', $this->pks),
								$tmplt);
		$tmpQuery= preg_replace(self::FK_NAME,
								$table['name']."_".implode('_', $this->pks),
								$tmpQuery);
		$query= preg_replace(self::PRIMARY_KEYS, trim($tmpQuery), $query);
	}
	
	private function createForeignKeys(&$query, $table)
	{
		$tmplt= QueryFactory::getQueryString('createFk');
		foreach($this->fks as $fk)
		{
			$constraintName= str_replace('.', '_', $fk['ref_to_property']);
			$constraintName= "fk_".$table['name'].'_'.$constraintName;
			$tb= explode('.', $fk['ref_to_property']);
			$col= $tb[1];
			$tb= $tb[0];
			$tmpQuery= preg_replace(self::TABLE_NAME, $table['name'], $tmplt);
			$tmpQuery= preg_replace(self::CONSTRAINT_NAME, $constraintName, $tmpQuery);
			$tmpQuery= preg_replace(self::PROP_NAME, $fk['name'], $tmpQuery);
			$tmpQuery= preg_replace(self::REF_TAB_NAME, $tb, $tmpQuery);
			$tmpQuery= preg_replace(self::REF_COL_NAME, $col, $tmpQuery);
			
			$this->closingQuery[]= $tmpQuery;
		}
	}


	public function __construct($command, Array $table, $template)
	{
		$query= '';
		$this->setUp();
				
		switch($command)
		{
			case 'createTable':
				$query= preg_replace(self::TABLE_NAME,
									 $table['name'],
									 $template);

				//if(preg_match(self::PROPS, $template))
				self::createProperties($query, $table);
				self::createPrimaryKeys($query, $table);
				self::createForeignKeys($query, $table);
				break;
		}
		
		$this->query= $query;
		$this->table= $table;
		return $query;
	}
	
	public function __toString()
	{
		return $this->query;
	}
	
}