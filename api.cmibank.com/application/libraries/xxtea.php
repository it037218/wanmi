<?php
/**
 * xxtea加密类
 *
 * 免费library修改而来的静态类
 *
 * @author dark9@rune.ws
 *
 * @copyright Dec 5, 2006 Ma Bingyao <andot@ujn.edu.cn>, This library is free.  You can redistribute it and/or modify it.
 */
abstract class lib_xxtea
{
	private static function _long2str($v, $w)
	{
		$len = count($v);

		$n = ($len - 1) << 2;

		if ($w)
		{
			$m = $v[$len - 1];

			if (($m < $n - 3) || ($m > $n))
			{
				return false;
			}

			$n = $m;
		}

		$s = array();

		for ($i = 0; $i < $len; $i++)
		{
			$s[$i] = pack("V", $v[$i]);
		}

		if ($w)
		{
			return substr(join('', $s), 0, $n);
		}
		else
		{
			return join('', $s);
		}
	}

	private static function _str2long($s, $w)
	{
		$v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
		$v = array_values($v);
		if ($w)
		{
			$v[count($v)] = strlen($s);
		}

		return $v;
	}

	private static function _int32($n)
	{
		while ($n >= 2147483648)
		{
			$n -= 4294967296;
		}

		while ($n <= -2147483649)
		{
			$n += 4294967296;
		}

		return (int)$n;
	}

	public static function encrypt($str, $key)
	{		
		if ($str == "")
		{
			return "";
		}

		$v = self::_str2long($str, true);
		$k = self::_str2long($key, false);

		if (count($k) < 4)
		{
			for ($i = count($k); $i < 4; $i++)
			{
				$k[$i] = 0;
			}
		}

		$n = count($v) - 1;

		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = 0;

		while (0 < $q--)
		{
			$sum = self::_int32($sum + $delta);
			$e = $sum >> 2 & 3;
			for ($p = 0; $p < $n; $p++)
			{
				$y = $v[$p + 1];
				$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$z = $v[$p] = self::_int32($v[$p] + $mx);
			}
			$y = $v[0];
			$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$n] = self::_int32($v[$n] + $mx);
		}

		return self::_long2str($v, false);
	}
	/**
	 *新KEY算法
	 * @param type $key
	 * @return string 
	 */
	public static function getNewKey($key)
	{
		$z1=hexdec(substr($key, 0, 4));	
		$z2=hexdec(substr($key, 2, 4));	
		$z3=hexdec(substr($key, 7, 4));
		$z4=hexdec(substr($key, 10, 4));
		$z1  = ceil(($z1 << 4)/5.27 )>>1;
		$z2  = ceil(($z2 <<3)/9.79842 )>>2;
		$z3  = ceil(($z3 <<3)/7.27 )>>1;
		$z4  = ceil(($z4 <<2)/8.27 )>>2;
		$rt=max(1,$z1,$z2,$z3,$z4);		
		$rt=strval($rt);
		$len=strlen($rt);
		$len=$len>10?10:$len;
		$rt=substr($rt, 0,10).substr($key,$len);
		return $rt;
	}
	/**
	 *非QQ平台使用的KEY
	 * @param type $key 
	 */
	public static function notqqkey($key)
	{
		//战斗公钥验证
		$key=strrev($key);
		$b=substr($key,5,1);
		$c=substr($key,8,1);
		$key=str_replace($b,$c,$key);
		return $key;
	}
	public static function decrypt($str, $key)
	{		
		if ($str == "")
		{
			return "";
		}

		$v = self::_str2long($str, false);
		$k = self::_str2long($key, false);
		if (count($k) < 4)
		{
			for ($i = count($k); $i < 4; $i++)
			{
				$k[$i] = 0;
			}
		}

		$n = count($v) - 1;
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = self::_int32($q * $delta);
		while ($sum != 0)
		{
			$e = $sum >> 2 & 3;
			for ($p = $n; $p > 0; $p--)
			{
				$z = $v[$p - 1];
				$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$y = $v[$p] = self::_int32($v[$p] - $mx);
			}
			$z = $v[$n];
			$mx = self::_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[0] = self::_int32($v[0] - $mx);
			$sum = self::_int32($sum - $delta);
		}

		return self::_long2str($v, true);
	}
}