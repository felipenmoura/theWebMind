<?php
/**
 * This class represents the structure required to use L10N
 *
 * @author felipe
 */
class pt {
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
		$this->messages['invalidCreateParams']		= Mind::message("Main: Parâmetros inválidos", "[Fail]", false);
		$this->messages['invalidOption']			= Mind::message("Opção inválida '%s'", '[Fail]', false);
		$this->messages['projectAlreadyExists']		= Mind::message("Lamento mas já existe um projeto de mesmo nome.", '[Fail]', false);
		$this->messages['projectCreated']			= Mind::message("Projeto '%s' criado.", '[Ok]', false);
		$this->messages['userCreated']				= Mind::message("Usuário '%s' criado", '[Ok]', false);
		$this->messages['noProject']				= Mind::message("Projeto '%s' não existe ou você não tem acesso ao mesmo.", '[Fail]', false);
		$this->messages['projectOpened']			= Mind::message("Acessando projeto '%s'", '[Ok]', false);
		$this->messages['currentProjectRequired']	= Mind::message("Primeiro você precisará abrir um projeto.", '[Fail]', false);
		$this->messages['currentProjectRequiredTip']= "Tente com o comando\n  use project <projectName>\n";

		$this->messages['http_invalid_requisition']	= <<<MESSAGE
   Requisição HTTP inválida.
   Você *deve* enviar alguma informação via POST juntamente com o parâmetro "program" com o nome do programa que deseja rodar e seus parâmetros.
MESSAGE;
	}
}