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

    private static function filterURI($uri, $allowSlashes=false)
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
    
    private static function mountURI($uri= '', $allowSlashes= false)
    {
        $tmpURI = \Mind::$currentProject['path']."/";
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
            self::fixDirectory(dirname($uri));
        mkdir(filterURI($uri));
        \chmod($uri, 0777);
    }
    
    public static function createFile($uri)
    {
        self::fixDirectory(dirname($uri));
        $uri= self::mountURI($uri, true);
        return fopen($uri, 'wb+');
    }
}