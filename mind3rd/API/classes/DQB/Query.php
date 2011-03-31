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
	
	const TABLE_NAME  = '/<tablename(\/)?>/';
	const PROPS       = '/<properties(\/)?>/';
	const PROPS_NAME  = '/<propertienames(\/)?>/';
	const PROP_NAME   = '/<propertyname(\/)?>/';
	const PROP_TYPE   = '/<propertytype(\/)?>/';
	const PROP_SIZE   = '/<propertysize(\/)?>/';
	const PROP_DETAILS= '/<propertydetails(\/)?>/';
	const PROP_DEFAULT= '/<defaultvalue(\/)?>/';
	const PROP_OPTIONS= '/<options(\/)?>/';
	const PRIMARY_KEYS= '/<primarykeys(\/)?>/';
	const FK_NAME= '/<fkname(\/)?>/';
	public $query= "";
	private $pks= Array(); // temporary variable
	private $fks= Array(); // temporary variable
	
	public function setUp()
	{
		$this->pks= false;
		$this->pks= Array();
		$this->fks= false;
		$this->fks= Array();
	}
	
	private function parseDetails(Array $prop, $table)
	{
		$details= Array();
		
		// parsing te default value
		if(!empty($prop['default_value']))
		{
			$default= QueryFactory::getQueryString('createDefault');
				
			$details[]= preg_replace(self::PROP_DEFAULT,
									 $prop['default_value'],
									 $default);
		}
		
		// checking the not null attribute
		if($prop['required'])
			$details[]= QueryFactory::getQueryString('notNullDefinition');
		
		// 
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
		//print_r($prop);
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
				$prop['default_value']= false;
			}
			
			if($prop['size'])
				$propQuery= preg_replace(self::PROP_SIZE, "(".$prop['size'].")", $propQuery);
			else
				$propQuery= preg_replace(self::PROP_SIZE, "", $propQuery);
			$propQuery= preg_replace(self::PROP_NAME, $prop['name'], $propQuery);
			$propQuery= preg_replace(self::PROP_DETAILS,
									 $this->parseDetails($prop, $table),
									 $propQuery);
			
			$propQuery= preg_replace(self::PROP_TYPE, $prop['type'], $propQuery);
			
			$tmpQuery.= $propQuery."\n    ";
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
	
	public function __construct($command, Array $table, $template)
	{
		$query= '';
		$this->setUp();
				
		switch($command)
		{
			case 'createTable':
				$query= preg_replace(self::TABLE_NAME, $table['name'], $template);

				//if(preg_match(self::PROPS, $template))
				self::createProperties($query, $table);
				self::createPrimaryKeys($query, $table);
				break;
		}
		
		$this->query= $query;
		return $query;
	}
	
	public function __toString()
	{
		return htmlentities($this->query);
	}
	
}