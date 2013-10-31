<?php
//echo $this->_path; exit;
include_once(dirname(__FILE__)."/../../confined.php");
include_once(dirname(__FILE__)."/../../pxorder.php");
class PayExPayRedirectModuleFrontController extends ModuleFrontController {
    protected $accountNumber; // NB: Merchant account number REMEMBER TO SET MERCHANT ENCRYPTION KEY IN FUNCTIONS.PHP
    protected $purchaseOperation = 'SALE'; // AUTHORIZATION or SALE
    protected $price; // Product price, in lowest monetary unit (=1 NOK)
    protected $priceArgList = ''; // No CPA, VISA,
    protected $currency = 'SEK';
    protected $vat = 0; // Vat
    protected $orderID; // Local order id
    protected $productNumber; // Local product number
    protected $description; // Product description
    protected $clientIPAddress = '';
    protected $clientIdentifier = '';
    protected $additionalValues = '';
    protected $externalID = '';
    protected $returnUrl; // ReturnURL
    protected $view = 'CC'; // Payment method PayEx
    protected $agreementRef = '';
    protected $cancelUrl;
    protected $clientLanguage = '';
//$respons is xml
    private $respons;
    private $orderRef;

    public function init() {
        parent::init();
        $cart = $this->context->cart;
        $cart_details = $cart->getSummaryDetails(null, true);
        Configuration::updateValue('PS_PAYEX_TRANS', 7);

        $this->accountumber = trim(Configuration::get('PS_PAYEX_ACC_NUM'));
        $this->returnUrl = $this->context->link->getModuleLink('payex', 'recipt');
        $this->cancelUrl = $this->context->link->getPageLink('order');
        $this->orderID = (int)$cart->id;
        $this->productNumber = (int)$cart->id;
        $this->price = round((int)$cart->getOrderTotal(true) * 100,2);
        $this->description = Configuration::get('PS_PAYEX_DESC');

        $this->TwoPhaseTransaction();
    }

    protected function initialization()
    {
        //$_server won't work if run from console.
        $this->clientIPAddress = $_SERVER['REMOTE_ADDR'];
        $this->clientIdentifier = "USERAGENT=".$_SERVER['HTTP_USER_AGENT'];
        $params = array
        (
            'accountNumber' => $this->accountumber,
            'purchaseOperation' => $this->purchaseOperation,
            'price' => $this->price,
            'priceArgList' => $this->priceArgList,
            'currency' => $this->currency,
            'vat' => $this->vat,
            'orderID' => $this->orderID,
            'productNumber' => $this->productNumber,
            'description' => $this->description,
            'clientIPAddress' => $this->clientIPAddress,
            'clientIdentifier' => $this->clientIdentifier,
            'additionalValues' => $this->additionalValues,
            'externalID' => $this->externalID,
            'returnUrl' => $this->returnUrl,
            'view' => $this->view,
            'agreementRef' => $this->agreementRef,
            'cancelUrl' => $this->cancelUrl,
            'clientLanguage' => $this->clientLanguage
        );

        return $params;
    }

    protected function TwoPhaseTransaction()
    {
        $order = new pxorder();
        $functions = new functions();

        $params = $this->initialization();
        $result = $order->initialize7($params);
//        print_r($result); exit;
        $status = $functions->checkStatus($result);

        // if code & description & errorCode is OK, redirect the user
        if($status['code'] == "OK" && $status['errorCode'] == "OK" && $status['description'] == "OK")
        {
            header('Location: '.$status['redirectUrl']);
            //echo $result;
            //echo $status['redirectUrl'];
        }else {
            foreach($status as $error => $value)
            {
                echo "$error, $value"."\n";
            }
        }
    }
}
