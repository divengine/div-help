<?php

/**
 * [[]] Div PHP Helpers
 *
 * The div class is the complete implementation of Div: please, extends me!
 *
 * Div (division) is a template engine for PHP >=5.4
 * and it is a social project without spirit of lucre.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program as the file LICENSE.txt; if not, please see
 * https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package org.divengine.divHelp
 * @author  Rafa Rodriguez [@rafageist] <rafageist@hotmail.com>
 * @version 1.0
 *
 * @link    http://divengine.github.io
 *
 * @example example.php
 * @example example.tpl
 */

class divHelp
{

	static $requestHeaders = null;
	/* STRINGS */

	/**
	 * Clear string
	 *
	 * @param String $name
	 * @param String $extra_chars
	 * @param String $chars
	 * @param bool $direction True for keep, false for delete
	 *
	 * @return String
	 */
	public static function strClear($name, $extra_chars = '', $chars = "abcdefghijklmnopqrstuvwxyz", $direction = true)
	{
		$l = strlen($name);
		$new_name = '';
		$chars .= $extra_chars;

		for($i = 0; $i < $l; $i ++)
		{
			$ch = $name[$i];
			if(stripos($chars, $ch) === $direction)
			{
				$new_name .= $ch;
			}
		}

		return $new_name;
	}

	/**
	 * Generate varname from text label
	 *
	 * @param $label
	 *
	 * @return string
	 */
	public static function strVarFromLabel($label)
	{
		$label = trim(strtolower($label));
		while(strpos($label, '  ') !== false)
		{
			$label = str_replace('  ', ' ', $label);
		}

		$label = str_replace(' ', '-', $label);
		$chars = 'abcdefghijklmnopqrstuvwxyz1234567890-';
		$newLabel = '';
		$l = strlen($label);
		for($i = 0; $i < $l; $i ++)
		{
			if(stripos($chars, $label[$i]) !== false)
			{
				$newLabel .= $label[$i];
			}
		}

		return $newLabel;
	}

	/* ARRAYS */

	/**
	 * Get item from array, checking if exists, returning default
	 *
	 * @param $arr
	 * @param $index
	 * @param null $default
	 *
	 * @return null
	 */
	public static function arrayGetItem($arr, $index, $default = null)
	{
		if (!is_array($index)) $index = [$index];

		foreach($index as $v)
		{
			if(isset($arr[$v])) return $arr[$v];
		}

		return $default;
	}

	/**
	 * Get first item of array
	 *
	 * @param $arr
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public static function arrayFirstItem($arr, $default = null)
	{
		if(is_array($arr))
		{
			foreach($arr as $item)
			{
				return $item;
			}
		}

		return $default;
	}

	/**
	 * Get last item from array
	 *
	 * @param $arr
	 * @param null $default
	 *
	 * @return null
	 */
	public static function arrayLastItem($arr, $default = null)
	{
		if(is_array($arr))
		{
			$arrx = array_reverse($arr);
			foreach($arrx as $item)
			{
				return $item;
			}
		}

		return $default;
	}

	/**
	 * Fastest array length
	 *
	 * @param $arr
	 *
	 * @return int
	 */
	public static function arrayLength($arr)
	{
		if( ! is_array($arr)) return - 1;
		$i = 0;
		foreach($arr as $item)
		{
			$i ++;
		}

		return $i;
	}


	/**
	 * Get random item from array
	 *
	 * @param $arr
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public static function arrayGetRandomItem($arr, $default = null)
	{
		if( ! is_array($arr)) return null;

		return $arr[array_rand($arr)];
	}

	/**
	 * Search for $values in array's keys and values
	 *
	 * @param array $arr
	 * @param array $values
	 * @param bool $all
	 *
	 * @return bool
	 */
	public static function arrayIssetSearch($arr = [], $values = [], $all = false)
	{
		$total = count($values);
		$i = 0;
		foreach($values as $val)
		{
			if(isset($arr[$val]) || array_search($val, $arr) !== false) $i ++;
		}

		if($all) return $i == $total;

		return $i > 0;
	}


	/* CURL */

