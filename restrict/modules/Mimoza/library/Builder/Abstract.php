<?php

Mimoza_Loader::loadClass('Mimoza_Filter_Interface');
Mimoza_Loader::loadClass('Mimoza_Helper_Abstract');

/**
 * Mimoza Builder
 * Construtor de Saída para Arquivos Utilizando Formatos
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see    http://code.google.com/p/webmind/Mimoza
 * 
 * @uses Project
 * @uses Mimoza_Filter_Interface
 * @uses Mimoza_Helper_Abstract
 * 
 * @package Mimoza
 * @subpackage Builder
 *
 */
abstract class Mimoza_Builder_Abstract
{
    /**
     * Projeto Padrão do Webmind
     * @var Project
     */
    protected $_project;

    /**
     * Container de Valores
     * @var array
     */
    protected $_values = array();

    /**
     * Caminho para os Formatos de Arquivos
     * @var string
     */
    protected $_scriptPath = null;

    /**
     * Codificação de Entrada e Saída para Função de Escape
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Container de Filtros
     * @var array
     */
    protected $_filters = array();

    /**
     * Construtor da Classe
     * @param Project $project Projeto Informado pelo Webmind
     */
    public function __construct(Project $project)
    {
        $this->_setProject($project)->init();
    }

    /**
     * Inicializador do Objeto
     * Configura as Instâncias sem Necessidade de Especialização de Construtor
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function init()
    {
        return $this;
    }

    /**
     * Configuração para Projeto do Webmind
     * @param Project $project Projeto Informado pelo Webmind
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    protected function _setProject(Project $project)
    {
        $this->_project = $project;
        return $this;
    }

    /**
     * Informa o Projeto do Webmind Configurado
     * @return Project Projeto do Webmind
     */
    public function getProject()
    {
        return $this->_project;
    }

    /**
     * Configuração para Caminho para Formatos de Arquivos
     * @param string $path Diretório do Sistema
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function setScriptPath($path)
    {
        $this->_scriptPath = $path;
        return $this;
    }

    /**
     * Informa o Caminho para Formatos Configurado
     * @return string Diretório do Sistema Configurado
     */
    public function getScriptPath()
    {
        return $this->_scriptPath;
    }

    /**
     * Configuração de Valores
     * Adiciona um Valor para Chave no Container de Informações
     * Valor Configurado como Nulo é Descartado do Conjunto de Valores
     * @param string $key Chave Identificadora
     * @param mixed $value Valor para Configuração
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function setValue($key, $value = null)
    {
        $key = (string) $key;
        if ($value === null) {
            unset($this->$key);
        } else {
            $this->_values[$key] = $value;
        }
        return $this;
    }

    /**
     * Valor Configurado
     * Informa um Valor Identificado pela Chave Inicializado Previamente
     * @param string $key Chave Identificadora
     * @return mixed Valor Representado pela Chave
     */
    public function getValue($key)
    {
        $key = (string) $key;
        $value = null;
        if (isset($this->$key)) {
            $value = $this->_values[$key];
        }
        return $value;
    }

    /**
     * Método de Escape para Caracteres Especiais
     * @param string $var Variável para Escape
     * @return string Valor Convertido para Tabela de Hipertexto
     */
    public function escape($var)
    {
        return htmlspecialchars((string) $var, ENT_COMPAT, $this->_encoding);
    }

    /**
     * Adição de Filtro
     * Enfileiramento de Filtros para Manipulação de Dados após Processamento
     * @param Mimoza_Filter_Interface $filter Filtro Adicional
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function addFilter(Mimoza_Filter_Interface $filter)
    {
        $class = get_class($filter);
        $this->_filters[$class] = $filter;
        return $this;
    }

    /**
     * Remoção de Filtros
     * Retira um Filtro Adicionado na Fila de Processamento
     * @param string $name Nome da Classe do Filtro para Remoção
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function removeFilter($name)
    {
        $name = (string) $name;
        if (isset($this->_filters[$name])) {
            unset($this->_filters[$name]);
        }
        return $this;
    }

    /**
     * Limpeza Completa de Filtros
     * Remove Todos os Filtros da Fila de Processamento
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function clearFilters()
    {
        $this->_filters = array();
        return $this;
    }

    /**
     * Filtro Configurado
     * Informa o Filtro Adicionado na Lista de Processamento Conforme Nome
     * @param string $name Nome do Filtro
     * @return null|Mimoza_Filter_Interface Filtro Configurado ou Nulo
     */
    public function getFilter($name)
    {
        $name = (string) $name;
        $filter = null;
        if (isset($this->_filters[$name])) {
            $filter = $this->_filters[$name];
        }
        return $filter;
    }

