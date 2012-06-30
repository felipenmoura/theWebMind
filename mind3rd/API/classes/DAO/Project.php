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
namespace DAO;
/**
 * Deals with the SQLite to retrieve or interact with project's information.
 * It deals a lot with version control of the current project.
 *
 * @package VCS
 * @subpackage DAO
 * @author felipe
 */
class Project{
	public $data= null;
	public $versionId= 0;
	public $tag= '';
	public $description= '';
	public $originalCode= '';
	public $framework= '';
	
	/**
	 * Gets the list of entities in the current version of the analyzed project.
	 * It returns an array with all the entity's name, id and version.
	 * 
	 * @param integer $vs
	 * @return Array An array with all the entities for the project in the curret
	 * or passed(if passed) version.
	 */
	public function getCurrentEntities($vs=false, $name=false, $pk=false)
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
		if($name)
			$qr.= " and entity.name = '".$name."'";
		if($pk)
			$qr.= " and pk_entity = ".$pk."";
		
		$entities= $this->db->query($qr);
		return $entities;
	}
	
	/**
	 * Inserts the passed property into the SQLite database.
	 * It inserts the passed property as a property of the table with the
	 * id equals to the passed $enKey.
	 * 
	 * @param \MindProperty $prop
	 * @param integer $enKey
	 * @return boolean True if everything went ok, an error otherwise
	 */
	public function insertProperty(\MindProperty $prop, $enKey)
	{
		$refs= "";
		if($prop->refTo)
		{
			$refs= $prop->refTo[0]->name.".".$prop->refTo[1]->name;
		}
		$qr= "INSERT into property
				(
					name,
					type,
					size,
					options,
					is_pk,
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
					'".(($prop->key)? 1: 0)."',
					'".$prop->default."',
					'".$prop->unique."',
					'".$prop->required."',
					'".$prop->comment."',
					".\COMMIT_STATUS_OK.",
					".$enKey.",
					'".$refs."'
				)";
		return $this->db->execute($qr);
	}

	/**
	 * Get all the properties of the passed entity.
	 * It returns an associative array(with the property name in each index) of
	 * all the properties the passed entity has.
	 * 
	 * @param array $entity
	 * @return Array 
	 */
	public function getProperties(Array $entity)
	{
		$qr= "select pk_property,
					 property.name as name,
					 type,
					 size,
					 options,
					 is_pk,
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
	
	/**
	 * Marks an entity as changed from the last version.
	 * It updates the entity's status to changed, in the previous version, 
	 * the new version will have it with the OK flag.
	 * 
	 * @param array $en
	 * @return boolean True in case of success, otherwise, generates an error
	 */
	public function markAsChanged(Array $en)
	{
		$qr= "UPDATE entity
			     SET status= ".\COMMIT_STATUS_CHANGED."
			   WHERE pk_entity= ".$en['pk_entity'];
		return $this->db->execute($qr);
	}
	
    public function addUser($user){
        if(!\Mind::hasProject($this->data['name'], $user['pk_user'])){
            $qr_userProj= "INSERT into project_user
                            (
                                fk_project,
                                fk_user
                            )
                            values
                            (
                                ".$this->data['pk_project'].",
                                ".$user['pk_user']."
                            )";
            return $this->db->execute($qr_userProj);
        }else{
            return true;
            //echo "JA TINHA\n";
        }
        
        //var_dump($this->data['pk_project']);
    }
    
	/**
	 * Marks an entity as dropped from the last version.
	 * The next version has not the passed entity, so, it will be marked
	 * as dropped, in the previous version.
	 * 
	 * @param array $en
	 * @return boolean True in case of success, otherwise, generates an error
	 */
	public function markAsDropped(Array $en)
	{
		$qr= "UPDATE entity
			     SET status= ".\COMMIT_STATUS_DROP."
			   WHERE pk_entity= ".$en['pk_entity'];
		return $this->db->execute($qr);
	}
	
	/**
	 * Inserts a new version into the SQLite database.
	 * It also starts the versonId variable to the next version.
	 * 
	 * @return boolean True in case of success, otherwise, generates an error
	 */
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
	
	/**
	 * Inserts an entity into the SQLite database.
	 * Adds an entity into the database, marking it with the passed status
	 * and version.
	 * 
	 * @param \MindEntity $entity
	 * @param integer $vs
	 * @param integer $status
	 * @return integer the inserted entity id(the primary key, itself)
	 */
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
	
	/**
	 * The constructor.
	 */
	public function __construct()
	{
		$this->db= new \MindDB();
	}
}