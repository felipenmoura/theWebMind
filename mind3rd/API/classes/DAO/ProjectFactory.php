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
			return true;
		return false;
	}
	
	public function __construct(Array $projectData)
	{
		parent::__construct();
		$this->data= $projectData;
		
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
		print_r($this->data);
		$this->addNewVersion();
			
	}
}