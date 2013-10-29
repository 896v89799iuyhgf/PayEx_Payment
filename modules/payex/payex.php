<?php
if (!defined('_PS_VERSION_')) exit;

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
        return parent::uninstall() && Configuration::deleteByName('PS_PAYEX') && Configuration::deleteByName('PS_PAYEX_ACC_NUM') && Configuration::deleteByName('PS_PAYEX_ENC_KEY') && Configuration::deleteByName('PS_PAYEX_RETURN_URL') && Configuration::deleteByName('PS_PAYEX_CANCEL_URL') && Configuration::deleteByName('PS_PAYEX_PX_ORDER') && Configuration::deleteByName('PS_PAYEX_PX_CONFINED');
    }

    public function getContent()
    {
        $output = NULL;

        if (Tools::isSubmit('submit' . $this->name)) {
            $account_number = strval(Tools::getValue('PS_PAYEX_ACC_NUM'));
            $encryption_key = strval(Tools::getValue('PS_PAYEX_ENC_KEY'));
            $return_url     = strval(Tools::getValue('PS_PAYEX_RETURN_URL'));
            $cancel_url     = strval(Tools::getValue('PS_PAYEX_CANCEL_URL'));
            $px_order       = strval(Tools::getValue('PS_PAYEX_PX_ORDER'));
            $px_confined    = strval(Tools::getValue('PS_PAYEX_PX_CONFINED'));
            if ((!$account_number || empty($account_number) || !Validate::isInt($account_number)) && (!$encryption_key || empty($encryption_key))) $output .= $this->displayError($this->l('Invalid Configuration value')); else {
                Configuration::updateValue('PS_PAYEX_ACC_NUM', $account_number);
                Configuration::updateValue('PS_PAYEX_ENC_KEY', $encryption_key);
                Configuration::updateValue('PS_PAYEX_RETURN_URL', $return_url);
                Configuration::updateValue('PS_PAYEX_CANCEL_URL', $cancel_url);
                Configuration::updateValue('PS_PAYEX_PX_ORDER', $px_order);
                Configuration::updateValue('PS_PAYEX_PX_CONFINED', $px_confined);
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
                                                          array('type'     => 'text',
                                                                'label'    => $this->l('Return URL'),
                                                                'name'     => 'PS_PAYEX_RETURN_URL',
                                                                'size'     => 20,
                                                                'required' => TRUE),
                                                          array('type'     => 'text',
                                                                'label'    => $this->l('Cancel URL'),
                                                                'name'     => 'PS_PAYEX_CANCEL_URL',
                                                                'size'     => 20,
                                                                'required' => TRUE),
                                                          array('type'     => 'text',
                                                                'label'    => $this->l('PxOrderWSDL File Location'),
                                                                'name'     => 'PS_PAYEX_PX_ORDER',
                                                                'size'     => 20,
                                                                'required' => TRUE),
                                                          array('type'     => 'text',
                                                                'label'    => $this->l('PxConfinedWSDL File Location'),
                                                                'name'     => 'PS_PAYEX_PX_CONFINED',
                                                                'size'     => 20,
                                                                'required' => TRUE)),
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
        $helper->fields_value['PS_PAYEX_RETURN_URL']   = Configuration::get('PS_PAYEX_RETURN_URL');
        $helper->fields_value['PS_PAYEX_CANCEL_URL']   = Configuration::get('PS_PAYEX_CANCEL_URL');
        $helper->fields_value['PS_PAYEX_PX_ORDER'] = Configuration::get('PS_PAYEX_PX_ORDER');
        $helper->fields_value['PS_PAYEX_PX_CONFINED'] = Configuration::get('PS_PAYEX_PX_CONFINED');

        return $helper->generateForm($fields_form);
    }

    public function hookPayment($params)
    {
        if (!$this->active) return;
        global $smarty;
//        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) return;
//        return $this->display(__FILE__, 'confirmation.tpl');
    }
}
