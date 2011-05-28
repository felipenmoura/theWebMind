<?php
/**
 * This class represents the structure required to use L10N
 *
 * @author felipe
 */
class pt implements l10n{
	private $messages= Array();

	public $name= 'pt';

	/**
	 * This method returns the translated message
	 * @method getMessage
	 * @param String $msg
	 * @return String Returns the string translated, or fals if the required message does not exist
	 */
    public function getMessage($msg)
	{
		if(isset($this->messages[$msg]))
			return $this->messages[$msg];
		else
			return false;
	}

	/**
	 * Construct
	 * This method should start all the messages, setting them
	 * to the class::$messages
	 */
	public function __construct()
	{
		//header('Content-type: text/html; charset=iso-8859-1');
		$this->messages['programRequired']			= Mind::message("API: Você precisa passar o nome do programa a ser executado.", '[Fail]', false);
		$this->messages['loginRequired']			= Mind::message("Auth: Tanto login quanto senha são obrigatórios.", '[Fail]', false);
		$this->messages['passwordRequired']			= "Precisarei do password para este usuário, por favor: ";
		$this->messages['autenticated']				= Mind::message("\nMain: %s autenticado", "[OK]", false);//"\n[OK] %s autenticated\n";
		$this->messages['not_allowed']				= Mind::message("\nMain: Você ainda não autenticou suas credenciais.", '[Fail]', false);
		$this->messages['not_allowed_tip']			= "Tente executar o comando\n    auth < login >\nUma senha será solicitada.\n";
		$this->messages['no_such_file']				= Mind::message("\nMain: Não conheço tal comando: '%s'", "[Fail]", false);
		$this->messages['auth_fail']				= Mind::message("\nAuth: Usuário ou senha inválidos", "[Fail]", false);
		$this->messages['bye']						= "Saindo, até logo...\n";
		$this->messages['thinking']				    = "Por favor aguarde, enquanto eu penso...\n";
		$this->messages['invalidCreateParams']		= Mind::message("Main: Parâmetros inválidos", "[Fail]", false);
		$this->messages['invalidOption']			= Mind::message("Opção inválida '%s'", '[Fail]', false);
		$this->messages['projectAlreadyExists']		= Mind::message("Lamento mas já existe um projeto de mesmo nome.", '[Fail]', false);
		$this->messages['projectCreated']			= Mind::message("Projeto '%s' criado.", '[Ok]', false);
		$this->messages['userCreated']				= Mind::message("Usuário '%s' criado", '[Ok]', false);
		$this->messages['noProject']				= Mind::message("Projeto '%s' não existe ou você não tem acesso ao mesmo.", '[Fail]', false);
		$this->messages['projectOpened']			= Mind::message("Acessando projeto '%s'", '[Ok]', false);
		$this->messages['currentProjectRequired']	= Mind::message("Primeiro você precisará abrir um projeto.", '[Fail]', false);
		$this->messages['currentProjectRequiredTip']= "Tente com o comando\n  use project <projectName>\n";
		$this->messages['analyseFirst']             = "Você precisará analisar o projeto, primeiro. O projeto ainda não foi analizado. Execute o comando 'analyze'.\n";
		$this->messages['sourceFileNotFound']	    = Mind::message("O arquivo fonte '%s' não foi encontrado para o projeto atual.", '[Fail]', false);
		$this->messages['permissionDenied']	        = Mind::message("Permissão negada pra acessar, criar, alterar ou excluir um arquivo.\nPor favor, libere acesso ao sistema para o diretório raíz do Mind.\n", '[Fail]', false);
		$this->messages['additionalCounterCol']     = "Campo adicionado automaticamente, a ser usado como diferencial para cada tupla, a fim de possibilizar um novo registro utilizando as mesmas demais chaves.";
		$this->messages['commitChanged']            = Mind::message("VCS: Consignado para versão %s", '[Ok]', false);
		$this->messages['commitUnchanged']          = Mind::message("VCS: Nada a consignar. Ainda na versão %s", '[Ok]', false);
		$this->messages['theosDBQrFail']            = Mind::message("Theos: Ocorreu um problema durante a execução da query abaixo:\n", '[Fail]', false);
		$this->messages['theosDBQrFailAbort']       = Mind::message("Theos: Todas as queries serão abortadas", '[Fail]', false);
		$this->messages['theosDBQrOk']              = Mind::message("Theos: Base de dados gerada com sucesso", '[Ok]', false);
		$this->messages['theosDBQrOk']              = Mind::message("Theos: Base de dados gerada com sucesso", '[Ok]', false);
		$this->messages['dbDriverNotFound']         = Mind::message("Theos: Database Driver não encontrado", '[Fail]', false);
		$this->messages['missingParameter']         = Mind::message("API: Argumento faltando: %s", '[Fail]', false);
		$this->messages['done']                     = Mind::message("API: Pronto\n", '[Ok]', false);
		$this->messages['invalidOptionValue']       = Mind::message("API: '%s' não é uma opção válida para '%s'", '[Fail]', false);
		$this->messages['runnintPHPUnit']           = "Aguarde...executando testes unitários...\n";
		$this->messages['mustBeAdmin']              = Mind::message("API: Você precisa ser o administrador para executar esta instrução", '[Fail]', false);
		$this->messages['cannotInstall']            = Mind::message("API: Para instalar um componente, será preciso alterar a propriedade 'allow_installation' no arquivo mind.ini para true", '[Fail]', false);
		$this->messages['phpunitNotFound']          = "Especifique onde encontram-se as classes do PHPUnit\n".
                                                      "Você pode configurar isto em mind3rd/env/mind.ini ini file\n".
                                                      "alterando a propriedade phpunit-src\n";

		$this->messages['http_invalid_requisition']	= <<<MESSAGE
   Requisição HTTP inválida.
   Você *deve* enviar alguma informação via POST juntamente com o parâmetro "program" com o nome do programa que deseja rodar e seus parâmetros.
MESSAGE;
	}
}