<?php

/**
 * Mimoza Loader
 * Carrega Classes da Estrutura do Mimoza
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see http://code.google.com/p/webmind/Mimoza
 * 
 * @uses Mimoza_Loader_Exception
 * 
 * @package Mimoza
 * @subpackage Loader
 *
 */
class Mimoza_Loader
{
    /**
     * Mimoza Singleton
     * Padrão de Desenvolvimento
     * @var Mimoza_Loader
     * @staticvar
     */
    protected static $_instance = null;

    /**
     * Diretório Raiz do Pacote Mimoza
     * @var string
     */
    protected $_root;

    /**
     * Diretórios para Pesquisa
     * @var array
     */
    protected $_paths = array(
        'Builder_' => '/builders',
        'Mimoza_'  => '/library',
    );

    /**
     * Construtor
     * Padrão de Desenvolvimento Singleton
     */
    protected function __construct()
    {
        $this->_root = realpath(dirname(dirname(__FILE__)));
    }

    /**
     * Retorna os Diretórios para Pesquisa
     * @return array
     */
    public function getPaths()
    {
        return $this->_paths;
    }

    /**
     * Método Singleton
     * Padrão de Desenvolvimento Singleton
     * Mantém somente uma variável instanciada no sistema
     * @return Mimoza_Loader
     * @static
     */
    public function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Carrega um Arquivo para Importação
     * @param string $name Nome do Arquivo
     * @throws Mimoza_Loader_Exception
     * @return Mimoza_Loader
     */
    public function loadFile($name)
    {
        $filename = $this->_root . $name;
        if (!is_file($filename)) {
            self::loadClass('Mimoza_Loader_Exception');
            $message = sprintf('Arquivo "%s" não encontrado', $name);
            throw new Mimoza_Loader_Exception($message);
        }
        require_once $filename;
        return $this;
    }

    /**
     * Carrega uma Classe Interna em Arquivos
     * Padrão de Estrutura de Pacotes do Zend_Framework
     * @param $name Nome do Arquivo
     * @return Mimoza_Loader
     */
    public static function loadClass($name)
    {
        $loader = self::getInstance();

        /*
         * Transforma os Nomes de Pacotes em Caminhos de Pesquisa
         */
        foreach ($loader->getPaths() as $alias => $path) {
            $packages = explode('_', (string) name);
            if ($packages[0] . '_' == alias) {
                $packages[0] = $path;
            }
        }

        $filename = implode('_', $packages);

        $filename = str_replace('_', DIRECTORY_SEPARATOR, $filename) . '.php';
        $loader->loadFile($name);
        return $loader;
    }
}