Mind.Plugins.DBMapping= {
	/* needed methods */
	Run: function(){
		//this.Save('', '');
	},
	Load: function(){
		
	},
	/***************************/
	Init: function(){
		
	}
}
/*
	os metodos Save, Unlink, MkDir, List e LoadFile serão adicionados ao plugin, pelo proprio Mind, sendo assim, são palavras reservadas
	Assinaturas:
	
	
	function Save(file_path, content [, flag]):boolean;
	Retorno: true caso funcione, false caso algum erro ocorra
	Parametros:
		file_path: endereço, incluindo diretório para salvar o arquivo, caso nao exista, será criado
		content: conteúdo a ser salvo no arquivo
		flag: true pra concatenar content ao conteudo do arquivo, false, para substituir todo o conteúdo, caso o arquivo já exista
	
	function Unlink(file_path):boolean;
	Retorno: true caso funcione, false caso algum erro ocorra
	Parametros:
		file_path: endereço, incluindo diretório, do arquivo a ser removido
		
	function MkDir(dir_pach):boolean;
	Retorno: true caso funcione, false caso algum erro ocorra
	Parametros:
		dir_pach: endereço, incluindo diretório pais, de onde o novo diretório deve ser criado
		
	function List(dir_pach):ObjectCollection;
	Retorno: ObjectCollection[
									Object[
											name:nome do arquivo ou diretório,
											type:directory ou file,
											address:endereço absoluto do arquivo
										  ]
							 ]
	Parametros:
		dir_pach: endereço, incluindo diretório pais, do diretório cujos arquivos serão listado
	
	function LoadFile(file_path):Object;
	Retorno: Object[
						name:nome do arquivo,
						address:endereço completo do arquivo,
						size:tamanho do arquivo,
						content:conteúdo do arquivo,
						lastChange:data da ultima modificação do arquivo
				   ]
	Parametros:
		file_path: endereço, incluindo diretório, do arquivo a ser carregado
*/