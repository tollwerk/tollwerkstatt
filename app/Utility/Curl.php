<?php

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace App\Utility;

/**
 * CURL-Wrapper-Klasse (zur HTTP-Kommunikation mit entfernten Endpunkten)
 *
 * @author joschi
 *
 */
class Curl {
	/**
	 * SSL-Zertifikatsvalidierung
	 *
	 * @var boolean
	 */
	protected static $_verify = true;
	/**
	 * GET-Aufruf
	 *
	 * @var string
	 */
	const GET = 'GET';
	/**
	 * POST-Aufruf
	 *
	 * @var string
	 */
	const POST = 'POST';
	/**
	 * DELETE-Aufruf
	 *
	 * @var string
	 */
	const DELETE = 'DELETE';
	/**
	 * PUT-Aufruf
	 *
	 * @var string
	 */
	const PUT = 'PUT';

	/**
	 * Aktivieren / Deaktivieren der Validierung von SSL-Zertifikaten
	 *
	 * @param boolean $verify			Validierung von SSL-Zertifikaten
	 * @return boolean					Validierung von SSL-Zertifikaten
	 */
	public static function setVerify($verify = true) {
		self::$_verify					= (boolean)$verify;
		return self::$_verify;
	}

	/**
	 * Absetzen eines HTTP-Aufrufs per CURL
	 *
	 * @param string $url				Endpunkt / URL
	 * @param array $header				Header
	 * @param string $method			Methode
	 * @param string $body				Body
	 * @param boolean $debug			Debugging-Ausgaben
	 * @param int $httpStatus			HTTP-Status-Code
	 * @return string					Daten
	 */
	public static function httpRequest($url, array $header = array(), $method = self::GET, $body = null, $debug = false, &$httpStatus = 0) {
//		echo $url."\n";
		$httpStatus						= 0;

		$curl							= curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		if (!self::$_verify) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}

		if ($body) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, strval($body));
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($header, 'Content-Type: text/xml;charset=utf-8'));
		}

		$data							= curl_exec($curl);
		$info							= curl_getinfo($curl);
		$httpStatus						= $info['http_code'];

		// Ggf. Debugging-Ausgabe
		if ($debug) {
			$info['method']				= $method;
			$info['body']				= strval($body);
			print_r($header);
			print_r($info);
		}

		curl_close($curl);

		return $data;
	}
}