    /**
     * Filtros Configurados
     * Retorna Todos os Filtros Configurados na Fila de Processamento
     * @return array Conjunto de Filtros
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * Renderiza um Arquivo
     * Utiliza os Valores Configurados no Construtor para Montagem
     * Filtra o Conteúdo Gerado Conforme Fila de Processamento
     * @param string $script Nome do Arquivo para Renderização
     * @return string Conteúdo Gerado e Filtrado
     */
    public function render($script)
    {
        $script = (string) $script;
        ob_start();
        $this->_run($script);
        $content = ob_get_clean();
        foreach ($this->getFilters() as $filter) {
            $content = $filter->filter($content);
        }
        return $content;
    }

    /**
     * Execução de Arquivo
     * Processa um Arquivo Solicitado para Construção de Saída
     * @param string $script Nome do Arquivo para Saída Padronizada
     * @throws Mimoza_Builder_Exception Arquivo Não Encontrado
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    protected function _run($script)
    {
        $script = (string) $script;
        $path = $this->getScriptPath();
        $filename = realpath($path . '/' . $script);
        if (!is_file($filename)) {
            Mimoza_Loader::loadClass('Mimoza_Loader_Exception');
            $message = sprintf('File "%s" not found', $filename);
            throw new Mimoza_Builder_Exception($message);
        }
        include $filename;
        return $this;
    }

    /**
     * Método Abstrato
     * Construtor para Especialização da Classe
     * Criado para Centralização de Renderização de Um ou Mais Arquivos
     * @param mixed $element Elemento para Construção
     * @return string Conteúdo Construído a Partir das Informações do Elemento
     */
    public abstract function build($element);

    /**
     * Método Mágico
     * Configuração de Valores
     * Adiciona um Valor para Chave no Container de Informações
     * Valor Configurado como Nulo é Descartado do Conjunto de Valores
     * @param string $key Chave Identificadora
     * @param mixed $value Valor para Configuração
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function __set($key, $value)
    {
        return $this->setValue($key, $value);
    }

    /**
     * Método Mágico
     * Valor Configurado
     * Informa um Valor Identificado pela Chave Inicializado Previamente
     * @param string $key Chave Identificadora
     * @return mixed Valor Representado pela Chave
     */
    public function __get($key)
    {
        return $this->getValue($key);
    }

    /**
     * Método Mágico
     * Verificador de Chaves
     * Efetua Checagem Informando Inicialização Positiva de Chave
     * @param string $key Chave Identificadora
     * @return bool Confirmação de Chave Inicializada
     */
    public function __isset($key)
    {
        $key = (string) $key;
        return isset($this->_values[$key]);
    }

    /**
     * Método Mágico
     * Desconfigurador de Chaves
     * Retira um Valor Configurado Através de Sua Chave Identificadora
     * @param string $key Chave Identificadora
     * @return Mimoza_Builder_Abstract Próprio Objeto para Encadeamento
     */
    public function __unset($key)
    {
        $key = (string) $key;
        if (isset($this->$key)) {
            unset($this->_values[$key]);
        }
        return $this;
    }

    /**
     * Método Mágico
     * Chamada de Métodos Auxiliares
     * Construção em Tempo de Execução de Objetos Auxiliares
     * @param string $method Nome do Método Solicitado
     * @param array $args Argumentos Informados
     * @return mixed Resultado da Execução do Auxiliar
     */
    public function __call($method, $args)
    {
        $class = 'Mimoza_Helper_' . ucfirst($method);
        Mimoza_Loader::loadClass($class);
        $helper = new $class($this);
        return $helper->execute($args);
    }
}