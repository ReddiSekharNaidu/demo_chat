<?php
/* SVN FILE: $Id: app_controller.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
* Short description for file.
*
* This file is application-wide controller file. You can put all
* application-wide controller-related methods here.
*
* PHP versions 4 and 5
*
* CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
* Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @filesource
* @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
* @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
* @package       cake
* @subpackage    cake.cake.libs.controller
* @since         CakePHP(tm) v 0.2.9
* @version       $Revision: 7945 $
* @modifiedby    $LastChangedBy: gwoo $
* @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
* @license       http://www.opensource.org/licenses/mit-license.php The MIT License
*/
/**
* This is a placeholder class.
* Create the same file in app/app_controller.php
*
* Add your application-wide methods in the class below, your controllers
* will inherit them.
*
* @package       cake
* @subpackage    cake.cake.libs.controller
*/
App::import('Sanitize');
class AppController extends Controller {
    var $helpers = array('Javascript', 'Html', 'Form', 'Ajax', 'Session', 'Validanguage', 'Cache', 'Ekardahtml');
    var $components = array('RequestHandler', 'Auth', 'Session', 'EkardaUtility');
    public function constructClasses() {
        if (Configure::read("debug") >= 1) {
            $this->components[] = 'DebugKit.Toolbar';
        }
        parent::constructClasses();
    }
    function enforceSSL() {
        if (USE_SSL && (!$this->RequestHandler->isSSL() || env('SERVER_PORT') != 443)) {
            $whitelist = array(
                "processemails" => "cron_job",
                "accounts" => "monthly_reset_balance_credit_cron",
                "accounts" => "yearly_consumer_paid_balance_paid_cron",
                "accounts" => "renew_account",
                "myecard_recipients" => "bounce_email"
            );
            if (in_array($this->params['controller'], array_keys($whitelist)) && in_array($this->params['controller'], $whitelist)) return;
            $this->redirect("https://{$_SERVER['SERVER_NAME']}{$this->here}");
            exit;
        }
    }    /**
    * @Function : beforeFilter
    * @Param : None
    * @Return Value : True or False
    * @Purpose: This function call parent beforeFilter and Set Authentication Settings
    * @Author :
    * @Created Date:
    * @Modified Date:
    */
    function beforeFilter() {
        $this->enforceSSL();
        //ensure ajax pages continue to work during development
        if ($this->RequestHandler->isAjax())
            Configure::write('debug', 0);
        $this->Auth->allow('admin_verifysignup','view_online_ecard', 'card_varification_this', 'signup','connect', 'login', 'unsubscribe_ecard_contact', 'admin_login', 'superadmin_login', 'home', 'sign_up', 'forgotpassword', 'admin_forgotpassword', 'superadmin_forgotpassword', 'resetpassword', 'admin_resetpassword', 'superadmin_resetpassword', 'display', 'admin_virifysignup', 'cron_job', 'printinvoice', 'terms_and_conditions', 'cron_varify_code', 'sale_printinvoice', 'yearly_consumer_paid_balance_paid_cron', 'monthly_reset_balance_credit_cron', 'renew_account', 'admin_upload_contact_add', 'tenminite_fail_email_cron', 'bounce_email', 'bound_update_contact', 'autoLoginAdmin', 'set_ecard_status_open', 'phpmyadmin_info', 'cron_job', 'renew_account', 'iframeCardPreview');
        $this->Auth->fields = array('username'=>'email', 'password'=>'password');
        $this->Auth->loginAction = array('controller'=>'users', 'action'=>'login');
        $this->Auth->logoutRedirect = array('controller'=>'users', 'action'=>'dashboard');
        $this->Auth->logoutRedirect = array('controller'=>'users', 'action'=>'login');
        $this->Auth->logoutRedirect = array(Configure::read('Routing.admin')=>true, 'controller'=>'users', 'action'=>'login');
        $this->Auth->logoutRedirect = array(Configure::read('Routing.superadmin')=>true, 'controller'=>'users', 'action'=>'login');
        $this->Auth->loginError = 'Invalid credentials, please try again.';
        if ($this->Session->check('Auth.User.usertype')) {
            $this->checkRights();
        }
        //Added by ankita
        if ($this->Session->read('Auth.User.id') != '' && $this->Session->read('Auth.User.usertype') != 'superadmin') {
            $account_data = $this->getAccountInformation();
            $available_credits = $account_data['Account']['available_credits'];
            $balance_credit = $account_data['Account']['balance_credit'];
            $active_senders = $account_data['Account']['active_senders'];
            $total_senders = $account_data['Account']['total_senders'];
            $this->set("balance_credit", $balance_credit);
            $this->set("active_senders", $active_senders);
            $this->set("total_senders", $total_senders);
            $this->set("available_credits", $available_credits);
        }
        if (($this->params['controller'] == 'myecard_recipients' && $this->params['action'] == 'card_send') || ($this->params['controller'] == 'users' && $this->params['action'] == 'home') || ($this->params['controller'] == 'accounts' && $this->params['action'] == 'signup') || ($this->params['controller'] == 'users' && $this->params['action'] == 'admin_login') || ($this->params['controller'] == 'invoices' && $this->params['action'] == 'admin_shop') || ($this->params['controller'] == 'accounts' && $this->params['action'] == 'admin_upgrade') || ($this->params['controller'] == 'invoices' && $this->params['action'] == 'superadmin_generate_invoice' || ($this->params['controller'] == 'payment_details' && $this->params['action'] == 'admin_manage_payment_detail') || ($this->params['controller'] == 'payment_details' && $this->params['action'] == 'admin_edit_payment_detail'))) {
            $onchange_event = "showTax()";
            $this->set('onchange', $onchange_event);
            $arr = $this->EkardaUtility->getPaymentInfo();
            $this->set("month_array", $arr['month_array']);
            $this->set("card_type", $arr['card_type']);
            $this->set("year_array", $arr['year_array']);
            $this->set("curr_month", $arr['curr_month']);
            $this->set("curr_year", $arr['curr_year']);
            $this->set("curr_year_full", $arr['curr_year_full']);
            $this->set("selected_current_month", $arr['selected_current_month']);
            $this->set("selected_current_year", $arr['selected_current_year']);
            $this->set("selected_month", $arr['selected_month']);
            $this->set("selected_year", $arr['selected_year']);
            $messages = "<script>
            var ENTER_VALID_CREDIT_LIMIT='".ENTER_VALID_CREDIT_LIMIT."';
            var ENTER_VALID_CREDIT='".ENTER_VALID_CREDIT."';
            var ENTER_VALID_SENDER_LIMIT='".ENTER_VALID_SENDER_LIMIT."';
            var ENTER_VALID_SENDER='".ENTER_VALID_SENDER."';
            var TAX='".TAX."';
            var ENTER_CARD_NUMBER='".ENTER_CARD_NUMBER."';
            var ENTER_VALID_CARD_NUMBER='".ENTER_VALID_CARD_NUMBER."';
            var ENTER_NAME_ON_CARD='".ENTER_NAME_ON_CARD."';
            var ENTER_VALID_CARD_NUMBER='".ENTER_VALID_CARD_NUMBER."';
            var ENTER_VARIFICATION_NUMBER='".ENTER_VARIFICATION_NUMBER."';
            var ENTER_PAYMENT='".ENTER_PAYMENT."';
            var ENTER_VALID_EXPIREDATE='".ENTER_VALID_EXPIREDATE."';
            var ENTER_VERIFICATION_NUMBER='".ENTER_VERIFICATION_NUMBER."';
            var ENTER_VALID_VERIFICATION_NUMBER='".ENTER_VALID_VERIFICATION_NUMBER."';
            var SURE_TO_PAYMENT_CONTINUE='".SURE_TO_PAYMENT_CONTINUE."';
            var CONSUMER_PAID_SUBSCRIPTION_CHARGE='".CONSUMER_PAID_SUBSCRIPTION_CHARGE."';
            var ACCEPT_TERMS_CONDITION='".ACCEPT_TERMS_CONDITION."';
            </script>";
            $this->set("messages", $messages);
            if ($this->params['controller'] == 'myecard_recipients' && $this->params['action'] == 'card_send') {
                for ($i = 1; $i <= 31; $i++) {
                    $day_array[$i] = str_pad($i, 2, 0, STR_PAD_LEFT);
                }
                $this->set("day_array", $day_array);
                for ($i = 0; $i <= 59; $i++) {
                    $minute_array[$i] = str_pad($i, 2, 0, STR_PAD_LEFT);
                }
                $this->set("minute_array", $minute_array);
                for ($i = 0; $i <= 11; $i++) {
                    $hour_array[$i] = str_pad($i, 2, 0, STR_PAD_LEFT);
                }
                $this->set("hour_array", $hour_array);
                $this->set("am_pm_array", array("am"=>"AM", "pm"=>"PM"));
                $this->set("selected_hour", date("H") + 2);
                // set default selected hour after 2 hour of current hour
                $this->set("selected_am_pm", "am");
                $this->set("selected_minute", 0);
                $this->set("selected_day", date("d"));
                $this->set("myecardOffsetSignVar", "-");
                $this->set("myecardOffsetVar", "0");
                $this->set("myecardOffsetByVar", "Days");
            }
        }
    }
    /**
    * @Purpose       : This is common function used to fetch credit limit of current user
    * @Function      : getCreditLimit
    * @Param         :
    * @Return Value  : -
    * @Author        : Ankita
    * @Created Date  : 22-09-2011
    * @Modified Date :
    */
    function getAccountInformation() {
        $this->loadModel("Account");
        $this->Account->unbindModel(array('hasMany'=>array('AccountActivity')));
        $account_data = $this->Account->find("first", array("fields"=>array("balance_credit", "total_senders", "active_senders", "available_credits"), "conditions"=>array("Account.id"=>$this->Session->read("Auth.User.account_id"))));
        return $account_data;
    }
    /**
    * @Purpose       : This is common function used for check permission to access action
    * @Function      : checkRights
    * @Param         : user type
    * @Return Value  : -
    * @Author        : Deepesh Singh
    * @Created Date  : 27-07-2011
    * @Modified Date :
    */
    function checkRights() {
        $user_type                   = $this->Session->read('Auth.User.usertype');

        $admin_sender_common_array   = array('get_thumbnail','swift_mail_demo', 'saved_draft', 'select_cardtypes', 'select_ecarddesigns', 'card_send', 'recipients', 'subscribe_contacts', 'contacts_pagesearch', 'add_myecardscontacts', 'recipient_pagesearch', 'delete_myecardscontacts', 'cardmessage', 'card_preview', 'card_sent_sucess', 'edit_resend', 'card_send_schedule', 'unsubscribed', 'bounced', 'add_suppressionlist', 'activecontacts', 'subscribe', 'delete_contacts', 'edit', 'add', 'manage_contact_list', 'delete', 'add_manually_contact', 'select_custom_field', 'manage_custom_field', 'page_search', 'suppresion_page_search', 'suppression', 'delete_suppression', 'import_from_file', 'import_columnconf', 'sample_csv', 'unsubscribe_ecard', 'unsubscribe_ecard_contact', 'add_suppressionlist', 'delete_myecard_recipient', 'edit_myecard_recipient', 'search_myecardscontacts', 'search_recipients', 'send', 'select_carddesign', 'scheduled_cards_delete', 'select_recipients', 'add_manual_recipient', 'getstates', 'view_report', 'view_online_ecard', 'edit_custom_field', 'get_country_states', 'get_country_states', 'unsubscribe', 'add_logoes', 'set_ecard_status_open', 'edit_and_resend', 'logout', 'admin_logout', 'delete_myecardscontacts_for_manual', 'admin_download_report', 'scheduled_cards', 'admin_css', 'admin_test', 'admin_test1', 'contact_occasion', 'terms_and_conditions', 'recipient_pagesearch_for_manual', 'iframeCardPreview');
        $sender_controller_array     = array('users', 'contact_lists', 'contacts', 'myecard_recipients');
        $sender_action_array         = am(array('dashboard', 'login', 'sender_reports', 'view_report', 'edit_user_details', 'search_reports'), $admin_sender_common_array);

        $admin_controller_array      = array('users', 'accounts', 'contact_lists', 'contacts', 'myecard_recipients', 'card_types', 'card_designs', 'admin_design_setups', 'custom_card_design_requests', 'invoices', 'PaymentDetails', 'admin_admin_design_setups');
        $admin_action_array          = array('admin_dashboard', 'admin_home', 'index', 'admin_index', 'admin_add_logo', 'admin_update_automatic_card_adding', 'admin_savedesign', 'admin_delete_logo', 'admin_socialmedialinks', 'admin_ecardsetup', 'admin_update_size', 'edit_sender', 'validation_adminedit_sender_monthly', 'resendemail', 'delete_sender', 'admin_addmanualsender', 'admin_serch_sender', 'admin_sender', 'admin_addsender', 'admin_reports', 'admin_shop', 'admin_invoices', 'admin_edit_user_details', 'admin_logout', 'admin_manage_payment_detail', 'admin_custom_card_design_requests', 'superadmin_invoicepdf', 'admin_cacel_myaccount', 'admin_add_payment_detail', 'admin_index', 'getCardtypeName', 'searchecarddesign', 'add', 'add_suppressionlist', 'admin_upload_contact_add', 'admin_delete_sender', 'admin_edit_sender', 'admin_report_page_search', 'admin_view_report', 'admin_search_reports', 'admin_sender_reports', 'admin_delete', 'admin_upgrade', 'admin_cancel_custom_card_request', 'admin_generate_invoice', 'admin_delete_payment_detail', 'admin_edit_payment_detail', 'admin_gettitle', 'charge_card', 'admin_edit_custom_request', 'admin_login', 'admin_test');
        $admin_action_array          = am($admin_action_array, $admin_sender_common_array);

        $superadmin_controller_array = array('users', 'card_types', 'card_designs', 'card_templates', 'invoices', 'contents', 'faqs', 'debug_kit');
        $superadmin_action_array     = array('superadmin_dashboard', 'superadmin_index', 'superadmin_add', 'superadmin_edit', 'superadmin_delete', '_delete_image_or_file', 'carddesign_count_myecard', 'superadmin_view', 'superadmin_add_user', 'superadmin_user_accounts', 'superadmin_adduser_account', 'superadmin_getcountry', 'superadmin_edit_user', 'superadmin_login_report', 'superadmin_ecard_usage_report', 'superadmin_admin_user_edit', 'superadmin_reports', 'superadmin_ecard_usage_search_reports', 'superadmin_ecard_usage_page_search', 'superadmin_logout', 'getCardtypeName', 'getCardtemplateName', 'superadmin_download_report', 'superadmin_custom_card_design_requests', 'superadmin_invoicepdf', 'superadmin_admin_user_login', 'superadmin_admin_users', 'superadmin_admin_user_search', 'superadmin_admin_user_page_search', 'superadmin_edit_user_details', 'superadmin_login_search_reports', 'superadmin_login_user_page_search', 'superadmin_edituser', 'superadmin_editcustomcard', 'superadmin_deletecustomcard', 'superadmin_searchcardtype', 'superadmin_serch_customdesign_request', 'superadmin_customdesign_delete', 'superadmin_customdesign_edit', 'superadmin_customdesign_add', 'superadmin_custom_carddesign', 'superadmin_custom_carddesign_page_search', 'superadmin_custom_carddesign_search', 'superadmin_custom_carddesign_nav', 'superadmin_custom_carddesign_status', 'superadmin_edit_custom_carddesign', 'superadmin_custom_carddesign_download', 'superadmin_custom_carddesign_image_delete', 'superadmin_file_delete', 'superadmin_delete_custom_ecard', 'superadmin_listing', 'superadmin_generate_invoice', 'superadmin_invoice_report', 'superadmin_invoice_user_page_search', 'superadmin_invoice_search_reports', 'superadmin_downloadrport', 'superadmin_sales_report', 'superadmin_sales_search_reports', 'superadmin_sales_user_page_search', 'superadmin_delete_customcard', 'superadmin_custom_ecards', 'get_country_states', 'superadmin_admin_user_delete', 'set_ecard_status_open', 'phpmyadmin_info', 'iframeCardPreview');

        switch ($user_type) {
            case $user_type == 'admin' && !in_array($this->params['action'], $admin_controller_array) && !in_array($this->params['action'], $admin_action_array):
                $this->Session->setFlash('You can not access this url.'.$this->params['action']);
                $this->redirect('/admin/users/dashboard');
                break;
            case $user_type == 'superadmin' && !in_array($this->params['action'], $superadmin_action_array) && !in_array($this->params['controller'], $superadmin_controller_array):
                $this->Session->setFlash('You can not access this url.'.$this->params['action']);
                $this->redirect('/superadmin/users/dashboard');
                break;
            case $user_type == 'sender' && !in_array($this->params['action'], $sender_action_array) && !in_array($this->params['controller'], $sender_controller_array):
                $this->Session->setFlash('You can not access this url.'.$this->params['action']);
                $this->redirect('/users/dashboard');
                break;
        }
        return true;
    }
    /**
    * @Purpose       : This function is used for set layout
    * @Function      : _set_defaults
    * @Param         : layout
    * @Return Value  : -
    * @Author        : Deepesh Singh
    * @Created Date  : 27-07-2011
    * @Modified Date :
    */
    function _set_defaults($layoutname = '') {
        $this->layout = $layoutname;
    }
    // End of Function
    /**
    * @Function : _get_carddesign_listing
    * @Param :
    * @Return Value : array of card design
    * @Purpose: This function is return card design list based on conditions.
    */
    function _get_carddesign_listing($carddesign_search_data = array(), $paging = NULL, $orderBy = NULL, $carddesign_data = array()) {
        // Set Condition
        $conditions = array();
        $joins = array();
        $this->loadModel('CardDesignsTag');
        $this->CardDesign->bindModel(array('hasMany'=>array('CardDesignsTag'=>array('foreignKey'=>'card_design_id'))), false);
        $joins[] = array('table'=>'card_designs_tags', 'foreignKey'=>'card_design_id', 'type'=>'LEFT', 'conditions'=>array('CardDesignsTag.card_design_id = CardDesign.id'), 'alias'=>'CardDesignsTag',);
        $fields = array('CardDesign.id', 'CardDesign.user_id', 'CardDesign.title', 'CardDesign.imgpath', 'CardDesign.account_id','CardDesign.card_type_id',
                                'CardDesign.card_template_id','CardDesign.motif','CardDesign.motif_config', 'CardDesign.created', 'CardDesign.modified',
                                'CardDesign.is_premium', 'CardDesign.is_custom_design', 'CardDesign.is_additional_greeting', 'CardDesign.status','CardDesign.length_config',
                                'CardDesignsTag.card_design_id', 'CardDesignsTag.tag_name');
        // Search data - Check if previously set in session
        if (!isset($carddesign_search_data['CardDesign']) || empty($carddesign_search_data['CardDesign'])) {
            if ($this->Session->check('carddesign_search_data')) {
                $carddesign_search_data = $this->Session->read('carddesign_search_data');
            }
        }
        if (!isset($carddesign_data) || empty($carddesign_search_data)) {
            if ($this->Session->check('carddesign_data')) {
                $carddesign_data = $this->Session->read('carddesign_data');
            }
        }
        //condition check
        if (isset($carddesign_search_data['User']['serchemail']) && $carddesign_search_data['User']['serchemail'] != '') {
            $this->set('serchemail', $carddesign_search_data['User']['serchemail']);
        }
        if (isset($carddesign_search_data['CardDesign']['card_type_id']) && $carddesign_search_data['CardDesign']['card_type_id'] != '') {
            $conditions = am($conditions, array('CardDesign.card_type_id'=>$carddesign_search_data['CardDesign']['card_type_id']));
            $this->set('defaultSel', $carddesign_search_data['CardDesign']['card_type_id']);
            $this->Session->write('defaultSel', $carddesign_search_data['CardDesign']['card_type_id']);
        }
        if (isset($carddesign_search_data['CardDesign']['user_id']) && $carddesign_search_data['CardDesign']['user_id'] != '') {
            $conditions = am($conditions, array('CardDesign.user_id'=>$carddesign_search_data['CardDesign']['user_id']));
        }
        if (isset($carddesign_search_data['CardDesign']['account_id']) && $carddesign_search_data['CardDesign']['account_id'] != '') {
            $conditions = am($conditions, array('CardDesign.account_id'=>$carddesign_search_data['CardDesign']['account_id']));
        }
        if (isset($carddesign_search_data['CardDesign']['is_custom_design']) && $carddesign_search_data['CardDesign']['is_custom_design'] != '') {
            $conditions = am($conditions, array('CardDesign.is_custom_design'=>$carddesign_search_data['CardDesign']['is_custom_design']));
        }
        if (isset($carddesign_data) && !empty($carddesign_data)) {
            if (isset($carddesign_data['CardDesign']['is_custom_design']) && $carddesign_data['CardDesign']['is_custom_design'] == 0 || $carddesign_data['CardDesign']['is_custom_design'] == 1) {
                $conditions = am($conditions, array('OR'=>array('CardDesign.is_custom_design'=>$carddesign_data['CardDesign']['is_custom_design'])));
            }
            if (isset($carddesign_data['CardDesign']['is_premium']) && $carddesign_data['CardDesign']['is_premium'] == 0) {
                $conditions = am($conditions, array('CardDesign.is_premium'=>$carddesign_data['CardDesign']['is_premium']));
            }
            if (isset($carddesign_data['User']['id']) && !empty($carddesign_data['User']['id'])) {
                $conditions['OR'] = am($conditions['OR'], array('CardDesign.user_id'=>$carddesign_data['User']['id']));
            }
        }
        // Set Order By
        if ($orderBy == NULL) {
            $orderBy = 'CardDesign.id DESC';
        }
        if ($paging != NULL && $paging == 'yes') {
            $pgLimit = PAGING_LIMIT;
            if ($this->RequestHandler->isXml() && !empty($this->params['url']['n'])) {
                $pgLimit = $this->params['url']['n'];
            }
            $this->paginate = array('limit'=>$pgLimit, 'joins'=>$joins, 'conditions'=>$conditions, 'order'=>$orderBy,'fields'=>$fields);
            $carddesigns = $this->paginate('CardDesign');
        } else {
            $carddesigns = $this->CardDesign->find('all', array('joins'=>$joins, 'conditions'=>$conditions, 'order'=>$orderBy,'fields'=>$fields));
        }
        $this->Session->write('carddesign_search_data', $carddesign_search_data);
        $this->Session->write('carddesign_data', $carddesign_data);
        return $carddesigns;
    }
    //end function
    /**
    * @Function : _redirectLocation
    * @Param : controllername,actionname
    * @Return Value : Redirect page to new location
    * @Purpose: This function will take controllername and action name as argument and will redirect page accordingly.
    * @Author : chandresh dashora
    * @Created Date: 25/06/11
    * @Modified Date:
    */
    function _redirectLocation($controllername, $actionname, $id = NULL) {
        $this->redirect(array('controller'=>$controllername, 'action'=>$actionname, $id));
    }
    /**
    * @Function : _getCardPrivewContent
    * @Purpose: This function return ecard message for template content.
    * @Author : Ankita
    */
    function _getCardPrivewContent($myecard_id, $recepient_id = '', $recepient_name = '', $recepient_email = '', $hash_code = '') {
        //helpers we need
        App::import('Helper', 'Html'); App::import('Helper', 'Ekardahtml'); $this->Html = new EkardahtmlHelper();
        //components we need
        App::import('Component', 'Hashcrypt'); $this->Crypt = new HashcryptComponent("secret");
        //models we need
        $this->loadModel("Account");
        $this->loadModel("CardDesign");
        $this->loadModel("AdminDesignSetup");
        $this->loadModel('CardTemplate');

        //Read sender account info
        $accout_data = $this->Account->find("first", array(
            "fields" => array(
                "company",
                "twitter_link",
                "facebook_link"
            ),
            "conditions" => array(
                "Account.id" => $this->Session->read("Auth.User.account_id")
            )
        ));
        $sender_company      = (isset($accout_data['Account']['company']) ? $accout_data['Account']['company'] : "");
        $resp                = $this->Myecard->find('first', array('conditions'=>array('Myecard.id'=>$myecard_id)));
        $resp_card_design    = $this->CardDesign->find('list', array(
                                    'fields' => array(
                                        'is_additional_greeting
                                    '),
                                    'conditions' => array(
                                        'CardDesign.id' => $resp['Myecard']['card_design_id']
                                    )
                               ));
        $additional_greeting = '';
        if (isset($resp_card_design[$resp['Myecard']['card_design_id']]) && $resp_card_design[$resp['Myecard']['card_design_id']] == 1) {
            $additional_greeting = $resp['Myecard']['additional_greetings'];
        }
        //View online
        $recipient_name = $recipient_email = $view_online = "";
        if ($recepient_id != '' && $recepient_id != '0') {
            $view_online     = $this->Html->url(array('admin' => false, 'controller' => 'myecard_recipients', 'action' => 'view_online_ecard', base64_encode($recepient_id)));
            $recipient_name  = $recepient_name;
            $recipient_email = $recepient_email;
        }
        $social_media = "";
        if ($accout_data['Account']['twitter_link'] != '0') {
            $social_media .= "<a class='preview-social-media-link' target='_blank' href='http://twitter.com/intent/tweet?original_referer=&text=".$resp['Myecard']['subject']."&url=".$view_online."'><img title='Twitter' alt='Twitter ' src='".BROADCASTS_URL."/Enteract/downloads/twitter.gif'></a>";
        }
        if ($accout_data['Account']['facebook_link'] != '0') {
            $social_media .= "<a class='preview-social-media-link' target='_blank' href='http://www.facebook.com/sharer.php?u=".$view_online."&t=".$resp['Myecard']['subject']."'><img title='Facebook' alt='facebook' src='".BROADCASTS_URL."/Enteract/downloads/facebook.gif'></a>";
        }
        //Fetch Footer
        $footer = "";
        $this->AdminDesignSetup->unbindModel(array("hasMany"=>array("AdminSelectedDesign")));
        $footer_data = $this->AdminDesignSetup->find("first", array(
            "fields" => array(
                "isfooter",
                "footer_content"
            ),
            "conditions" => array(
                "AdminDesignSetup.account_id" => $this->Session->read("Auth.User.account_id"),
                "AdminDesignSetup.user_id" => $this->Session->read("Auth.User.id"),
                "card_type_id" => $resp['Myecard']['card_type_id']
            )
        ));
        $footer_html = '';
        if (count($footer_data) > 0) {
            if ($footer_data['AdminDesignSetup']['isfooter'] == 1) {
                $footer_html = $footer_data['AdminDesignSetup']['footer_content'];
            }
        }
        $card_name      = $resp['Myecard']['card_name'];
        $sender_name    = $resp['Myecard']['sender_name'];
        $sender_email   = $resp['Myecard']['reply_to_email'];
        $subject        = $resp['Myecard']['subject'];
        $card_type_id   = $resp['Myecard']['card_type_id'];
        $card_design_id = $resp['Myecard']['card_design_id'];
        $salutation     = $resp['Myecard']['salutation'];
        $signature      = $resp['Myecard']['signature'];
        $card_message   = $resp['Myecard']['card_message'];
        $cardtype_name  = $this->EkardaUtility->_get_field_value('CardType', 'title', $card_type_id);
        $cardtype_img   = $this->Html->image(MYECARD_CARD_TYPE_IMAGE_PATH.$this->EkardaUtility->_get_field_value('CardType', 'imgpath', $card_type_id), array('fullURL' => true));
        $carddesign_img = $this->Html->image(MYECARD_CARD_DESIGN_IMAGE_PATH.$this->EkardaUtility->_get_field_value('CardDesign', 'imgpath', $card_design_id), array('width' => 700, 'height' => 100, 'fullURL' => true));
        $logo_img = '';
        if ($resp['Myecard']['logo_id'] != '' && $resp['Myecard']['logo_id'] != '0') {
            $logo_img_size = $this->EkardaUtility->_get_field_value('Logo', 'logo_size', $resp['Myecard']['logo_id']);
            $logo_name = $this->EkardaUtility->_get_field_value('Logo', 'logo_path', $resp['Myecard']['logo_id']);

            if ($logo_name != '' && file_exists(WWW_ROOT."/img/" . MYECARD_LOGO_IMAGE_PATH . "{$logo_img_size}/{$logo_name}")) {
                $logo_img_path = MYECARD_LOGO_IMAGE_PATH . "{$logo_img_size}/{$logo_name}";
                $logo_img = $this->Html->image($logo_img_path, array('fullURL' => true));
            }
        }
        $card_template_id = $this->EkardaUtility->_get_field_value('CardDesign', 'card_template_id', $resp['Myecard']['card_design_id']);
        $template = $this->CardTemplate->find('first', array(
            'fields' => array(
                'CardTemplate.type',
                'CardTemplate.html_content',
                'CardTemplate.text_content'
            ),
            'conditions' => array(
                'CardTemplate.id' => $card_template_id
            )
        ));

        $template_content = array(
            'html' => $template['CardTemplate']['html_content'],
            'text' => $template['CardTemplate']['text_content']
        );
        //$template_content;
        //dont believe this is being used
        $this->set('cardtype_name', $cardtype_name);
        $this->set('cardtype_img', $cardtype_img);
        $this->set('carddesign_img', $carddesign_img);
        $this->set('template_content', $template_content);

        $image_preview_url = $this->Html->url(array('controller'=>'entdev', 'action'=>'preview', $card_design_id), true);
        $card_render_data = serialize(array(
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'subject' => $subject,
            'additional_greetings' => $additional_greeting,
            'salutation' => $salutation,
            'card_message' => $card_message,
            'signature' => $signature
        ));
        $image_preview_url .= "?d=".rawurlencode($this->Crypt->encrypt($card_render_data));

        foreach ($template_content as $key => $value) {
            $template_content[$key] = str_replace('{sender-name}',         $sender_name,         $template_content[$key]);
            $template_content[$key] = str_replace('{sender-email}',        $sender_email,        $template_content[$key]);
            $template_content[$key] = str_replace('{recipient-name}',      $recipient_name,      $template_content[$key]);
            $template_content[$key] = str_replace('{recipient-email}',     $recipient_email,     $template_content[$key]);
            $template_content[$key] = str_replace('{subject}',             $subject,             $template_content[$key]);
            $template_content[$key] = str_replace('{logo}',                $logo_img,            $template_content[$key]);
            $template_content[$key] = str_replace('{card-type}',           $cardtype_name,       $template_content[$key]);
            $template_content[$key] = str_replace('{card-design}',         $carddesign_img,      $template_content[$key]);
            $template_content[$key] = str_replace('{salutation}',          $salutation,          $template_content[$key]);
            $template_content[$key] = str_replace('{signature}',           $signature,           $template_content[$key]);
            $template_content[$key] = str_replace('{card-message}',        $card_message,        $template_content[$key]);
            $template_content[$key] = str_replace('{additional-greeting}', $additional_greeting, $template_content[$key]);
            $template_content[$key] = str_replace('{sender-company}',      $sender_company,      $template_content[$key]);
            $template_content[$key] = str_replace('{footer-html}',         $footer_html,         $template_content[$key]);
            $template_content[$key] = str_replace('{recipient-ID}',        $hash_code,           $template_content[$key]);
            $template_content[$key] = str_replace('{view-online}',         $view_online,         $template_content[$key]);
            $template_content[$key] = str_replace('{socialmedia}',         $social_media,        $template_content[$key]);
            $template_content[$key] = str_replace('{footer}',              $footer,              $template_content[$key]);
            $template_content[$key] = str_replace('{unsubscribe}',         '#',                  $template_content[$key]);
            $template_content[$key] = str_replace('{ecard_image_url}',     $image_preview_url,   $template_content[$key]);
        }

        return $template_content;
    }
    function _getCardFinalContent($myecard_id, $recepient_id = '', $recepient_name = '', $recepient_email = '', $hash_code = '') {
        App::import('Helper', 'Html'); App::import('Helper', 'Ekardahtml'); $this->Html = new EkardahtmlHelper();
        $this->loadModel("Account");
        $this->loadModel("CardDesign");
        $this->loadModel("AdminDesignSetup");
        $this->loadModel('CardTemplate');
        //Read sender account info
        $accout_data = $this->Account->find("first", array("fields"=>array("company", "twitter_link", "facebook_link"), "conditions"=>array("Account.id"=>$this->Session->read("Auth.User.account_id"))));
        $sender_company = '';
        $sender_company = (isset($accout_data['Account']['company']) ? $accout_data['Account']['company'] : "");

        $resp = $this->Myecard->find('first', array('conditions'=>array('Myecard.id'=>$myecard_id)));
        $resp_card_design = $this->CardDesign->find('list', array('fields'=>array('is_additional_greeting'), 'conditions'=>array('CardDesign.id'=>$resp['Myecard']['card_design_id'])));

        $additional_greeting = '';
        if (isset($resp_card_design[$resp['Myecard']['card_design_id']]) && $resp_card_design[$resp['Myecard']['card_design_id']] == 1) {
            $additional_greeting = $resp['Myecard']['additional_greetings'];
        }
        //View online
        $recipient_name = $recipient_email = $view_online = "";
        if ($recepient_id != '' && $recepient_id != '0') {
            $view_online     = $this->Html->url(array('controller' => 'myecard_recipients', 'action' => 'view_online_ecard', base64_encode($recepient_id)), true);
            $recipient_name  = $recepient_name;
            $recipient_email = $recepient_email;
        }

        $social_media = "";
        if ($accout_data['Account']['twitter_link'] != '0') {
            $twitter_img_url = $this->Html->url('/img/twitter.gif', true);
            $social_media .= "<a target='_blank' href='http://twitter.com/intent/tweet?original_referer=&text=".$resp['Myecard']['subject']."&url=".$view_online."'><img title='Twitter' alt='Twitter' src='".BROADCASTS_URL."/Enteract/downloads/twitter.gif'></a>";
        }
        if ($accout_data['Account']['facebook_link'] != '0') {
            $facebook_img_url = $this->Html->url('/img/facebook.gif', true);
            $social_media .= "<a target='_blank' href='http://www.facebook.com/sharer.php?u=".$view_online."&t=".$resp['Myecard']['subject']."'><img title='Facebook' alt='facebook' src='".BROADCASTS_URL."/Enteract/downloads/facebook.gif'></a>";
        }

        $footer = "";
        //Fetch Footer
        $this->AdminDesignSetup->unbindModel(array("hasMany" => array("AdminSelectedDesign")));
        $footer_data = $this->AdminDesignSetup->find("first", array(
            "fields" => array(
                "isfooter",
                "footer_content"
            ),
            "conditions" => array(
                "AdminDesignSetup.account_id" => $this->Session->read("Auth.User.account_id"),
                "AdminDesignSetup.user_id" => $this->Session->read("Auth.User.id"),
                "card_type_id" => $resp['Myecard']['card_type_id']
            )
        ));
        $footer_html = '';
        if (count($footer_data) > 0) {
            if ($footer_data['AdminDesignSetup']['isfooter'] == 1) {
                $footer_html = $footer_data['AdminDesignSetup']['footer_content'];
            }
        }
        $card_name           = $resp['Myecard']['card_name'];
        $sender_name         = $resp['Myecard']['sender_name'];
        $sender_email        = $resp['Myecard']['reply_to_email'];
        $subject             = $resp['Myecard']['subject'];
        $card_type_id        = $resp['Myecard']['card_type_id'];
        $card_design_id      = $resp['Myecard']['card_design_id'];
        $salutation          = $resp['Myecard']['salutation'];
        $signature           = $resp['Myecard']['signature'];
        $card_message        = $resp['Myecard']['card_message'];
        $cardtype_name       = $this->EkardaUtility->_get_field_value('CardType', 'title', $card_type_id);
        $cardtype_img        = $this->Html->image(MYECARD_CARD_TYPE_IMAGE_PATH.$this->EkardaUtility->_get_field_value('CardType', 'imgpath', $card_type_id));
        $carddesign_img_path = MYECARD_CARD_DESIGN_IMAGE_PATH.$this->EkardaUtility->_get_field_value('CardDesign', 'imgpath', $card_design_id);
        $carddesign_img      = $this->Html->image($carddesign_img_path, array('width' => 700, 'height' => 100));

        $logo_img = '';
        if ($resp['Myecard']['logo_id'] != '' && $resp['Myecard']['logo_id'] != '0') {
            $logo_img_size = $this->EkardaUtility->_get_field_value('Logo', 'logo_size', $resp['Myecard']['logo_id']);
            $logo_name = $this->EkardaUtility->_get_field_value('Logo', 'logo_path', $resp['Myecard']['logo_id']);

            if ($logo_name != '' && file_exists(WWW_ROOT."/img/" . MYECARD_LOGO_IMAGE_PATH . "{$logo_img_size}/{$logo_name}")) {
                $logo_img_path = MYECARD_LOGO_IMAGE_PATH . "{$logo_img_size}/{$logo_name}";
                $logo_img = $this->Html->image($logo_img_path, array('fullURL' => true));
            }
        }

        $card_template_id = $this->EkardaUtility->_get_field_value('CardDesign', 'card_template_id', $resp['Myecard']['card_design_id']);
        $template = $this->CardTemplate->find('first', array(
            'fields' => array(
                'CardTemplate.type',
                'CardTemplate.html_content',
                'CardTemplate.text_content'
            ),
            'conditions' => array(
                'CardTemplate.id' => $card_template_id
            )
        ));
        $template_content = array(
            'html' => $template['CardTemplate']['html_content'],
            'text' => $template['CardTemplate']['text_content']
        );
        $this->set('cardtype_name', $cardtype_name);
        $this->set('cardtype_img', $cardtype_img);
        $this->set('carddesign_img', $carddesign_img);
        $this->set('template_content', $template_content);

        $image_preview_url = $this->Html->url(array('controller'=>'entdev', 'action'=>'generateImageByHash', $hash_code), true);

        foreach ($template_content as $key => $value) {
            $template_content[$key] = str_replace('{sender-name}',         $sender_name,         $template_content[$key]);
            $template_content[$key] = str_replace('{sender-email}',        $sender_email,        $template_content[$key]);
            $template_content[$key] = str_replace('{recipient-name}',      $recipient_name,      $template_content[$key]);
            $template_content[$key] = str_replace('{recipient-email}',     $recipient_email,     $template_content[$key]);
            $template_content[$key] = str_replace('{subject}',             $subject,             $template_content[$key]);
            $template_content[$key] = str_replace('{logo}',                $logo_img,            $template_content[$key]);
            $template_content[$key] = str_replace('{card-type}',           $cardtype_name,       $template_content[$key]);
            $template_content[$key] = str_replace('{card-design}',         $carddesign_img,      $template_content[$key]);
            $template_content[$key] = str_replace('{salutation}',          $salutation,          $template_content[$key]);
            $template_content[$key] = str_replace('{signature}',           $signature,           $template_content[$key]);
            $template_content[$key] = str_replace('{card-message}',        $card_message,        $template_content[$key]);
            $template_content[$key] = str_replace('{additional-greeting}', $additional_greeting, $template_content[$key]);
            $template_content[$key] = str_replace('{sender-company}',      $sender_company,      $template_content[$key]);
            $template_content[$key] = str_replace('{footer-html}',         $footer_html,         $template_content[$key]);
            $template_content[$key] = str_replace('{recipient-ID}',        $hash_code,           $template_content[$key]);
            $template_content[$key] = str_replace('{view-online}',         $view_online,         $template_content[$key]);
            $template_content[$key] = str_replace('{socialmedia}',         $social_media,        $template_content[$key]);
            $template_content[$key] = str_replace('{footer}',              $footer,              $template_content[$key]);
            $template_content[$key] = str_replace('{ecard_image_url}',     $image_preview_url,   $template_content[$key]);
        }

        return $template_content;
    }
    /**
    * @Function : send_invoice
    * @Purpose: This function use for payment process of invoice
    * @Author : Ankita
    */
    function send_invoice($type = '', $tax_amount = '', $country_id = '', $user_id = '', $account_id = '', $first_name = '', $last_name = '', $email = '') {
        if ($type == 'registration') {
            $this->data['Invoice']['total_custom_amt'] = 0;
            $this->data['Invoice']['total_credit_amt'] = 0;
            $this->data['Invoice']['total_sender_amt'] = 0;
            $this->data['Invoice']['credit'] = 0;
            $amt = CONSUMER_PAID_SUBSCRIPTION_CHARGE;
            $total_amt = $amt + $tax_amount;
            $RequestType = 'Periodic';
        } elseif ($type == 'create_invoice') {
            $amt = $this->data['Invoice']['total_custom_amt'] + $this->data['Invoice']['total_credit_amt'] + $this->data['Invoice']['total_sender_amt'];
            $tax_amount = $this->EkardaUtility->count_tax($amt, $user_id);
            $total_amt = $amt + $tax_amount;
            $RequestType = 'create_invoice';
        } else {
            $amt = $this->data['Invoice']['total_custom_amt'] + $this->data['Invoice']['total_credit_amt'] + $this->data['Invoice']['total_sender_amt'];
            $tax_amount = $this->EkardaUtility->count_tax($amt, $this->Session->read("Auth.User.id"));
            $total_amt = $amt + $tax_amount;
            $user_id = $this->Session->read("Auth.User.id");
            $RequestType = 'Payment';
            $this->data['Invoice']['custom_cards_discount']        = '0';
            $this->data['Invoice']['custom_cards_discount_reason'] = '';
            $this->data['Invoice']['card_credits_discount']        = '0';
            $this->data['Invoice']['card_credits_discount_reason'] = '';
            $this->data['Invoice']['senders_discount']             = '0';
            $this->data['Invoice']['senders_discount_reason']      = '';
        }
        $payment_obj = $this->PaymentDetail->find('first', array(
            'conditions' => array(
                'PaymentDetail.user_id' => $user_id,
            )
        ));
        if (substr_count($this->data['Invoice']['card_number'], '*') != 0) {
            if(isset($payment_obj) && !empty($payment_obj)) {
                $crypt = $this->Hashcrypt;
                $this->data['Invoice']['card_number'] = $crypt->decrypt($payment_obj['PaymentDetail']['card_number']);
            }
        }

        $invoice = $this->data['Invoice'];
        $invoice_number = $this->EkardaUtility->str_rand(10, 'numeric');
        if ($country_id == '13')
            $currency = 'AUD';
        else
            $currency = 'USD';

        if ($type != 'create_invoice') {
            $decimal_total_amt = ceil($total_amt);
            $paymentInfo = array("CreditCard"=>array('ccnum'=>$invoice['card_number'], 'ccexpmonth'=>$invoice['month'], 'ccexpyear'=>substr($invoice['year'], 2), 'cc_cvv'=>$invoice['card_verification'], 'amount'=>$decimal_total_amt, 'invoice_number'=>$invoice_number, 'currency'=>$currency, 'RequestType'=>$RequestType));
            $response = $this->Paymentprocess->chargeCard($paymentInfo);
            $i = 0;
            $this->data['Invoice']['account_id'] = $this->Session->read("Auth.User.account_id");
            $this->data['Invoice']['invoice_number'] = $invoice_number;
            $this->data['Invoice']['error_description'] = '';
            $this->data['Invoice']['error_code'] = '';
            if ($RequestType == 'Payment') {
                $this->data['Invoice']['error_description'] = $response['SecurePayMessage']['Payment']['TxnList']['Txn']['responseCode'];
                $this->data['Invoice']['error_code'] = $response['SecurePayMessage']['Payment']['TxnList']['Txn']['responseText'];
                $this->data['Invoice']['transaction_no'] = $response['SecurePayMessage']['Payment']['TxnList']['Txn']['txnID'];
                if ($response['SecurePayMessage']['Payment']['TxnList']['Txn']['approved'] == 'Yes') {
                    $status = "Approved";
                    $this->Session->setFlash('You have successfully completed payment', true);
                } else {
                    $status = "Error : ".$response['SecurePayMessage']['Payment']['TxnList']['Txn']['responseText'];
                    $this->Session->setFlash("Error : ".$response['SecurePayMessage']['Payment']['TxnList']['Txn']['responseText'].".  Please try again.", true);
                    return 0;
                }
            } elseif ($RequestType == 'Periodic') {
                $this->data['Invoice']['transaction_no'] = $response['SecurePayMessage']['Periodic']['PeriodicList']['PeriodicItem']['clientID'];
                if (strtolower($response['SecurePayMessage']['Periodic']['PeriodicList']['PeriodicItem']['successful']) == 'yes' && $response['SecurePayMessage']['Status']['statusCode'] == 0) {
                    $this->data['Invoice']['client_id'] = $response['SecurePayMessage']['Periodic']['PeriodicList']['PeriodicItem']['clientID'];
                    $status = "Approved";
                } else {
                    $status = "Error : ".$response['SecurePayMessage']['Status']['statusDescription'];
                    return 0;
                }
            }
            $this->data['Invoice']['total_amount'] = $total_amt;
            $this->data['Invoice']['method_type'] = 'paymentgateway';
            $this->data['Invoice']['payment_remark'] = $status;
            $this->data['Invoice']['user_id'] = $this->Session->read("Auth.User.id");
            $this->data['Invoice']['status'] = 'paid';
        } else {
            $this->data['Invoice']['account_id'] = $account_id;
            $this->data['Invoice']['invoice_number'] = $invoice_number;
            $this->data['Invoice']['total_amount'] = $total_amt;
            $this->data['Invoice']['method_type'] = 'Manual';
            $this->data['Invoice']['payment_remark'] = "Invoice generated by superadmin";
            $this->data['Invoice']['user_id'] = $user_id;
            $this->data['Invoice']['status'] = 'paid';
        }
        if ($type != "registration") {
            $save = $this->Invoice->save($this->data);
            if ($save >= 0) {
                //Insert into invoice detail
                $last_insert_id = $this->Invoice->getLastInsertId();
                $counter = 0;
                if ($this->data['Invoice']['total_custom_amt'] > 0) {
                    //Update custom filed
                    $custom_card_ids_array = explode(",", $this->data['Invoice']['custom_card_id']);
                    $this->loadModel("CustomCardDesignRequest");
                    $total_custom_card_count = 0;
                    $custom_card_id_array = array();
                    if ($type != 'create_invoice') {
                        for ($i = 0; $i < count($custom_card_ids_array); $i++) {
                            $update_condition['CustomCardDesignRequest.status'] = "'paid'";
                            $result_ecard = $this->CustomCardDesignRequest->updateAll($update_condition, array('CustomCardDesignRequest.id ='=>$custom_card_ids_array[$i]));
                            $total_custom_card_count++;
                            $custom_card_id_array[] = $custom_card_ids_array[$i];
                        }
                    } else {
                        for ($i = 0; $i < count($custom_card_ids_array); $i++) {
                            if ($this->data['Invoice']['include'.$custom_card_ids_array[$i]] == 1) {
                                $this->data['Invoice']['include'.$custom_card_ids_array[$i]];
                                $update_condition['CustomCardDesignRequest.status'] = "'paid'";
                                $result_ecard = $this->CustomCardDesignRequest->updateAll($update_condition, array('CustomCardDesignRequest.id ='=>$custom_card_ids_array[$i]));
                                $total_custom_card_count++;
                                $custom_card_id_array[] = $custom_card_ids_array[$i];
                            }
                        }
                    }
                    $custom_card_name_array = $this->CustomCardDesignRequest->find("list", array("conditions"=>array("CustomCardDesignRequest.id"=>$custom_card_id_array), "fields"=>array("title", "title")));
                    $custom_card_names = implode(",", $custom_card_name_array);
                    $detail_data['InvoiceData'][$counter]['invoice_id'] = $last_insert_id;
                    $detail_data['InvoiceData'][$counter]['amount'] = $this->data['Invoice']['total_custom_amt'];
                    $detail_data['InvoiceData'][$counter]['actual_amount'] = (isset($this->data['Invoice']['total_orignal_custom_amt']) ? $this->data['Invoice']['total_orignal_custom_amt'] : 0);
                    $description = $total_custom_card_count.' Custom eKard payment ('.$custom_card_names.')';
                    if ($this->data['Invoice']['custom_cards_discount'] > 0) {
                        $description .= "<br>Got ".$this->data['Invoice']['custom_cards_discount']."% discount";
                        if ($this->data['Invoice']['custom_cards_discount_reason'] != '') {
                            $description .= "( ".$this->data['Invoice']['custom_cards_discount_reason']." )";
                        }
                    }
                    $detail_data['InvoiceData'][$counter]['description'] = $description;
                    $detail_data['InvoiceData'][$counter]['type'] = 'credit';
                    $detail_data['InvoiceData'][$counter]['quantity'] = $total_custom_card_count;
                    $detail_data['InvoiceData'][$counter]['discount'] = $this->data['Invoice']['custom_cards_discount'];
                    $detail_data['InvoiceData'][$counter]['discount_reason'] = $this->data['Invoice']['custom_cards_discount_reason'];
                    $total_payments_list_for_mail['Custom eKard payment'] = $this->data['Invoice']['total_custom_amt'];
                    $counter++;
                }
                if ($this->data['Invoice']['total_credit_amt'] > 0) {
                    $detail_data['InvoiceData'][$counter]['invoice_id'] = $last_insert_id;
                    $detail_data['InvoiceData'][$counter]['amount'] = $this->data['Invoice']['total_credit_amt'];
                    $detail_data['InvoiceData'][$counter]['actual_amount'] = (isset($this->data['Invoice']['total_orignal_credit_amt']) ? $this->data['Invoice']['total_orignal_credit_amt'] : 0);
                    $description = $this->data['Invoice']['credit'].' Credit purchase payment';
                    if ($this->data['Invoice']['card_credits_discount'] > 0) {
                        $description .= "<br>Got ".$this->data['Invoice']['card_credits_discount']."% discount";
                        if ($this->data['Invoice']['card_credits_discount_reason'] != '') {
                            $description .= "( ".$this->data['Invoice']['card_credits_discount_reason']." )";
                        }
                    }
                    $detail_data['InvoiceData'][$counter]['description'] = $description;
                    $detail_data['InvoiceData'][$counter]['type'] = 'credit';
                    $detail_data['InvoiceData'][$counter]['quantity'] = $this->data['Invoice']['credit'];
                    $detail_data['InvoiceData'][$counter]['discount'] = $this->data['Invoice']['card_credits_discount'];
                    $detail_data['InvoiceData'][$counter]['discount_reason'] = $this->data['Invoice']['card_credits_discount_reason'];
                    $counter++;
                    $total_payments_list_for_mail['Credit purchase payment'] = $this->data['Invoice']['total_credit_amt'];
                }
                if ($this->data['Invoice']['total_sender_amt'] > 0) {
                    $detail_data['InvoiceData'][$counter]['invoice_id'] = $last_insert_id;
                    $detail_data['InvoiceData'][$counter]['amount'] = $this->data['Invoice']['total_sender_amt'];
                    $detail_data['InvoiceData'][$counter]['actual_amount'] = (isset($this->data['Invoice']['total_orignal_sender_amt']) ? $this->data['Invoice']['total_orignal_credit_amt'] : 0);
                    $description = $this->data['Invoice']['sender'].' Sender purchase payment';
                    if ($this->data['Invoice']['senders_discount'] > 0) {
                        $description .= "<br>Got ".$this->data['Invoice']['senders_discount']."% discount";
                        if ($this->data['Invoice']['senders_discount_reason'] != '') {
                            $description .= "( ".$this->data['Invoice']['senders_discount_reason']." )";
                        }
                    }
                    $detail_data['InvoiceData'][$counter]['description'] = $description;
                    $detail_data['InvoiceData'][$counter]['type'] = 'credit';
                    $detail_data['InvoiceData'][$counter]['quantity'] = $this->data['Invoice']['sender'];
                    $detail_data['InvoiceData'][$counter]['discount'] = $this->data['Invoice']['senders_discount'];
                    $detail_data['InvoiceData'][$counter]['discount_reason'] = $this->data['Invoice']['senders_discount_reason'];
                    $total_payments_list_for_mail['Sender purchase payment'] = $this->data['Invoice']['total_sender_amt'];
                    $counter++;
                }
                if ($this->data['Invoice']['total_sender_amt'] > 0 || $this->data['Invoice']['total_credit_amt'] > 0 || $this->data['Invoice']['total_custom_amt'] > 0) {
                    $this->loadModel("InvoiceDetail");
                    $saveAll = $this->InvoiceDetail->saveAll($detail_data['InvoiceData']);
                }
                //Update account
                if (isset($this->data['Invoice']['credit']) && $this->data['Invoice']['credit'] > 0 || $this->data['Invoice']['total_sender_amt'] > 0) {
                    $update_condition = array();
                    if (isset($this->data['Invoice']['credit']) && $this->data['Invoice']['credit'] > 0) {
                        $update_condition['Account.balance_credit'] = "Account.balance_credit + ".$this->data['Invoice']['credit'];
                        $update_condition['Account.available_credits'] = "Account.available_credits  + ".$this->data['Invoice']['credit'];
                    }
                    if (isset($this->data['Invoice']['total_sender_amt']) && $this->data['Invoice']['total_sender_amt'] > 0)
                        $update_condition['Account.total_senders'] = "Account.total_senders + ".$this->data['Invoice']['sender'];
                    $this->loadModel("Account");
                    if ($type == 'create_invoice')
                        $result_ecard = $this->Account->updateAll($update_condition, array('Account.id'=>$account_id));
                    else
                        $result_ecard = $this->Account->updateAll($update_condition, array('Account.id'=>$this->Session->read("Auth.User.account_id")));
                }
                if ($type == 'create_invoice')
                    $message = '<p style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:22px;color:#878787;"><strong>Here is invoice information.</strong></p>';
                else
                    $message = '<p style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:22px;color:#878787;"><strong>Here is payment information </strong></p>';

                if (!empty($total_payments_list_for_mail)) {
                    foreach ($total_payments_list_for_mail as $name=>$value) {
                        $message .= '<p><span style="width:100px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:22px;color:#878787;padding-left:10px;"><strong>'.$name.':</strong></span>';
                        $message .= '<span style="width:100px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:22px;color:#878787;padding-left:10px;">$'.number_format($value, 2, '.', '').'</span>';
                        $message .= '</p>';
                    }
                }
                if ($tax_amount > 0) {
                    $message .= '<p><span style="width:100px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:22px;color:#878787;padding-left:10px;"><strong>Tax:</strong></span>';
                    $message .= '<span style="width:100px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:22px;color:#878787;padding-left:10px;">$'.number_format(($amt * 0.10), 2, '.', '').'</span>';
                    $message .= '</p>';
                }
                $message .= '<p><span style="width:100px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:22px;color:#878787;padding-left:10px;"><strong>Total amount:</strong></span>';
                $message .= '<span style="width:100px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:22px;color:#878787;padding-left:10px;">$'.number_format($total_amt, 2, '.', '').'</span>';
                $message .= '</p>';
                if ($type == 'create_invoice') {
                    $this->generate_invoicepdf($last_insert_id, $account_id, $user_id, $message, $first_name, $last_name, array($email, ADMIN_FROM_EMAIL), array('superadmin_generate_invoice', 'superadmin_generate_invoice_msg'), 1);
                } else
                    $this->generate_invoicepdf($last_insert_id, $this->Session->read('Auth.User.account_id'), $this->Session->read('Auth.User.id'), $message, $this->Session->read("Auth.User.first_name"), $this->Session->read("Auth.User.last_name"));
                if ($last_insert_id) {
                    if ($this->data['Invoice']['credit'] >= 100 && $this->Session->read("Auth.User.account_type") == "corporate_free") {
                        $this->Account->id = $this->Session->read("Auth.User.account_id");
                        $this->Account->saveField('account_type', 'corporate_paid');
                        $this->Session->write('Auth.User.account_type', 'corporate_paid');
                        $this->Session->setFlash('You have successfully completed payment.You have successfully upgrated your account to corporate paid.', true);
                    } else
                        $this->Session->setFlash('You have successfully completed payment', true);
                } else {
                    $this->Session->setFlash("Error in save data.Plz try again ", true);
                }
                return $last_insert_id;
            } else {
                return false;
            }
        } else {
            return $this->data['Invoice'];
        }
    }
    /**
    * @Function : generate_invoicepdf
    * @Purpose: This function use to generate invoice pdf
    * @Author : Ankita
    */
    function generate_invoicepdf($invoice_id, $account_id, $user_id, $messages, $firstname, $lastname, $email = '', $template = '', $is_array = 0) {
        $this->layout = NULL;
        $header = '';
        $id = $invoice_id;
        $sess_acc_id = $account_id;
        $userid = $user_id;
        $path = SITE_URL. "/" .INVOICES.'printinvoice/'.$id.'/'.$userid.'/'.$sess_acc_id;
        $invoice_store_path = SITE_PATH . "files" . DS . "pdf" . DS . $userid . DS;
        if (!file_exists($invoice_store_path)) {
            mkdir($invoice_store_path, 0777);
        }
        $objpdf = new RWSPdf($path, 'eKarda_invoice', 2);
        $objpdf->setLocation($invoice_store_path);
        $objpdf->SetPageWidth(1024);
        $objpdf->SetMargin(0, 0, 0, 0);
        $footer = 'Page no ##PAGE## out of ##PAGES##';
        $objpdf->setheader_footer($header, $footer);
        $objpdf->pdf();
        //Email Send Information
        if ($is_array == 1) {
            for ($i = 0; $i < count($email); $i++) {
                //prepare variables for outgoing email
                $template 		= 'send_confirmation_msg_to_admin';

                $view_vars = array(
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'project_name' => PROJECT_NAME,
                    'total_invoice_desc' => $messages,
                );

                //dispatch the email
                App::import('Component', 'Emailsending'); $this->Emailsending = new EmailsendingComponent();
                $this->Emailsending->pushQueue(array(
                    'to' => $email[$i],
                    'subject' => PAYMENT_EMAIL_SUBJECT,
                    'from' => ADMIN_FROM_EMAIL,
                    'template' => $template,
                    'view_vars' => $view_vars,
                    'attachment' => "{$invoice_store_path}eKarda_invoice.pdf",
                ));
            }
        } else {
            if ($email != '')
                $to = $email;
            else
                $to = $this->Session->read("Auth.User.email");
            $from = ADMIN_FROM_EMAIL;
            $subject = PAYMENT_EMAIL_SUBJECT;
            if ($template == '') {
                if ($this->Session->read("Auth.User.usertype") == 'superadmin')
                    $template = 'superadmin_generate_invoice';
                else
                    $template = 'admin_invoice';
            }
            //Email Information
            $view_vars = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'project_name' => PROJECT_NAME,
                'total_invoice_desc' => $messages
            );
            //dispatch the email
            App::import('Component', 'Emailsending'); $this->Emailsending = new EmailsendingComponent();
            $this->Emailsending->pushQueue(array(
                'to' => $to,
                'subject' => $subject,
                'from' => $from,
                'template' => $template,
                'view_vars' => $view_vars,
                'attachment' => "{$invoice_store_path}eKarda_invoice.pdf",
            ));
        }
        return;
    }
    //end function
    /**
    * @Function : _setMsgDiv
    * @Purpose: This function return message
    * @Author :
    */
    function _setMsgDiv($msg, $mode = 'fail', $additionalVariables = array()) {
    	$this->set('additionalVariables', $additionalVariables);
        if ($mode == 'success') {
            $this->set('msg', $msg);
            $msgDiv = "<span id='id_notifications'>".$this->render('/elements/notification')."</span>".'<script language="javascript">function msgHideEffect(){jQuery("#Myecardsmsg").html("");jQuery("#Scheduledmsg").html("");jQuery("#card_type_msg").html("");jQuery("#card_design_msg").html("");jQuery("#msg").html("");}setTimeout("msgHideEffect()",5000);</script>';
        } else {
            $this->set('msg', $msg);
            $msgDiv = "<span id='id_notifications'>".$this->render('/elements/notification')."</span>".'<script language="javascript">function msgHideEffect(){jQuery("#Myecardsmsg").html("");jQuery("#Scheduledmsg").html("");jQuery("#card_type_msg").html("");jQuery("#card_design_msg").html("");jQuery("#msg").html("");}setTimeout("msgHideEffect()",5000);</script>';
        }
        return $msgDiv;
    }
    /**
    * @Function : _admin_user_login
    * @Purpose: This function is used when superadmin login as admin user
    * @Author : Ankita
    */
    function _admin_user_login($user_id = 0) {
        if ($user_id == '' || $user_id == 0) {
            $this->User->recursive = 1;
            $userData = $this->User->find('first', array('conditions'=>array('User.usertype'=>'superadmin')));
        } else
            $userData = $this->User->find('first', array('conditions'=>array('User.id'=>$user_id)));
        $this->Session->write('Auth.User.first_name', $userData['Userdetail']['first_name']);
        $this->Session->write('Auth.User.last_name', $userData['Userdetail']['last_name']);
        $this->Session->write('Auth.User.account_type', $userData['Account']['account_type']);
        $this->Session->write('Auth.User.country_id', $userData['Userdetail']['country_id']);
        $this->Session->write('Auth.User.balance_credit', $userData['Account']['balance_credit']);
        $this->Session->write('Auth.User.last_login_date', $userData['User']['last_login_date']);
        $notIncludeFileds = array("password");
        foreach ($userData['User'] as $key=>$value) {
            if (!in_array($key, $notIncludeFileds)) {
                $this->Session->write('Auth.User.'.$key, $value);
            }
        }
        $this->Session->setFlash("");
        if ($userData['User']['usertype'] == 'admin') {
            $this->Session->write('admin_user_id', $this->Session->read('Auth.User.id'));
            $this->Session->setFlash(YOU_ARE_LOGIN_AS.$userData['Userdetail']['first_name']." ".$userData['Userdetail']['last_name']);
            $this->Accountinfo->available_credits($this->Session->read('Auth.User.account_id'));
            App::import('Component', 'EcardDesign'); $this->EcardDesign = new EcardDesignComponent();
            $this->EcardDesign->_automatic_selection();
            $this->redirect(SITE_URL.'/admin/users/dashboard');
        } else {
            $this->Session->setFlash(YOU_ARE_SUCESSFULLY_LOGOUT);
            $this->Session->write('admin_user_id', "0");
            $this->redirect(SITE_URL.'/superadmin/users/dashboard');
        }
    }
    /**
    * @Function : customEcardStatusChangeMail
    * @Purpose: When admin created custom card status is changed so send mail to admin abt current status.
    * @Author : Ankita
    */
    function customEcardStatusChangeMail($customcard_id) {
        App::import('Helper', 'Html'); $this->Html = new HtmlHelper();

        $this->loadModel("CustomCardDesignRequest");
        $this->CustomCardDesignRequest->bindModel(array(
            "belongsTo" => array(
                "Userdetail" => array(
                    "className" => "Userdetail",
                    "foreignKey" => "user_id"
                )
            )
        ));
        $customCardDesignRequestData = $this->CustomCardDesignRequest->find("first", array(
            "conditions" => array(
                "CustomCardDesignRequest.id" => $customcard_id
            )
        ));
        $encodedCredentials = $this->EkardaUtility->paramEncode(array(
            $customCardDesignRequestData['User']['id'],
            $customCardDesignRequestData['User']['password']
        ));

        $url = $this->Html->url(array(
            'admin'      => false,
            'controller' => 'users',
            'action'     => 'autoLoginAdmin',
            $encodedCredentials
        ), true);

        if ($customCardDesignRequestData['Userdetail']['first_name'] == '') {
            $firstname    = "User";
            $lastname     = "";
        } else {
            $firstname    = $customCardDesignRequestData['Userdetail']['first_name'];
            $lastname     = $customCardDesignRequestData['Userdetail']['last_name'];
        }
        $custom_card_name = $customCardDesignRequestData['CustomCardDesignRequest']['title'];
        $status           = ucfirst($customCardDesignRequestData['CustomCardDesignRequest']['status']);
        $extra            = '';
        if ($status == 'Quoted') {
            $extra        = CUSTOM_CARD_PAYMENT_NOW_POSSIBLE;
        }

        //Email Send Information
        $to       = $customCardDesignRequestData['User']['email'];
        $from     = ADMIN_FROM_EMAIL;
        $subject  = sprintf(CUSTOM_CARD_CHNAGE_STATUS_EMAIL_SUBJECT, $custom_card_name, $status);
        $template = 'custom_ecard_status_change';

        $view_vars = array(
            'first_name'       => $firstname,
            'last_name'        => $lastname,
            'custom_card_name' => $custom_card_name,
            'status'           => $status,
            'project_url'      => $this->Html->url('/', true),
            'url'              => $url,
            'extra'            => $extra,
        );

        App::import('Component', 'Emailsending'); $this->Emailsending = new EmailsendingComponent();
        $this->Emailsending->pushQueue(array(
            'to'        => $to,
            'subject'   => $subject,
            'from'      => $from,
            'template'  => $template,
            'view_vars' => $view_vars,
        ));
        return true;
    }

