<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Logs different types of messages.
 * 
 * This class logs different types of messages innto files.
 * The available types are the following:
 *   user User interactions, like login or login failure
 *   project Interactions with projects, like creating a project, add a user to it or commiting it.
 *   sys System logs, like errors or warnings(lines starting with E represent errors, while W represents warnings)
 * 
 * @author felipenmoura
 */
class MindLog {
    
    const LOG_TYPE_USER   = 'user';
    const LOG_TYPE_PROJECT= 'project';
    const LOG_TYPE_SYS    = 'sys';
    
    public static function log($type, $msg, Array $details= null){
        GLOBAL $_MIND;
        
        if($details)
            $msg." - Details: ".json_encode($details);
        
        $msg.= ' - '.date('m/d/Y - H:i:s') . ' ip: '.(isset ($_SERVER['HTTP_X_FORWARDED_FOR'])?
                                                      $_SERVER['HTTP_X_FORWARDED_FOR']:
                                                      key_exists('REMOTE_ADDR', $_SERVER)?
                                                                 $_SERVER['REMOTE_ADDR']:
                                                                 'local');
        
        if(!file_exists(_MINDSRC_.\LOGS_DIR)){
            try{
                mkdir(_MINDSRC_.\LOGS_DIR);
            }catch(Exception $e){
                echo "ERROR: failed trying to create log! Could not creaate the directory "._MINDSRC_.\LOGS_DIR."!\n";
            }
        }
        
        switch($type){
            case self::LOG_TYPE_USER:{
                if(strtolower($_MIND->conf['log_user_interaction'])){
                    if(!@file_put_contents(_MINDSRC_.\LOGS_DIR.'user.log', $msg."\n", FILE_APPEND)){
                        echo "ERROR: failed trying to create log! please, check the writting permissions for "._MINDSRC_.\LOGS_DIR."!\n";
                    }
                }
                break;
            }
            case self::LOG_TYPE_PROJECT:{
                if(strtolower($_MIND->conf['log_project_interaction'])){
                    if(@file_put_contents(_MINDSRC_.\LOGS_DIR.'project.log', $msg."\n", FILE_APPEND)){
                        echo "ERROR: failed trying to create log! please, check the writting permissions for "._MINDSRC_.\LOGS_DIR."!\n";
                    }
                }
                break;
            }
            default:{
                if(!@file_put_contents(_MINDSRC_.\LOGS_DIR.'sys.log', $msg."\n", FILE_APPEND)){
                    echo "ERROR: failed trying to create log! please, check the writting permissions for "._MINDSRC_.\LOGS_DIR."!\n";
                }
                break;
            }
        }
    }
    
}