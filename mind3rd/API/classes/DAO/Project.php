<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DAO;
/**
 * Description of Project
 *
 * @author felipe
 */
class Project{
	public $data= null;
	public $versionId= 0;
	public $tag= '';
	public $description= '';
	public $originalCode= '';
	public $framework= '';
	
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
	
	public function __construct()
	{
		$this->db= new \MindDB();
	}
}