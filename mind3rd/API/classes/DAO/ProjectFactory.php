<?php

/*
 * This file is part of theWebMind Project.
 * It offers features to deal with the project table into the database
 */
namespace DAO;
/**
 * TableFactory: will work with the DAO\Table class
 *
 * @package VCS
 * @subpackage DAO
 * @author felipe
 */
class ProjectFactory extends Project{
	
	public function areDifferent(Array $entity1, \MindEntity $entity2)
	{
		$props= $this->getProperties($entity1);
		
		
		if(sizeof($props) != sizeof($entity2->properties)
			||
		   array_keys($props) != array_keys($entity2->properties))
			return true;
		
		foreach($props as $k=>$prop)
		{
			$refs= "";
			if(!isset($entity2->properties[$prop['name']]))
				return true;
			
			$p= $entity2->properties[$prop['name']];
			
			if($p->refTo)
				$refs= $p->refTo[0]->name.".".$p->refTo[1]->name;
			
			if(
				$prop['name']            != $p->name ||
				$prop['type']            != $p->type ||
				$prop['size']            != $p->size ||
				$prop['options']         != JSON_encode($p->options) ||
				$prop['unique_value']    != $p->unique ||
				$prop['default_value']   != $p->default ||
				$prop['required']        != $p->required ||
				$prop['comment']         != $p->comment ||
				$prop['ref_to_property'] != $refs
			  )
			{
				return true;
			}
			unset($props[$k]);
		}
		
		return false;
	}
	
	public function saveEntities(&$currentEntities)
	{
		$enKey= null;
		$commited= false;
		
		foreach(\Analyst::$entities as &$entity)
		{
			$vs= 1;
			// if it is a new entity
			if(!isset($currentEntities[$entity->name]))
			{
				$commited= true;
				unset($currentEntities[$entity->name]);
				$this->insertEntity($entity, $vs);
			}else{
				// if it is a possible update of an entity
				if($this->areDifferent($currentEntities[$entity->name], $entity))
				{
					$commited= true;
					$this->markAsChanged($currentEntities[$entity->name]);
					$vs= ++$currentEntities[$entity->name]['version'];
					$this->insertEntity($entity,
										$vs);
				}
				unset($currentEntities[$entity->name]);
			}
		}
		
		foreach($currentEntities as $en)
		{
			$commited= true;
			echo "DROPPING ".$en['name']."\n";
			$this->markAsDopped($en);
		}
		
		if($commited)
			echo "VCS: Commited to version ".$this->data['version']."\n";
		else
		{
			$this->data['version']--;
			echo "VCS: Nothing to commit...still in version ".
				  $this->data['version']."\n";
		}
		$this->changed= $commited;
	}
	
	public function getCurrentVersion($vs= false)
	{
		$qr_newProj= "SELECT pk_version,
							 v.version as version,
							 p.creator as creator
						from project p,
							 version v
					   where p.pk_project = v.fk_project
					     and p.pk_project = ".$this->data['pk_project'];
		if($vs)
			$qr_newProj.= " and v.version= ".$vs;
		$qr_newProj.= "
					   ORDER by pk_version desc
					   LIMIT 1";
		$data= $this->db->query($qr_newProj);
		$data= $data[0];
		$this->versionId= $data['pk_version'];
		return $data;
	}
	
	public function commit()
	{
		$this->changed= false;
		$currentEntities= $this->getCurrentEntities();
		$curEn= Array();
		
		foreach($currentEntities as &$en)
		{
			$curEn[$en['name']]= $en;
		}
		$currentEntities= $curEn;
		
		$this->addNewVersion();
		
		$this->saveEntities($currentEntities);
		
		if($this->changed)
			$this->db->execute("COMMIT");
	}
	
	public function __construct(Array $projectData, $vs=false)
	{
		parent::__construct();
		$this->data= $projectData;
		$this->db->execute('BEGIN');
		
		$data= $this->getCurrentVersion($vs);
		$this->data= array_merge($this->data, $data);
	}
}