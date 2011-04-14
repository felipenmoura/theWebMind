<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace theos;
/**
 * Description of ProjectFileManager
 *
 * @author felipe
 */
final class ProjectFileManager {

    private static function filterURI($uri, $allowSlashes=true)
    {
        $uri= \urlencode(\utf8_encode($uri));
        if($allowSlashes)
           $uri= preg_replace('/%2F/', '/', $uri);
        
        while(false !== \strpos('..', $uri))
        {
            $uri= str_replace('..', '', $uri);
        }
        $uri= preg_replace('/^\\|\//', '', $uri);
        return $uri;
    }
    
    private static function mountURI($uri= '', $allowSlashes= true)
    {
        $tmpURI = '';//\Mind::$currentProject['path']."/";
        $tmpURI.= self::filterURI($uri, $allowSlashes);
        return $tmpURI;
    }
    
    public static function createDir()
    {
        
    }
    
    private static function fixDirectory($uri)
    {
        if(file_exists($uri))
            return true;
        if(!file_exists(dirname($uri)))
        {
            self::fixDirectory(dirname($uri));
        }
        mkdir(self::filterURI($uri));
        chmod($uri, 0777);
    }
    
    public static function appendDataToFile($file, $data)
    {
        if(file_exists($file))
            return \file_put_contents ($file, $data, \FILE_APPEND);
        return false;
    }
    
    private static function setInnerURI($uri)
    {
        return \Mind::$currentProject['path']."/".$uri;
    }
    
    public static function writeToFile($file, $data)
    {
        $file= self::setInnerURI($file);
        if(file_exists($file))
            return \file_put_contents ($file, $data);
        return false;
    }
    
    public static function createFile($uri, $type='general')
    {
        $uri= self::setInnerURI($uri);
        self::fixDirectory(dirname($uri));
        $uri= self::mountURI($uri, true);
        if($type=='general')
            return fopen($uri, 'wb+');
        else{
                if(file_exists($uri))
                    return @\simplexml_load_file($uri);
                $h= fopen($uri, 'wb+');
                $content= '<?xml version="1.0" ?><root></root>';
                fwrite($h, $content);
                fclose($h);
                return @\simplexml_load_file($uri);
            }
    }
}