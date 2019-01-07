<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 */
    header('Content-Type: text/plain;charset=utf-8');
	Class Security
	{
		const salt = "";

        const hex_iv = '00000000000000000000000000000000';

        const key = '397e2eb61307109f6e68006ebcb62f98';

		//不可逆MD5加密
		static function MD5EnCode($stringHandler)
		{
			$stringHandler = $stringHandler.Security::salt;
            $stringResult = md5($stringHandler);
			return $stringResult;
		}

        static public function encrypt($input)
        {
            $data = openssl_encrypt($input, 'AES-256-CBC', Security::key, OPENSSL_RAW_DATA, Security::hexToStr(Security::hex_iv));
            $data = base64_encode($data);
            return $data;
        }

        static public function decrypt($input)
        {
            $decrypted = openssl_decrypt(base64_decode($input), 'AES-256-CBC', Security::key, OPENSSL_RAW_DATA, Security::hexToStr(Security::hex_iv));
            return $decrypted;
        }

        static function hexToStr($hex)
        {

            $string='';

            for ($i=0; $i < strlen($hex)-1; $i+=2)

            {

                $string .= chr(hexdec($hex[$i].$hex[$i+1]));

            }

            return $string;
        }
}

