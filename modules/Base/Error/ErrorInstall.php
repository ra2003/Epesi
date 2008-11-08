<?php
/**
 * Provides error to mail handling.
 * 
 * @author Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 1.0
 * @license EPL
 * @package epesi-base-extra
 * @subpackage error
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Base_ErrorInstall extends ModuleInstall {
	public function install() {
		Base_LangCommon::install_translations($this->get_type());
		Variable::set('error_mail','');
		$this->create_data_dir();
		return true;
	}
	
	public function uninstall() {
		Variable::delete('error_mail');
		return true;
	}
	
	public function version() {
		return array('1.0.0');
	}

	public function requires($v) {
		return array(
			array('name'=>'Base/Mail', 'version'=>0),
			array('name'=>'Base/Lang', 'version'=>0),
			array('name'=>'Libs/QuickForm', 'version'=>0),
			array('name'=>'Base/Acl', 'version'=>0));
	}
	
}	

?>
