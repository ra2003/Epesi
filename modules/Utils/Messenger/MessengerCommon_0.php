<?php
/**
 * Popup message to the user
 * @author pbukowski@telaxus.com
 * @copyright pbukowski@telaxus.com
 * @license EPL
 * @version 0.1
 * @package utils-messenger
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Utils_MessengerCommon extends ModuleCommon {
	public static function applet_caption() {
		return "Messenger alarms";
	}

	public static function applet_info() {
		return "Displays last alarms";
	}
	
	public static function delete_by_parent_module($m) {
		$ret = DB::Execute('SELECT id FROM utils_messenger_message WHERE parent_module=%s',array($m));
		while($row = $ret->FetchRow())
			DB::Execute('DELETE FROM utils_messenger_users WHERE message_id=%d',array($row['id']));
		DB::Execute('DELETE FROM utils_messenger_message WHERE parent_module=%s',array($m));
	}
	
	public static function delete_by_id($id) {
		$mid = md5($id);
		$ret = DB::Execute('SELECT id FROM utils_messenger_message WHERE page_id=\''.$mid.'\'');
		while($row = $ret->FetchRow())
			DB::Execute('DELETE FROM utils_messenger_users WHERE message_id=%d',array($row['id']));
		DB::Execute('DELETE FROM utils_messenger_message WHERE page_id=\''.$mid.'\'');

	}
	
	public static function add($id,$parent_type,$message,$alert_on, $callback_method,$callback_args=null,$users=null) {
		$callback_args = isset($callback_args)?((is_array($callback_args))?$callback_args:array($callback_args)):array();
		if(!isset($users)) $users = Acl::get_user();
		DB::Execute('INSERT INTO utils_messenger_message(page_id,parent_module,message,callback_method,callback_args,created_on,created_by,alert_on) VALUES(%s,%s,%s,%s,%s,%T,%d,%T)',array(md5($id),$parent_type,$message,serialize($callback_method),serialize($callback_args),time(),Acl::get_user(),$alert_on));
		$id = DB::Insert_ID('utils_messenger_message','id');
		if(is_array($users)) {
			foreach($users as $r)
				DB::Execute('INSERT INTO utils_messenger_users(message_id,user_login_id) VALUES (%d,%d)',array($id,$r));
		} else
			DB::Execute('INSERT INTO utils_messenger_users(message_id,user_login_id) VALUES (%d,%d)',array($id,$users));
	}

	public static function tray_notification() {
		$arr = DB::GetAll('SELECT m.* FROM utils_messenger_message m INNER JOIN utils_messenger_users u ON u.message_id=m.id WHERE u.user_login_id=%d AND u.done=0 AND m.alert_on<%T',array(Acl::get_user(),time()));
		$ret = array();
		foreach($arr as $row) {
			ob_start();
			$m = call_user_func_array(unserialize($row['callback_method']),unserialize($row['callback_args']));
			ob_clean();
			$ret['messenger_'.$row['id']] = Base_LangCommon::ts('Utils/Messenger','Alert on: %s',array(Base_RegionalSettingsCommon::time2reg($row['alert_on'])))."<br>".str_replace("\n",'<br>',$m).($row['message']?"<br>".Base_LangCommon::ts('Utils/Messenger',"Alarm comment: %s",array($row['message'])):'');
		}
		return array('alerts'=>$ret);
	}
}

eval_js_once('utils_messenger_on = true; utils_messenger_refresh = function(){'.
			'if(utils_messenger_on) new Ajax.Request(\'modules/Utils/Messenger/refresh.php\',{method:\'get\'});'.
			'};setInterval(\'utils_messenger_refresh()\',180000);utils_messenger_refresh()');

?>