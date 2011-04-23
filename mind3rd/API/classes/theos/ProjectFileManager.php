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
class ProjectFileManager {
    
    protected static function filterURI($uri, $allowSlashes=true)
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
    
    protected static function mountURI($uri= '', $allowSlashes= true)
    {
        $tmpURI = '';
        $tmpURI.= self::filterURI($uri, $allowSlashes);
        return $tmpURI;
    }
    
    /**
     * Creates the given directorey.
     * Note that you can pass nested directories.
     * Example: 'new_dir/another_new_dir/the_final_new_dir/'
     * @param string $uri 
     */
    public static function createDir($uri)
    {
        $uri= self::setInnerURI($uri);
        self::fixDirectory($uri);
    }
    
    protected static function fixDirectory($uri)
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
    
    /**
     * Appends a string into a given file.
     * If the file does not exists, it creates it for you.
     * Again, it may be a nested uri.
     * 
     * @param string $file
     * @param string $data
     * @return boolean True in case of success, false otherwise.
     */
    public static function appendDataToFile($file, $data)
    {
        $file= self::setInnerURI($file);
        if(file_exists($file))
            return \file_put_contents($file, $data, \FILE_APPEND);
        return false;
    }
    
    protected static function setInnerURI($uri)
    {
        return \Mind::$currentProject['path']."/".$uri;
    }
    
    /**
     * Writes the given string in the file.
     * Note that this method will replace the old file's content.
     * If the file does not exist, it will be created.
     * Nested uris are allowed.
     * 
     * @param string $file
     * @param string $data
     * @return type 
     */
    public static function writeToFile($file, $data)
    {
        $file= self::setInnerURI($file);
        if(file_exists($file))
            return \file_put_contents ($file, $data);
        return false;
    }
    
    /**
     * Creates a file.
     * Nested URIs are allowed(any unexistent folder will be created, then).
     * 
     * @param string $uri
     * @param string $type Accepts null, 'general' or 'xml'
     * @return mixed the file handler or the SimpleXML from the created file.
     */
    public static function createFile($uri, $type='general')
    {
        $uri= self::setInnerURI($uri);
        self::fixDirectory(dirname($uri));
        $uri= self::mountURI($uri, true);
        if($type=='general')
            return fopen($uri, 'wb+');
        else{
                return self::createXMLFile($uri);
            }
    }
    
    /**
     * Creates an XML file.
     * Nested URIs are allowed.
     * 
     * @param string $uri
     * @return SimpleXML
     */
    public static function createXMLFile($uri)
    {
        if(file_exists($uri))
            return @\simplexml_load_file($uri);
        $h= fopen($uri, 'wb+');
        $content= '<?xml version="1.0" ?><root></root>';
        fwrite($h, $content);
        fclose($h);
        return @\simplexml_load_file($uri);
    }
}