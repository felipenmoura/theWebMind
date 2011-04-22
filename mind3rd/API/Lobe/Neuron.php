<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lobe;
/**
 * Description of Neuron
 *
 * @author felipe
 */
abstract class Neuron {
    
	public static function listLobes()
    {
        $list= Array();
        $d = dir(\theos\ProjectFileManager::getLobesDir());
        while (false !== ($entry = $d->read()))
        {
            if($entry!= 'Neuron.php' && $entry[0] != '.')
                $list[]= $entry;
        }
        $d->close();
        return $list;
    }
}