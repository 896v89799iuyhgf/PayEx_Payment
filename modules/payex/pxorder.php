<?php
include_once("constants.php");
include_once('functions.php');

class pxorder{

	function initialize7($params)
	{
		$PayEx = new SoapClient(PxOrderWSDL,array("trace" => 1, "exceptions" => 0));
		$function = new functions();

		//create the hash
		$hash = $function->createHash(trim(implode("", $params)));
		//append the hash to the parameters
		$params['hash'] = $hash;

		try{
			//defining which initialize version to run, this one is 7.
			$respons = $PayEx->Initialize7($params);
			/* NB: SHOULD BE EDITED TO NOT SHOW THE CUSTOMER THIS MESSAGE, BUT SHOW A GENERIC ERROR MESSAGE FOR THE USER, BUT YOU SHOULD BE INFORMED OF THE ERROR. "*/
		}catch (SoapFault $error){
			echo "Error: {$error->faultstring}";
		}
		return $respons->{'Initialize7Result'};
		//print_r($respons->{'Initialize7Result'}."\n");
	}


	function Complete($params)
	{
		$PayEx = new SoapClient(PxOrderWSDL,array("trace" => 1, "exceptions" => 0));
		$function = new functions();

		//create the hash
		$hash = $function->createHash(trim(implode("", $params)));
		//append the hash to the parameters
		$params['hash'] = $hash;

		try{
			//defining which complete
			$respons = $PayEx->Complete($params);
			/* NB: SHOULD BE EDITED TO NOT SHOW THE CUSTOMER THIS MESSAGE, BUT SHOW A GENERIC ERROR MESSAGE FOR THE USER, BUT YOU SHOULD BE INFORMED OF THE ERROR. "*/
		}catch (SoapFault $error){
			echo "Error: {$error->faultstring}";
		}
		return $respons->{'CompleteResult'};

	}

}

?>