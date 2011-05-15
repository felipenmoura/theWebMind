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
 * ProjectFactory: will work with the DAO\Project class.
 * This class will deal with the SQLite database to check for Project's
 * changes.
 *
 * @package VCS
 * @subpackage DAO
 * @author felipe
 */
class ProjectFactory extends Project{
	
	/**
	 * Returns if the entity from the SQLite is the same as the entity in the Memory
	 * 
	 * @param array $entity1
	 * @param \MindEntity $entity2
	 * @return boolean 
	 */
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
	
	/**
	 * Saves the current entities
	 * 
	 * It will persist the current entities into the SQLite database.
	 * This will also verifies the changes in the previous version and
	 * make the necessary changes.
	 * 
	 * @param Array $currentEntities 
	 */
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
			$this->markAsDopped($en);
		}
		
		if($commited)
			\Mind::write('commitChanged', true, $this->data['version']);
		else
		{
			$this->data['version']--;
			\Mind::write('commitUnchanged', true, $this->data['version']);
		}
		$this->changed= $commited;
	}
	
	/**
	 * Gets the data from the current version.
	 * 
	 * @param integer $vs
	 * @return Array The current version id, version and the project's creator
	 */
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
	
	/**
	 * This method will get the passed entity attributes.
	 * 
	 * @param mixed $entity you can pass either the pk or the name of the entity
	 * @return Array the query return itself
	 */
	public function getEntity($entity)
	{
		if(\is_string($entity))
			return $this->getCurrentEntities(false, $entity);
		else
			return $this->getCurrentEntities(false, false, $entity);
	}
	
	/**
	 * Commits the current data to the SQLite database.
	 * It will commit the analyzed structure to the databse into a 
	 * new version of the current project, or return a message
	 * of "unchanged"
	 */
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
	
    public function close()
    {
        \Mind::$project= null;
    }
    
	/**
	 * The DAO\ProjectFactory constructor
	 * It calls the DAO\Project's constructor
	 * @param array $projectData
	 * @param integer $vs 
	 */
	public function __construct(Array $projectData, $vs=false)
	{
		parent::__construct();
		$this->data= $projectData;
		$this->db->execute('BEGIN');
		
		$data= $this->getCurrentVersion($vs);
		$this->data= array_merge($this->data, $data);
	}
}