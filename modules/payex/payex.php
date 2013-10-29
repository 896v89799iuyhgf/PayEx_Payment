<?php
if (!defined('_PS_VERSION_')) exit;

include_once("confined.php");
include_once("pxorder.php");

class Payex extends PaymentModule
{
    public function __construct()
    {
        $this->name          = 'payex';
        $this->tab           = 'payments_gateways';
        $this->version       = '1.0';
        $this->author        = 'Tan Phan - tanitct89@gmail.com';
        $this->need_instance = 1;

        $this->currencies = TRUE;
        $this->currencies_mode = 'radio';

        parent::__construct();

        $this->displayName = $this->l('PayEX');
        $this->description = $this->l('Prestashop Module for PayEX Payment Gateway.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('PS_PAYEX')) $this->warning = $this->l('No name provided');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) Shop::setContext(Shop::CONTEXT_ALL);

        return
            parent::install()
            && Configuration::updateValue('PS_PAYEX', 'pay_ex')
            && $this->registerHook('payment')
            && $this->registerHook('paymentReturn');
    }

    public function uninstall()
    {
        return parent::uninstall() && Configuration::deleteByName('PS_PAYEX') && Configuration::deleteByName('PS_PAYEX_ACC_NUM') && Configuration::deleteByName('PS_PAYEX_ENC_KEY') && Configuration::deleteByName('PS_PAYEX_ENV')&& Configuration::deleteByName('PS_PAYEX_DESC');
    }

    public function getContent()
    {
        $output = NULL;

        if (Tools::isSubmit('submit' . $this->name)) {
            $account_number = strval(Tools::getValue('PS_PAYEX_ACC_NUM'));
            $encryption_key = strval(Tools::getValue('PS_PAYEX_ENC_KEY'));
            $live_mode       = strval(Tools::getValue('PS_PAYEX_ENV'));
            $desc     = strval(Tools::getValue('PS_PAYEX_DESC'));
            if ((!$account_number || empty($account_number) || !Validate::isInt($account_number)) && (!$encryption_key || empty($encryption_key))) $output .= $this->displayError($this->l('Invalid Configuration value')); else {
                Configuration::updateValue('PS_PAYEX_ACC_NUM', $account_number);
                Configuration::updateValue('PS_PAYEX_ENC_KEY', $encryption_key);
                Configuration::updateValue('PS_PAYEX_ENV', $live_mode);
                Configuration::updateValue('PS_PAYEX_DESC', $desc);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $this->displayForm();
    }

    public function displayForm()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array('legend' => array('title' => $this->l('PayEX Payment Gateway'),),
                                        'input'  => array(array('type'     => 'text',
                                                                'label'    => $this->l('Account Number'),
                                                                'name'     => 'PS_PAYEX_ACC_NUM',
                                                                'size'     => 20,
                                                                'required' => TRUE),
                                                          array('type'     => 'text',
                                                                'label'    => $this->l('Encryption Key'),
                                                                'name'     => 'PS_PAYEX_ENC_KEY',
                                                                'size'     => 20,
                                                                'required' => TRUE),
                                                          array(
                                                              'type'    => 'radio',
                                                              'label'   => $this->l('Live Mode'),
                                                              'desc'    => $this->l('Test or Live Mode'),
                                                              'name'    => 'PS_PAYEX_ENV',
                                                              'class'     => 't',
                                                              'is_bool'   => true,
                                                              'values'  => array(
                                                                  array(
                                                                      'id'    => 'live_on',
                                                                      'value' => 1,
                                                                      'label' => $this->l('Enabled')
                                                                  ),
                                                                  array(
                                                                      'id'    => 'live_off',
                                                                      'value' => 0,
                                                                      'label' => $this->l('Disabled')
                                                                  )
                                                              ),
                                                          ),
                                                          array('type'     => 'textarea',
                                                                'label'    => $this->l('Description'),
                                                                'desc'     => 'Description to show on payex payment page',
                                                                'name'     => 'PS_PAYEX_DESC',
                                                                'cols'     => 60,
                                                                'rows'     => 5,
                                                                'required' => TRUE),
                                        ),
                                        'submit' => array('title' => $this->l('Save'),
                                                          'class' => 'button'));

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module          = $this;
        $helper->name_controller = $this->name;
        $helper->token           = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex    = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language    = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title          = $this->displayName;
        $helper->show_toolbar   = TRUE; // false -> remove toolbar
        $helper->toolbar_scroll = TRUE; // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action  = 'submit' . $this->name;
        $helper->toolbar_btn    = array('save' => array('desc' => $this->l('Save'),
                                                        'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),),
                                        'back' => array('href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                                                        'desc' => $this->l('Back to list')));

        // Load current value
        $helper->fields_value['PS_PAYEX_ACC_NUM']      = Configuration::get('PS_PAYEX_ACC_NUM');
        $helper->fields_value['PS_PAYEX_ENC_KEY']      = Configuration::get('PS_PAYEX_ENC_KEY');
        $helper->fields_value['PS_PAYEX_ENV'] = Configuration::get('PS_PAYEX_ENV');
        $helper->fields_value['PS_PAYEX_DESC'] = Configuration::get('PS_PAYEX_DESC');

        return $helper->generateForm($fields_form);
    }

    public function hookPayment($params)
    {
        if (!$this->active) return;

        $this->context->smarty->assign(array(
                                       'path' => $this->_path
                                       ));

        return $this->display(__FILE__,'payment_button.tpl');
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) return;
        return $this->display(__FILE__, 'confirmation.tpl');
    }
}
