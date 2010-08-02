<?php
/**
*  Main Controller
*  @filesource
 * @author			Jaydson Gomes
 * @author			Felipe Nascimento
 * @copyright		TheWebMind.org
 * @package			Hello
 * @subpackage		Hello.application.controllers
 * @version			1.0
*/
class IndexController extends Zend_Controller_Action
{
	 public function init()
	 {
	 }
	 
	/**
	* Show the home page
	* @return void
	*/
	public function indexAction()
	{
		$view = Zend_Registry::get('view');
		$view->assign('header','pageHeader.phtml');
		$view->assign('body','index/bodyindex.phtml');
		$view->assign('footer','pageFooter.phtml');
	  	$this->_response->setBody($view->render('default.phtml'));
	}
}
