<?php
/**
 * Provides login audit log
 * @author pbukowski@telaxus.com & jtylek@telaxus.com
 * @copyright pbukowski@telaxus.com & jtylek@telaxus.com
 * @license EPL
 * @version 0.1
 * @package epesi-base
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class CRM_LoginAudit extends Module {

    public function admin() {

	    $this->lang = & $this->init_module('Base/Lang');

        $user = $this->get_module_variable('filter_user',-1);
        $form = $this->init_module('Libs/QuickForm',null,'filter');
        $form->setDefaults(array('users'=>$user));
        $ret = DB::Execute('SELECT id FROM user_login');
	            $users = array(-1=>$this->lang->t('All'));
	            while($row = $ret->FetchRow())
	                $users[$row['id']] = Base_UserCommon::get_user_login($row['id']);
	            $form->addElement('select','users',$this->lang->t('Select user'), $users, 'onChange="'.$form->get_submit_form_js().'"');
		$user = $form->exportValue('users');
        $form->display();
        $this->set_module_variable('filter_user',$user);

		$gb = & $this->init_module('Utils/GenericBrowser',null,'login_audit');

		$gb->set_table_columns(array(
						array('name'=>$this->lang->t('<b>Login</b> -> User Name'),'order'=>'b.user_login_id','width'=>20),
						array('name'=>$this->lang->t('Start'), 'order'=>'b.start_time', 'width'=>15),
						array('name'=>$this->lang->t('End'),'order'=>'b.end_time','width'=>15),
                        array('name'=>$this->lang->t('Duration'),'width'=>10),
                        array('name'=>$this->lang->t('IP Address'),'order'=>'b.ip_address','width'=>10),
                        array('name'=>$this->lang->t('Host Name'),'order'=>'b.host_name','width'=>30)));

        $gb->set_default_order(array($this->lang->t('End')=>'DESC'));

		if($user>=0){
            $query = 'SELECT b.user_login_id, b.start_time, b.end_time, b.ip_address, b.host_name FROM base_login_audit b WHERE b.user_login_id='.$user;
            $query_qty = 'SELECT count(b.id) FROM base_login_audit b WHERE b.user_login_id='.$user;
        } else {
            $query = 'SELECT b.user_login_id, b.start_time, b.end_time, b.ip_address, b.host_name FROM base_login_audit b';
            $query_qty = 'SELECT count(b.id) FROM base_login_audit b';
        }

		$ret = $gb->query_order_limit($query, $query_qty);

        if($ret)
			while(($row=$ret->FetchRow())) {
				$c = CRM_ContactsCommon::get_contact_by_user_id($row['user_login_id']);
                $ulogin = Base_UserCommon::get_user_login($row['user_login_id']);
                $uid = 'Contact not set';
                if($c) {
                        $uid = $c['first_name'].' '.$c['last_name'];
                        }
                $offset=strtotime("1970-01-01 00:00:00");
                $sess_time=date("G:i:s",strtotime($row['end_time'])-strtotime($row['start_time'])+$offset);
                $gb->add_row('<b>'.$ulogin.'</b> -> '.$uid,$row['start_time'],$row['end_time'],$sess_time,$row['ip_address'],$row['host_name']);
			}

		$this->display_module($gb);

        Base_ActionBarCommon::add('settings',$this->lang->t('Maintenance'),$this->create_callback_href(array($this, 'purge_log')));
        return true;
	}

    public function purge_log(){

        # Return to main body
        if($this->is_back()) return false;

        $this->lang = & $this->init_module('Base/Lang');

        $form = $this->init_module('Libs/QuickForm',null,'purge_date');

        $form->addElement('header',null,$this->lang->t('Audit Log Maintenance'));
        $form -> addElement('html','<tr><td colspan=2><br />'.$this->lang->t('Purge log with records older than specified number of days:').'</td></tr>');
        $form->addElement('select','purge_date',$this->lang->t('Select number of days'), array(30=>30,90=>90,365=>365,1=>'All'));
		$purge_date = $form->exportValue('purge_date');
        $form->display();

        if (!$purge_date==null){
            $del_date=strtotime("-".$purge_date." days",time());
            $sql_date=date('Y-m-d H:i:s',$del_date);
            # print ('<br/> date: '.$sql_date.'<br />');
            if ($purge_date==1) {
                $sql_query = 'Delete FROM base_login_audit';
                $ret = DB::Execute($sql_query);
                print ($this->lang->t('Entire log was be purged!'));
            } else {
                $sql_query = 'Delete FROM base_login_audit where start_time < \''.$sql_date.'\'';
                $ret = DB::Execute($sql_query);
                print ($this->lang->t('Records older than ').$purge_date.$this->lang->t(' days (').date('Y-m-d',$del_date).$this->lang->t(') were purged.'));
            }
        }

        Base_ActionBarCommon::add('back','Back',$this->create_back_href());
        Base_ActionBarCommon::add('delete','Purge Log File','href="javascript:void(0)" onClick="if(confirm(\''.Epesi::escapeJS($this->lang->t('Log will be purged!')).'\')){'.$form->get_submit_form_js().'}"');
        return true;
    }
}

?>
