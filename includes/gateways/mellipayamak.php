<?php
/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_MELLIPAYAMAK {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('ملی پیامک', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'username'  => __('نام کاربری','GF_SMS'),
			'password'  => __('پسورد','GF_SMS')
		);
	}

	/*
	* Gateway credit
	*/
	public static function credit(){
		return true;
	}
	

	/*
	* Gateway action
	*/	
	public static function process( $options, $action, $from, $to, $messages ){
	
		if ( $action == 'credit' && !self::credit() ) {
			return false;
		}
		
		if ( ! extension_loaded('soap') )
			return __('ماژول Soap بر روی هاست شما فعال نمی باشد .','GF_SMS');
		
		
		
		$username = $options['username'];
		$password = $options['password'];
		
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$to = explode(',', $to);
		$i = sizeOf($to);
		while($i--){
			$uNumber = Trim($to[$i]);
			$ret = &$uNumber;
			if (substr($uNumber,0, 5) == '%2B98'){ 
				$ret = substr($uNumber, 5);
			}
			if (substr($uNumber,0, 5) == '%2b98'){ 
				$ret = substr($uNumber, 5);
			}
			if (substr($uNumber,0, 4) == '0098'){ 
				$ret = substr($uNumber, 4);
			}
			if (substr($uNumber,0, 3) == '098')	{ 
				$ret = substr($uNumber, 3);
			}
			if (substr($uNumber,0, 3) == '+98'){ 
				$ret = substr($uNumber, 3);
			}
			if (substr($uNumber,0, 2) == '98'){ 
				$ret = substr($uNumber, 2);
			}
			$to[$i] =  '0' . $ret;
		}
		
		try	{
			$parameters['username'] = $username;
			$parameters['password'] = $password;
			$parameters['from'] 	= $from;
			$parameters['to'] 		= $to;
			$parameters['text'] 	= $messages;
			$parameters['isflash'] 	= false;
			$parameters['udh'] 		= '';
			$parameters['recId'] 	= array(0);
			$parameters['status'] 	= 0x0;
	
			if ($action == "send") {
				$client = new SoapClient("http://37.228.138.118/post/send.asmx?wsdl");	
				$send = $client->SendSms($parameters)->SendSmsResult;
			}
			
			if ($action == "credit") {
				$client = new SoapClient("http://37.228.138.118/post/send.asmx?wsdl");	
				$credit = $client->GetCredit(array("username"=> $parameters['username'] ,"password"=>$parameters['password']))->GetCreditResult;
			}
			
			
		}
		catch (SoapFault $ex) {
			$errorstr = $ex->faultstring;
		}
		
		
		if ($action == "send"){
			if ( $send == 0 ) {
				return __('نام کاربری یا کلمه عبور اشتباه است .', 'GF_SMS' );
			}
			else if ( $send == 2 ){
				return __('اعتبار کافی نیست .', 'GF_SMS' );
			}
			else if ( $send == 3 ){
				return __('محدودیت در ارسال روزانه .', 'GF_SMS' );
			}
			else if ( $send == 4 ){
				return __('محدودیت در حجم ارسال .', 'GF_SMS' );
			}
			else if ( $send == 5 ){
				return __('شماره فرستنده معتبر نیست .', 'GF_SMS' );
			}
			else if ( $send == 6 ){
				return __('سامانه در حال بروز رسانی است .', 'GF_SMS' );
			}
			else if ( $send == 7 ){
				return __('متن حاوی کلمات فیلتر شده میباشد .', 'GF_SMS' );
			}
			else if ( $send == 9 ){
				return __('ارسال از خطوط عمومی توسط وب سرویس امکان پذیر نیست .', 'GF_SMS' );
			}
			else if ( $send == 10 ){
				return __('کاربر مورد نظر فعال نیست .', 'GF_SMS' );
			}
			else if ( $send == 11 ){
				return __('پیامک ارسال نشده است .', 'GF_SMS' );
			}
			else if ( $send == 12 ){
				return __('مدارک کاربر کامل نشده است .', 'GF_SMS' );
			}
			else if ( $send == 1 ){
				return 'OK';
			}
			else {
				return __('خطای ناشناخته .', 'GF_SMS' );
			}
		}
		
		if ($action == "credit") {
			if ( $credit == 0 ) {
				return __('نام کاربری یا کلمه عبور اشتباه است .', 'GF_SMS' );
			}
			return ( (int) $credit ) . __(' پیامک', 'GF_SMS' );
		}

		
		if ($action == "range"){
			$min = 100;
			$max = 200;
			return array("min" => $min, "max" => $max);
		}

	}
}