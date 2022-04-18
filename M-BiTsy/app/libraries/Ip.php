<?php

class Ip
{
    // Check IP for ban and Redirect
    public static function checkipban($ip)
    {
        $nip = 0;
        $res = DB::raw('bans', '*', '');
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $banned = false;
            if (self::is_ipv6($row["first"]) && self::is_ipv6($row["last"]) && self::is_ipv6($ip)) {
                $row["first"] = self::ip2long6($row["first"]);
                $row["last"] = self::ip2long6($row["last"]);
                $banned = bccomp($row["first"], $nip) != -1 && bccomp($row["last"], $nip) != -1;
            } else {
                $row["first"] = ip2long($row["first"]);
                $row["last"] = ip2long($row["last"]);
                $banned = $nip >= $row["first"] && $nip <= $row["last"];
            }
            if ($banned) {
                header("HTTP/1.0 403 Forbidden");
                echo '<html><head><title>Forbidden</title> </head><body> <h1>Forbidden</h1>Unauthorized IP address.<br> </body></html>';
                die;
            }
        }
    }

    // IP Check
    public static function checkIP()
    {
        $ip = Ip::getIP();
        $ipq = get_row_count("users", "WHERE ip = '$ip'");
        if ($ipq >= Config::get('ACCOUNTMAX')) {
            Redirect::autolink(URLROOT . '/login', "This IP is already in use !");
        }
    }

    // IP Validation Function
    public static function validIP($ip)
    {
        if (strtolower($ip) === "unknown") {
            return false;
        }
        // generate ipv4 network address
        $ip = ip2long($ip);
        // if the ip is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // make sure to get unsigned long representation of ip due to discrepancies
            // between 32 and 64 bit OSes and signed numbers (ints default to signed in PHP)
            $ip = sprintf("%u", $ip);
            // do private network range checking
            if ($ip >= 0 && $ip <= 50331647) {
                return false;
            }
            if ($ip >= 167772160 && $ip <= 184549375) {
                return false;
            }
            if ($ip >= 2130706432 && $ip <= 2147483647) {
                return false;
            }
            if ($ip >= 2851995648 && $ip <= 2852061183) {
                return false;
            }
            if ($ip >= 2886729728 && $ip <= 2887778303) {
                return false;
            }
            if ($ip >= 3221225984 && $ip <= 3221226239) {
                return false;
            }
            if ($ip >= 3232235520 && $ip <= 3232301055) {
                return false;
            }
            if ($ip >= 4294967040) {
                return false;
            }
        }
        return true;
    }

    // Get Ip
    public static function getIP()
    {
        // Cloudflare
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            return $ip;
        }
        
        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validIP($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                foreach ($iplist as $ip) {
                    if (self::validIP($ip)) {
                        return $ip;
                    }
                }
            } else {
                if (self::validIP($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validIP($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validIP($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED']) && self::validIP($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }

    // Function For Verification If IP Address IPV6 Format
    public static function is_ipv6($s)
    {
        return is_int(strpos($s, ":"));
    }

    // Taken from php.net comments
    public static function ip2long6($ipv6)
    {
        $ip_n = inet_pton($ipv6);
        $bits = 15; // 16 x 8 bit = 128bit
        while ($bits >= 0) {
            $bin = sprintf("%08b", (ord($ip_n[$bits])));
            $ipv6long = $bin . $ipv6long;
            $bits--;
        }
        // Enable gmp extension
        return gmp_strval(gmp_init($ipv6long, 2), 10);
    }

    // Function To Convert An IP Address (IPv6) To A Digital IP Address
    public static function long2ip6($ipv6long)
    {
        $bin = gmp_strval(gmp_init($ipv6long, 10), 2);
        if (strlen($bin) < 128) {
            $pad = 128 - strlen($bin);
            for ($i = 1; $i <= $pad; $i++) {
                $bin = "0" . $bin;
            }
        }
        $bits = 0;
        while ($bits <= 7) {
            $bin_part = substr($bin, ($bits * 16), 16);
            $ipv6 .= dechex(bindec($bin_part)) . ":";
            $bits++;
        }
        // compress

        return inet_ntop(inet_pton(substr($ipv6, 0, -1)));
    }

    // Get user agent.
    public static function agent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    // Get OperatingSystem name.
    public static function operatingSystem()
    {
        $UserAgent = self::agent();
        if (preg_match_all('/windows/i', $UserAgent)) {
            $PlatForm = 'Windows';
        } elseif (preg_match_all('/linux/i', $UserAgent)) {
            $PlatForm = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $UserAgent)) {
            $PlatForm = 'Macintosh';
        } elseif (preg_match_all('/Android/i', $UserAgent)) {
            $PlatForm = 'Android';
        } elseif (preg_match_all('/iPhone/i', $UserAgent)) {
            $PlatForm = 'IOS';
        } elseif (preg_match_all('/ubuntu/i', $UserAgent)) {
            $PlatForm = 'Ubuntu';
        } else {
            $PlatForm = 'unknown';
        }

        return $PlatForm;
    }

    // Get Browser Name.
    public static function browser()
    {
        $UserAgent = self::agent();
        if (preg_match_all('/Edge/i', $UserAgent)) {
            $Browser = 'Microsoft Edge';
            $B_Agent = 'Edge';
        } elseif (preg_match_all('/MSIE/i', $UserAgent)) {
            $Browser = 'Mozilla Firefox';
            $B_Agent = 'Firefox';
        } elseif (preg_match_all('/OPR/i', $UserAgent)) {
            $Browser = 'Opera';
            $B_Agent = 'Opera';
        } elseif (preg_match_all('/Opera/i', $UserAgent)) {
            $Browser = 'Opera';
            $B_Agent = 'Opera';
        } elseif (preg_match_all('/Chrome/i', $UserAgent)) {
            $Browser = 'Google Chrome';
            $B_Agent = 'Chrome';
        } elseif (preg_match_all('/Safari/i', $UserAgent)) {
            $Browser = 'Apple Safari';
            $B_Agent = 'Safari';
        } elseif (preg_match_all('/firefox/i', $UserAgent)) {
            $Browser = 'Mozilla Firefox';
            $B_Agent = 'Firefox';
        } else {
            $Browser = null;
            $B_Agent = null;
        }

        return [
            'browser' => $Browser,
            'agent' => $B_Agent,
        ];
    }

    // Get Os version.
    public static function oSVersion()
    {
        $UserAgent = self::agent();
        if (preg_match_all('/windows nt 10/i', $UserAgent)) {
            $OsVersion = 'Windows 10';
        } elseif (preg_match_all('/windows nt 6.3/i', $UserAgent)) {
            $OsVersion = 'Windows 8.1';
        } elseif (preg_match_all('/windows nt 6.2/i', $UserAgent)) {
            $OsVersion = 'Windows 8';
        } elseif (preg_match_all('/windows nt 6.1/i', $UserAgent)) {
            $OsVersion = 'Windows 7';
        } elseif (preg_match_all('/windows nt 6.0/i', $UserAgent)) {
            $OsVersion = 'Windows Vista';
        } elseif (preg_match_all('/windows nt 5.1/i', $UserAgent)) {
            $OsVersion = 'Windows Xp';
        } elseif (preg_match_all('/windows xp/i', $UserAgent)) {
            $OsVersion = 'Windows Xp';
        } elseif (preg_match_all('/windows me/i', $UserAgent)) {
            $OsVersion = 'Windows Me';
        } elseif (preg_match_all('/win98/i', $UserAgent)) {
            $OsVersion = 'Windows 98';
        } elseif (preg_match_all('/win95/i', $UserAgent)) {
            $OsVersion = 'Windows 95';
        } elseif (preg_match_all('/Windows Phone +[0-9]/i', $UserAgent, $match)) {
            $OsVersion = $match;
        } elseif (preg_match_all('/Android +[0-9]/i', $UserAgent, $match)) {
            $OsVersion = $match;
        } elseif (preg_match_all('/Linux +x[0-9]+/i', $UserAgent, $match)) {
            $OsVersion = $match;
        }

        return $OsVersion;
    }

    // Get Browser version.
    public static function browserVersion()
    {
        $UserAgent = self::agent();
        $B_Agent = self::Browser()['agent'];
        if ($B_Agent !== null) {
            $known = ['Version', $B_Agent, 'other'];
            $pattern = '#(?<browser>' . implode('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
            if (!preg_match_all($pattern, $UserAgent, $matches)) {
            }
            $i = count($matches['browser']);
            if ($i != 1) {
                if (strripos($UserAgent, 'Version') < strripos($UserAgent, $B_Agent)) {
                    $Version = $matches['version'][0];
                } else {
                    $Version = $matches['version'][0];
                }
            } else {
                $Version = $matches['version'][0];
            }
        }

        return $Version;
    }

}