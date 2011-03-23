<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DAO;
/**
 * Description of TableFactory
 *
 * @author felipe
 */
class ProjectFactory extends Project{
	public $data= null;
	public $versionId= 0;
	public $tag= '';
	public $description= '';
	public $originalCode= '';
	public $framework= '';
	
	public function addNewVersion()
	{
		$this->data['version']++;
		
		$qr_vsProj= "INSERT into version
							 (
								version,
								tag,
								obs,
								originalcode,
								machine_lang,
								framework,
								database,
								fk_project,
								fk_user
							 )
							 values
							 (
								'".$this->data['version']."',
								'".$this->tag."',
								'".$this->description."',
								'".$this->originalCode."',
								'".$this->data['technology']."',
								'".$this->framework."',
								'".$this->data['database_drive']."',
								".$this->data['pk_project'].",
								".$_SESSION['pk_user']."
							 )";
		if($this->db->execute($qr_vsProj))
		{
			$this->versionId= $this->db->lastInsertedId;
			return true;
		}
		return false;
	}
	
	public function getCurrentEntities($vs=false)
	{
		$qr= "SELECT entity.name as name,
					 pk_entity,
					 entity.version as version
				from entity,
					 version
			   where fk_project = ".\Mind::$currentProject['pk_project']."
				 and fk_version= pk_version
				 and status = ".\COMMIT_STATUS_OK;
		if($vs)
			$qr.= " and fk_version = ".$vs."";
		
		$entities= $this->db->query($qr);
		return $entities;
	}
	
	public function insertProperty(\MindProperty $prop, $enKey)
	{
		$refs= "";
		if($prop->refTo)
			$refs= $prop->refTo[0]->name.".".$prop->refTo[1]->name;
		$qr= "INSERT into property
				(
					name,
					type,
					size,
					options,
					default_value,
					unique_value,
					required,
					comment,
					status,
					fk_entity,
					ref_to_property
				)
			  VALUES
				(
					'".$prop->name."',
					'".$prop->type."',
					'".$prop->size."',
					'".JSON_encode($prop->options)."',
					'".$prop->default."',
					'".$prop->unique."',
					'".$prop->required."',
					'".$prop->comment."',
					".\COMMIT_STATUS_OK.",
					".$enKey.",
					'".$refs."'
				)";
		$this->db->execute($qr);
	}
	
	public function insertEntity(\MindEntity $entity,
								 $vs=1,
								 $status=\COMMIT_STATUS_OK)
	{
		$qr= "INSERT into entity
				(
					name,
					version,
					status,
					fk_version
				)
			  VALUES
				(
					'".$entity->name."',
					".$vs.",
					".$status.",
					".$this->versionId."
				)";
		$entities= $this->db->execute($qr);
		$enKey= $this->db->lastInsertedId;

		foreach($entity->properties as &$prop)
		{
			$this->insertProperty($prop, $enKey);
		}
		return $enKey;
	}
	
	public function getProperties(Array $entity)
	{
		$qr= "select pk_property,
					 property.name as name,
					 type,
					 size,
					 options,
					 default_value,
					 unique_value,
					 required,
					 comment,
					 ref_to_property
			    from property,
					 entity
			   where pk_entity = fk_entity
			     and entity.pk_entity='".$entity['pk_entity']."'";
		$props= $this->db->query($qr);
		$ret= Array();
		foreach($props as $prop)
		{
			$ret[$prop['name']]= $prop;
		}
		return $ret;
	}


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
	
	public function markAsChanged(Array $en)
	{
		$qr= "UPDATE entity
			     SET status= ".\COMMIT_STATUS_CHANGED."
			   WHERE pk_entity= ".$en['pk_entity'];
		return $this->db->execute($qr);
	}
	
	public function markAsDopped(Array $en)
	{
		$qr= "UPDATE entity
			     SET status= ".\COMMIT_STATUS_DROP."
			   WHERE pk_entity= ".$en['pk_entity'];
		return $this->db->execute($qr);
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
			echo "CVS: Commited to version ".$this->data['version']."\n";
		else
		{
			$this->data['version']--;
			echo "CVS: Nothing to commit...still in version ".
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