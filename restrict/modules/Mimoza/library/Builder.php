<?php

Mimoza_Loader::loadClass('Mimoza_Builder_Abstract');
Mimoza_Loader::loadClass('Mimoza_Filter_Replace');

/**
 * Construtor Principal
 * Inicialização do Pacote Local de Construtor
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see http://code.google.com/p/webmind/Mimoza
 * 
 * @uses Project
 * @uses Mimoza_Builder_Abstract
 * @uses Mimoza_Filter_Replace
 * 
 * @package Mimoza
 * @subpackage Builder
 *
 */
class Mimoza_Builder extends Mimoza_Builder_Abstract
{
    /**
     * Construtor da Classe
     * Inicialização do Diretório de Arquivos Modelo para Renderização
     * @param Project $project Projeto Webmind
     */
    public function __construct(Project $project)
    {
        parent::__construct($project);
        $path = realpath(dirname(dirname(__FILE__)) . '/sources');
        $this->setScriptPath($path);

        $filter = new Mimoza_Filter_Replace();
        $filter->replace('[php]', '<?php')->replace('[/php]', '?>');
        $this->addFilter($filter);
    }
}