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
namespace Lobe;
/**
 * A Neuron.
 *
 * @author felipe
 * @package Lobe
 */
abstract class Neuron {
    
	public static function listLobes()
    {
        $list= Array();
        $d = dir(\theos\Gosh::getLobesDir());
        while (false !== ($entry = $d->read()))
        {
            if($entry!= 'Neuron.php' && $entry[0] != '.')
                $list[]= $entry;
        }
        $d->close();
        return $list;
    }
}