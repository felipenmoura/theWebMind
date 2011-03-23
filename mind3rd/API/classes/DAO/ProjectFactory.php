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
	private $data= null;
	private $versionId= 0;
	private $tag= '';
	private $description= '';
	private $originalCode= '';
	private $framework= '';
	
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
	
	public function getCurrentEntities()
	{
		$qr= "SELECT *
				from entity
			   where fk_version = ".$this->versionId."
				 and status <> ".\COMMIT_STATUS_DROP;
		
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
					0,
					".$enKey.",
					'".$refs."'
				)";
		$this->db->execute($qr);
	}
	
	public function insertEntity(\MindEntity $entity, $vs=1)
	{
		$qr= "INSERT into entity
				(
					name,
					version,
					fk_version
				)
			  VALUES
				(
					'".$entity->name."',
					".$vs.",
					".$this->versionId."
				)";
		$entities= $this->db->execute($qr);
		$enKey= $this->db->lastInsertedId;

		foreach($entity->properties as &$prop)
		{
			$this->insertProperty($prop, $enKey);
		}
	}
	
	private function getProperties(Array $entity)
	{
		$qr= "select pk_property,
					 property.name,
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
			     and entity.pk_entity='".$entity['pk_entity']."'
				 and entity.status=".\COMMIT_STATUS_OK;
		return $this->db->query($qr);
	}


	public function areDifferent(Array $entity1, \MindEntity $entity2)
	{
		$props= $this->getProperties($entity1);
		if(sizeof($props) != sizeof($entity2->properties))
			return true;
		
		foreach($props as $prop)
		{
			$refs= "";
			if(!isset($entity2->properties[$prop['property.name']]))
				return true;
			
			$p= $entity2->properties[$prop['property.name']];
			
			if($p->refTo)
				$refs= $p->refTo[0]->name.".".$p->refTo[1]->name;
			
			if(
				$prop['property.name']   != $p->name ||
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
		}
		
		return false;
	}
	
	public function saveEntities(&$currentEntities)
	{
		$enKey= null;
		
		foreach(\Analyst::$entities as &$entity)
		{
			$vs= 1;
			// if it is a new entity
			if(!isset($currentEntities[$entity->name]))
			{
				unset($currentEntities[$entity->name]);
				$this->insertEntity($entity, $vs);
			}else{
				// if it is a possible update of an entity
				if($this->areDifferent($currentEntities[$entity->name], $entity))
				{
					// inserir nova tabela com status de alterada,
					// quem alterou, e entao as propriedades dela
					echo "ALTERARAM";
				}
				unset($currentEntities[$entity->name]);
			}
		}
		// se sobraram tabelas em $currentEntities, entrao
		// marque todas como DROP
	}
	
	public function __construct(Array $projectData)
	{
		parent::__construct();
		$this->data= $projectData;
		$this->db->execute('BEGIN');
		$qr_newProj= "SELECT pk_version,
							 v.version as version,
							 p.creator as creator
						from project p,
							 version v
					   where p.pk_project = v.fk_project
					     and p.pk_project = ".$this->data['pk_project']."
					   ORDER by pk_version desc
					   LIMIT 1";
		$data= $this->db->query($qr_newProj);
		$this->data= array_merge($this->data, $data[0]);
		
		$currentEntities= $this->getCurrentEntities();
		$curEn= Array();
		foreach($currentEntities as &$en)
		{
			$curEn[$en['name']]= $en;
		}
		$currentEntities= $curEn;
		$this->saveEntities($currentEntities);
		
		print_r($this->data);
		$this->addNewVersion();
		$this->db->execute("COMMIT");
	}
}