    /**
    * @Function : _getCardtypeName
    * @Param :
    * @Return Value : Array of cardtype Names
    * @Purpose: This function is for getting all cardtype names for user section.
    * @Author :
    * @Created Date:
    * @Modified Date:
    */
    function getCardtypeName() {
        $card_type_obj = ClassRegistry::init("CardType");
        return $card_type_obj->find('list', array('fields'=>array('CardType.id', 'CardType.title'), 'order'=>array('CardType.id desc')));
    }
    /**
    * @Function : getCardtemplateName
    * @Param :
    * @Return Value : Array of additional greetings
    * @Purpose: This function is for getting list of card templates.
    * @Author :
    * @Created Date:
    * @Modified Date:
    */
    function getCardtemplateName() {
        $card_type_obj = ClassRegistry::init("CardTemplate");
        return $card_type_obj->find('list', array('fields'=>array('CardTemplate.id', 'CardTemplate.name'), 'conditions'=>array('CardTemplate.status'=>'active'), 'order'=>array('CardTemplate.id desc')));
    }
    //ensure beforeRender is always triggered
    function redirect($url, $status = null, $exit = true) {
        $this->beforeRender();
        parent::redirect($url, $status, $exit);
    }
    function beforeRender() {
        return;
        $sources = ConnectionManager::sourceList();
        $logs = array();
        foreach ($sources as $source) {
            $db = &ConnectionManager::getDataSource($source);
            if (!$db->isInterfaceSupported('getLog')) {
                continue;
            }
            $logs[$source] = $db->getLog();
            foreach ($logs[$source]['log'] as $k=>$i) {
                $this->log("\nSQL - ".h($i['query']));
            }
        }
    }
}
