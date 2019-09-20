<?php

namespace App\Http\Middleware;

use Closure;

class Illumina
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public static function CreateUniqueDateTimeKey($add = 0)
    {
        $dateNow = date_parse(gmdate(DATE_W3C));
        $y = $dateNow["year"];
        $M = $dateNow["month"];
        $d = $dateNow["day"];
        $h = $dateNow["hour"];
        $m = $dateNow["minute"] - $add;

        $key =  $y . $M . $d . $h . $m;
        return Illumina::GenerateIlluminaHash($key);
    }


    public static function GenerateRandKey($length = 10)
    {
        $keychar = [];
        $startingChar = 'A';
        for ($i = 0; $i < $length; $i++) {
            if (rand(0, 1) == 1) {
                $startingChar = 'a';
            }
            $keychar[$i] = chr(hexdec(bin2hex($startingChar)) + rand(0, 25));
        }
        return implode($keychar);
    }


    public static function GenerateIlluminaHash($msg)
    {
        $keystring = Illumina::GenerateRandKey(strlen($msg));
        $msgArray = str_split($msg);
        $cipheredMsg = [];
        for ($i = 0; $i < strlen($msg); $i++) {
            $cipheredMsg[($i * 2)] = $msgArray[strlen($msg) - 1 - $i];
            $cipheredMsg[($i * 2) + 1] = $msgArray[$i];
        }
        $hashedKeyString = sha1($keystring);
        $hashedCipheredMsg = md5(implode($cipheredMsg));
        $illumina_hash = [];
        for ($j = 0; $j < strlen($hashedCipheredMsg); $j++) {
            $illumina_hash[$j * 2] = $hashedKeyString[$j];
            $illumina_hash[($j * 2) + 1] = $hashedCipheredMsg[$j];
        }
        return implode($illumina_hash);
    }

    public static function CompareIlluminaHashes($leftHash, $rightHash)
    {
        $len = (strlen($leftHash) / 2);
        $leftHash = str_split($leftHash);
        $rightHash = str_split($rightHash);
        $leftOriginalHash = [];
        $rightOriginalHash = [];
        for ($i = 0; $i < $len; $i++) {
            $leftOriginalHash[$i] = $leftHash[($i * 2) + 1];
            $rightOriginalHash[$i] = $rightHash[($i * 2) + 1];
        }

        if (!strcmp(implode($leftOriginalHash), implode($rightOriginalHash))) {
            return true;
        }
        return false;
    }


    public static function atoi($char)
    {
        return hexdec(bin2hex($char));
    }
    public static function VigenereCipher($msg, $keyword, $encryption = true)
    {
        $factor = $encryption == true ? 1 : -1;

        $len = strlen($msg);
        $msg = str_split($msg);
        $j = 0;
        $key = "";
        $encrypted = "";
        while ($j < $len) {
            $key .= $keyword;
            $j += strlen($keyword);
        }
        $key = substr($key, 0, $len);
        $key = str_split($key);

        for ($i = 0; $i < $len; $i++) {
            $temp = Illumina::atoi($msg[$i]) - 65;
            $temp += ((Illumina::atoi($key[$i]) - 65) * $factor);

            $condition = $encryption == true ? $temp > 25 : $temp < 0;
            $temp = $condition == true ? $temp - (26 * $factor) : $temp;
            $encrypted .= chr($temp + 65);
        }

        return $encrypted;
    }



    public static function IlluminaCipherEncrypt($msg)
    {
        $randomKey = GenerateRandKey(strlen($msg) * 4);
        $keywords = ["HAVING", "FUN", "WITH", "ILLUMINA"];
        $cipheredParts = [];
        $keys = array([], [], [], []);
        $charArray = str_split($msg);
        $a = 0;
        $b = 0;

        for ($i = 0; $i < strlen($msg); $i++) {
            $a = 0;
            $b = 0;
            $temp = Illumina::atoi($charArray[$i]) - 32;
            $temp_part = $temp / 4;
            $temp_parts_excess = $temp % 4;

            if ($i % 2 == 0) {
                $a = $temp_parts_excess;
            } else {
                $b = $temp_parts_excess;
            }
            $keys[0][$i] = chr($temp_part + $a + 65);
            $keys[1][$i] = chr($temp_part + 65);
            $keys[2][$i] = chr($temp_part + 65);
            $keys[3][$i] = chr($temp_part + $b + 65);
        }

        for ($j = 0; $j < 4; $j++) {
            array_push($cipheredParts, implode($keys[$j]));
            $cipheredParts[$j] = Illumina::VigenereCipher($cipheredParts[$j], $keywords[$j], true);
        }

        $cipheredString = $cipheredParts[0] . $cipheredParts[1] . $cipheredParts[2] . $cipheredParts[3];
        $cipheredMsg = [];
        for ($k = 0; $k < strlen($cipheredString); $k++) {
            array_push($cipheredMsg, str_split($randomKey)[$k]);
            array_push($cipheredMsg, str_split($cipheredString)[$k]);
        }

        return implode($cipheredMsg);
    }

    public static function IlluminaCipherDecrypt($raw)
    {
        $originalDecipheredString = "";
        $keywords = ["HAVING", "FUN", "WITH", "ILLUMINA"];
        $len = strlen($raw);
        $raw = str_split($raw);
        $originalDecipheredChar = [];
        $decipheredParts = [];
        for ($i = 0; $i < ($len / 2); $i++) {
            $originalDecipheredChar[$i] = $raw[($i * 2) + 1];
        }
        $originalDecipheredString = implode($originalDecipheredChar);
        $leng = strlen($originalDecipheredString) / 4;
        for ($i = 0; $i < 4; $i++) {
            array_push($decipheredParts, substr($originalDecipheredString, $i * $leng, $leng));
            $decipheredParts[$i] = Illumina::VigenereCipher($decipheredParts[$i], $keywords[$i], false);
        }
        $decipheredChar = [];
        for ($j = 0; $j < $leng; $j++) {
            $sum = 0;
            for ($k = 0; $k < 4; $k++) {
                $sum += Illumina::atoi(str_split($decipheredParts[$k])[$j]) - 65;
            }
            $sum += 32;
            $decipheredChar[$j] = chr($sum);
        }

        return implode($decipheredChar);
    }
}
