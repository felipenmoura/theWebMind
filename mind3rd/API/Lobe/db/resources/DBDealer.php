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
namespace Lobe\db\resources;
/**
 * DBDealer.
 *
 * @author felipe
 * @package Lobe
 * @subpackage db
 * @subpackage resources
 */
class DBDealer {
    
    private $dbal;
    private $curEntities;
    
    public function createTable($query)
    {
        
        $prjDao= new \DAO\Project();
        $curEntities= $prjDao->getCurrentEntities();
        foreach($curEntities as $k=>$en)
        {
            $curEntities[$k]['properties']= $prjDao->getProperties($en);
            $this->curEntities[$en['name']]= $curEntities[$k]['properties'];
            
            $this->logCurrentStateOfDB($curEntities[$k]);
            
        }
        
        $exec = $this->dbal->execute($query);

        /*if(\DQB\QueryFactory::tableExists($queryData->table['name']))
        {
            
        }*/
        if($exec === false)
        {
            \Mind::write('theosDBQrFail');
            echo $query."\n";
            echo $this->dbal->getErrorMessage();
            \Mind::write('theosDBQrFailAbort');
            return false;
        }
        return true;
    }
    
    public static function hash($element)
    {
        return md5(JSON_encode($element));
    }
    
    private function logCurrentStateOfDB($en)
    {
        // we will use XML to write the current state of the generated DB
        $xmlFile= '.mind/tables/'.$en['name'].'.xml';
        $xml= \theos\ProjectFileManager::createFile($xmlFile, 'xml');
        // we need a new xml, so we will replace it for an empty one
        $xml= new \SimpleXMLElement('<root></root>');
        $xml->table['name']= $en['name'];
        $xml->table['version']= $en['version'];
        $xml->table['hash']= self::hash($en['properties']);

        foreach($en['properties'] as $prop)
        {
            $xprop= $xml->table->addChild('property');

            $xprop['hash']            = self::hash($prop);
            $xprop['name']            = $prop['name'];
            $xprop['type']            = $prop['type'];
            $xprop['size']            = $prop['size'];
            $xprop['options']         = $prop['options'];
            $xprop['is_pk']           = $prop['is_pk'];
            $xprop['default_value']   = $prop['default_value'];
            $xprop['unique_value']    = $prop['unique_value'];
            $xprop['required']        = $prop['required'];
            $xprop['comment']         = $prop['comment'];
            $xprop['ref_to_property'] = $prop['ref_to_property'];
        }

        \theos\ProjectFileManager::writeToFile($xmlFile, $xml->asXML());
    }
    
    public function __construct(\MindDBAL &$dbal)
    {
        $this->dbal= $dbal;
    }
}