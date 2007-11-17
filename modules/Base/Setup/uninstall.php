<?php
/**
 * Setup class
 * 
 * This file contains setup module.
 * 
 * @author Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 0.9
 * @license SPL
 * @package epesi-base-extra
 * @subpackage setup
 */
define('CID',false);
require_once('../../../include.php');
ModuleManager::load_modules();
require_once('modules/Libs/QuickForm/requires.php');

if(!isset($_GET['user']) || !isset($_GET['pass'])) {
	$form = new HTML_QuickForm('loginform','get',$_SERVER['PHP_SELF'].'?'.http_build_query($_GET));
	$form->addElement('text','user','Login');
	$form->addElement('password','pass','Password');
	$form->addElement('submit',null,'Ok');
	$form->display();
	exit();
}
$user = $_GET['user'];
$pass = $_GET['pass'];
if((!DB::GetOne('SELECT count(id) FROM user_login ul INNER JOIN user_password up ON ul.id=up.user_login_id WHERE login=%s AND password=%s',array($user,md5($pass))) ||
	 !Acl::check('Administration','Main','Users',$user)) && !Variable::get('anonymous_setup')) die('Access denied');

/*
 * Ok, you are in.
 */
if(isset($_GET['mod'])) {
	call_user_func($_GET['mod']);
//	print('<hr><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$user,'pass'=>$pass)).'">back</a>');
} else {
	print('<a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$user,'pass'=>$pass,'mod'=>'main_setup')).'">Modules administration</a><br>');
	print('<a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$user,'pass'=>$pass,'mod'=>'update_load_prio_array')).'">Update load priority array</a><br>');
	print('<a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$user,'pass'=>$pass,'mod'=>'themeup')).'">Update default theme</a><br>');
	print('<hr>');
	print('<a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$user,'pass'=>$pass,'mod'=>'phpinfo')).'">PHP info</a><br>');
	print('<a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$user,'pass'=>$pass,'mod'=>'db_info')).'">database info</a><br>');
	print('<a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$user,'pass'=>$pass,'mod'=>'config_info')).'">config info</a><br>');
}

function main_setup() { 
	//create default module form
	$form = new HTML_QuickForm('modulesform','post',$_SERVER['PHP_SELF'].'?'.http_build_query($_GET));
	$form->addElement('header', null, 'Uninstall module');

	foreach(ModuleManager::$modules as $name=>$v)
		$form->addElement('checkbox',$name,$name.' (ver '.$v.')');

	$form->addElement('submit', 'submit_button', 'OK');

	//validation or display
	if ($form->validate()) {
		//uninstall
		$vals = $form->exportValues();
		$modules_prio_rev = array();
		$ret = DB::Execute('SELECT * FROM modules ORDER BY priority DESC');
		while($row = $ret->FetchRow())
			if(isset($vals[$row['name']]) && $vals[$row['name']]) {
				if (!ModuleManager::uninstall($row['name'])) {
					die('Unable to remove module '.$row['name']);
				}
				if(count(ModuleManager::$modules)==0)
					die('No modules installed');
			}
		print('<hr><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$_GET['user'],'pass'=>$_GET['pass'],'mod'=>'main_setup')).'">back</a>');
	} else {
		$form->display();
		print('<hr><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$_GET['user'],'pass'=>$_GET['pass'])).'">back</a>');
	}
}

function themeup() {
print('<span style="font-family: courier; font-size: 11px;">');

$data_dir = 'data/Base_Theme/templates/default/';
print('Cleaning up directory...<br><br>');
$content = scandir($data_dir);
foreach ($content as $name){
	if ($name == '.' || $name == '..') continue;
	recursive_rmdir($data_dir.$name);
}

$ret = DB::Execute('SELECT * FROM modules');
while($row = $ret->FetchRow()) {
	$directory = 'modules/'.str_replace('_','/',$row[0]).'/theme_'.$row['version'];
	if (!is_dir($directory)) $directory = 'modules/'.str_replace('_','/',$row[0]).'/theme';
	$mod_name = $row[0];
	$data_dir = 'data/Base_Theme/templates/default/';
	print('<span style="color: #339933;">Checking theme:&nbsp;&nbsp;&nbsp;'.$directory.'</span><br>');
	if (!is_dir($directory)) continue;
	$content = scandir($directory);
	print('<span style="color: #336699;">Installing theme:&nbsp;'.$directory.'</span><br>');
	foreach ($content as $name){
		if($name == '.' || $name == '..' || ereg('^[\.~]',$name)) continue;
		recursive_copy($directory.'/'.$name,$data_dir.$mod_name.'__'.$name);
	}
}

function install_default_theme_common_files($dir,$f) {
	if(class_exists('ZipArchive')) {
		$zip = new ZipArchive;
		if ($zip->open($dir.$f.'.zip') == 1)
			$zip->extractTo('data/Base_Theme/templates/default/');
		return;
	}
	mkdir('data/Base_Theme/templates/default/'.$f);
	$content = scandir($dir.$f);
	foreach ($content as $name){
		if ($name == '.' || $name == '..') continue;
		$path = $dir.$f.'/'.$name;
		if (is_dir($path))
			install_default_theme_common_files($dir,$f.'/'.$name);
		else
			copy($path,'data/Base_Theme/templates/default/'.$f.'/'.$name);
	}
}
install_default_theme_common_files('modules/Base/Theme/','images');

print('</span>');
print('<hr><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$_GET['user'],'pass'=>$_GET['pass'])).'">back</a>');
}

function update_load_prio_array() {
	ModuleManager::create_load_priority_array();
	print('updated');
	print('<hr><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$_GET['user'],'pass'=>$_GET['pass'])).'">back</a>');
}

function db_info() {
	print('Database host: '.DATABASE_HOST.'<br>');
	print('Database user: '.DATABASE_USER.'<br>');
	print('Database password: '.DATABASE_PASSWORD.'<br>');
	print('Database name: '.DATABASE_NAME.'<br>');
	print('Database driver: '.DATABASE_DRIVER.'<br>');
	print('<hr><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$_GET['user'],'pass'=>$_GET['pass'])).'">back</a>');
}

function config_info() {
	print('Debug: '.(DEBUG?'YES':'no').'<br>');
	print('Display modules loading times: '.(MODULE_TIMES?'YES':'no').'<br>');
	print('Display sql queries processing times: '.(SQL_TIMES?'YES':'no').'<br>');
	print('Strip output html from comments: '.(STRIP_OUTPUT?'YES':'no').'<br>');
	print('Display additional error info: '.(DISPLAY_ERRORS?'YES':'no').'<br>');
	print('Report all errors (E_ALL): '.(REPORT_ALL_ERRORS?'YES':'no').'<br>');
	print('GZIP output: '.(GZIP_OUTPUT?'YES':'no').'<br>');
	print('GZIP client web browser history: '.(GZIP_HISTORY?'YES':'no').'<br>');
	print('<hr><a href="'.$_SERVER['PHP_SELF'].'?'.http_build_query(array('user'=>$_GET['user'],'pass'=>$_GET['pass'])).'">back</a>');
}

?>
