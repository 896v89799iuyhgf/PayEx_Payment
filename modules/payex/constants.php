<?php
/* Local wsdl's should be faster then remote, specially cached wsdl files.

   remote wsdl files is better if you want the newest functions accessible automatically.
   "Still need to be defined in functions.php".
*/

/* This is for PROD environment, local wsdl files */
// define ("PxOrderWSDL","wsdl/external.payex.com-pxorder.wsdl");
// define ("PxConfinedWSDL","wsdl/confined.payex.com-pxorder.wsdl");
/********************************/

/* This is for PROD environment, remote wsdl files from PayEx*/
// define ("PxOrderWSDL","https://external.payex.com/pxorder/pxorder.asmx?wsdl");
// define ("PxConfinedWSDL","https://confined.payex.com/PxConfined/pxorder.asmx?wsdl");

/********************************/

/* This is for TEST environment, local wsdl files */
//define ("PxOrderWSDL","wsdl/test-external.payex.com-pxorder.wsdl");
// define ("PxConfinedWSDL","wsdl/test-confined.payex.com-pxorder.wsdl");

/********************************/
if (Configuration::get('PS_PAYEX_ENV') == 0) {
    /* This is for TEST environment, remote wsdl files from PayEx */
    define ("PxOrderWSDL", "https://test-external.payex.com/pxorder/pxorder.asmx?wsdl");
    define ("PxConfinedWSDL", "https://test-confined.payex.com/PxConfined/pxorder.asmx?wsdl");
} else {
    /* This is for PROD environment, remote wsdl files from PayEx*/
    define ("PxOrderWSDL", "https://external.payex.com/pxorder/pxorder.asmx?wsdl");
    define ("PxConfinedWSDL", "https://confined.payex.com/PxConfined/pxorder.asmx?wsdl");
}

/********************************/