	/**
	 * GET
	 *
	 * @param $url
	 *
	 * @return bool|mixed
	 */
	public static function remoteGET($url, $headers = [])
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 404)
		{
			$data = false;
		}
		curl_close($ch);

		return $data;
	}

	/**
	 * POST
	 *
	 * @param $url
	 * @param null $postData
	 *
	 * @return bool|mixed
	 */
	public static function remotePOST($url, $postData = null, $headers = [])
	{

		$data_string = json_encode($postData);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);

		$headers[] = 'Content-Type: application/json';
		$headers[] =' Content-Length: ' . strlen($data_string);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

		$data = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 404)
		{
			$data = false;
		}

		curl_close($ch);

		return $data;
	}

	/**
	 * Get and decode
	 *
	 * @param $url
	 *
	 * @return mixed
	 */
	public static function remoteGetJSON($url, $headers = [])
	{
		$data = self::remoteGET($url, $headers);

		return json_decode($data);
	}

	/**
	 * Post and decode
	 *
	 * @param $url
	 * @param null $postData
	 *
	 * @return mixed
	 */
	public static function remotePostJSON($url, $postData = null, $headers = [])
	{
		$data = self::remotePOST($url, $postData, $headers);

		return json_decode($data);
	}


	public static function getJSONPost(){
		$data = file_get_contents("php://input");
		$data = json_decode($data);
		return $data;
	}

	public static function post($var, $default = null)
	{
		return self::arrayGetItem($_POST, $var, $default);
	}

	public static function get($var, $default = null)
	{
		return self::arrayGetItem($_GET, $var, $default);
	}



	/**
	 * Get all HTTP header key/values as an associative
	 * array for the current request.
	 *
	 * @return array The HTTP header key/value pairs.
	 */
	public static function getRequestHeaders()
	{

		if(is_null(self::$requestHeaders))
		{

			self::$requestHeaders = [];

			$copy_server = [
				'CONTENT_TYPE' => 'Content-Type',
				'CONTENT_LENGTH' => 'Content-Length',
				'CONTENT_MD5' => 'Content-Md5',
			];

			foreach($_SERVER as $key => $value)
			{
				if(substr($key, 0, 5) === 'HTTP_')
				{
					$key = substr($key, 5);
					if( ! isset($copy_server[$key]) || ! isset($_SERVER[$key]))
					{
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
						self::$requestHeaders[$key] = $value;
					}
				}
				elseif(isset($copy_server[$key]))
				{
					self::$requestHeaders[$copy_server[$key]] = $value;
				}
			}

			if( ! isset(self::$requestHeaders['Authorization']))
			{
				if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
				{
					self::$requestHeaders['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
				}
				elseif(isset($_SERVER['PHP_AUTH_USER']))
				{
					$basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
					self::$requestHeaders['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
				}
				elseif(isset($_SERVER['PHP_AUTH_DIGEST']))
				{
					self::$requestHeaders['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
				}
			}

			if (function_exists('getallheaders')) {
				self::$requestHeaders = array_merge(self::$requestHeaders, getallheaders());
			}
		}

		return self::$requestHeaders;
	}

	/**
	 * Return request header value
	 *
	 * @param $header
	 * @param null $default
	 *
	 * @return null
	 */
	static function getRequestHeader($header, $default = null){
		return self::arrayGetItem(self::getRequestHeaders(), $header, $default);
	}

	/**
	 * Complete object/array properties
	 *
	 * @param mixed $source
	 * @param mixed $complement
	 * @param integer $level
	 *
	 * @return mixed
	 */
	final static function cop(&$source, $complement, $level = 0)
	{
		$null = null;

		if(is_null($source))
		{
			return $complement;
		}

		if(is_null($complement))
		{
			return $source;
		}

		if(is_scalar($source) && is_scalar($complement))
		{
			return $complement;
		}

		if(is_scalar($complement) || is_scalar($source))
		{
			return $source;
		}

		if($level < 100)
		{ // prevent infinite loop
			if(is_object($complement))
			{
				$complement = get_object_vars($complement);
			}

			foreach($complement as $key => $value)
			{
				if(is_object($source))
				{
					if(isset ($source->$key))
					{
						$source->$key = self::cop($source->$key, $value, $level + 1);
					}
					else
					{
						$source->$key = self::cop($null, $value, $level + 1);
					}
				}
				if(is_array($source))
				{
					if(isset ($source [$key]))
					{
						$source [$key] = self::cop($source [$key], $value, $level + 1);
					}
					else
					{
						$source [$key] = self::cop($null, $value, $level + 1);
					}
				}
			}
		}

		return $source;
	}

	/**
	 * Output a JSON REST response
	 *
	 * @param $data
	 * @param int $http_response_code
	 */
	static function rest($data, $http_response_code = 200)
	{
		header("Content-type: application/json", true, $http_response_code);
		http_response_code($http_response_code);
		echo json_encode($data);
	}

	/**
	 * diff from datetime
	 *
	 * @param datetime $dt1
	 * @param datetime $dt2
	 *
	 * @return object $dtd (day, hour, min, sec / total)
	 */
	static function datetimeDiff($dt1, $dt2){
		$t1 = strtotime($dt1);
		$t2 = strtotime($dt2);

		$dtd = new stdClass();
		$dtd->interval = $t2 - $t1;
		$dtd->total_sec = abs($t2 - $t1);
		$dtd->total_min = floor($dtd->total_sec / 60);
		$dtd->total_hour = floor($dtd->total_min / 60);
		$dtd->total_day = floor($dtd->total_hour / 24);

		$dtd->day = $dtd->total_day;
		$dtd->hour = $dtd->total_hour - ($dtd->total_day * 24);
		$dtd->min = $dtd->total_min - ($dtd->total_hour * 60);
		$dtd->sec = $dtd->total_sec - ($dtd->total_min * 60);

		return $dtd;
	}
}