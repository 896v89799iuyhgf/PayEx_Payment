<?php
include_once(dirname(__FILE__)."/../../pxorder.php");

class PayExReciptModuleFrontController extends ModuleFrontController
{
    public $accountNumber; // NB: Merchant account number REMEMBER TO SET MERCHANT ENCRYPTION KEY IN FUNCTIONS.PHP
    public function postProcess(){
        //Normal Prestashop order action
        if ($this->context->cart->id_customer == 0 || !$this->module->active)
            Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'payex')
            {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die(Tools::displayError('This payment method is not available.'));
        $customer = new Customer((int)$this->context->cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
//        $customer = new Customer((int)$this->context->cart->id_customer);
        $total = $this->context->cart->getOrderTotal(true, Cart::BOTH);

        //PayEX Implemantation
        $this->accountNumber = trim(Configuration::get('PS_PAYEX_ACC_NUM'));
        $order               = new pxorder();
        $functions           = new functions();
        $orderRef            = stripcslashes($_GET['orderRef']);

        $params = array('accountNumber' => $this->accountNumber,
                        'orderRef'      => $orderRef);

        $completeResponse = $order->Complete($params);
        $result           = $functions->complete($completeResponse);

        /*
        Transaction statuses (defined in payex_defines.php):
        0=Sale, 1=Initialize, 2=Credit, 3=Authorize, 4=Cancel, 5=Failure, 6=Capture

        */
//        var_dump($result);

        if ($result['transactionStatus'] == '0' || $result['transactionStatus'] == '6') {
            $payment_type = (int)Configuration::get('PS_OS_PAYMENT');
            $this->module->validateOrder((int)$this->context->cart->id, $payment_type, $total, $this->module->displayName, null, array(), null, false, $customer->secure_key);
        }else if($result['transactionStatus'] == '5'){
            $payment_type = (int)Configuration::get('PS_OS_ERROR');
            $this->module->validateOrder((int)$this->context->cart->id, $payment_type, $total, $this->module->displayName, null, array(), null, false, $customer->secure_key);
        } else {
            $payment_type = (int)Configuration::get('PS_OS_ERROR');
            $this->module->validateOrder((int)$this->context->cart->id, $payment_type, $total, $this->module->displayName, null, array(), null, false, $customer->secure_key);
        }

        Configuration::updateValue('PS_PAYEX_TRANS', $result['transactionStatus']);
        Tools::redirectLink('index.php?controller=order-confirmation?key='.$customer->secure_key.'&id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->module->id.'&id_order='.(int)$this->module->currentOrder);
    }
    public function initContent() {
        $this->display_column_left = false;
        parent::initContent();
        $this->setTemplate('payex_confirmation.tpl');
    }
}
?>
