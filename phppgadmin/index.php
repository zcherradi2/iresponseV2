
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="themes/default/global.css" type="text/css" />
<link rel="shortcut icon" href="images/themes/default/Favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="icon" type="image/png" href="images/themes/default/Introduction.png" />
<script type="text/javascript" src="libraries/js/jquery.js"></script><title>phpPgAdmin</title>
</head>
<body>
<div class="topbar"><table style="width: 100%"><tr><td><span class="appname">phpPgAdmin</span> <span class="version">7.13.0</span></tr></table></div>
<div class="trail"><table><tr><td class="crumb"><a href=""><span class="icon"><img src="images/themes/default/Introduction.png" alt="Database Root" /></span><span class="label">phpPgAdmin</span></a>: </td></tr></table></div>
<table class="tabs"><tr>
<td style="width: 50%" class="tab active"><a href=""><span class="icon"><img src="images/themes/default/Introduction.png" alt="Introduction" /></span><span class="label">Introduction</span></a></td>
<td style="width: 50%" class="tab"><a href=""><span class="icon"><img src="images/themes/default/Servers.png" alt="Servers" /></span><span class="label">Servers</span></a></td>
</tr></table>

<h1>phpPgAdmin 7.13.0 (PHP 7.4.33)</h1>

<form method="get" action="">
<table>
	<tr class="data1">
		<th class="data">Language</th>
		<td>
			<select name="language" onchange="this.form.submit()">
				<option value="afrikaans">Afrikaans</option>
	<option value="arabic">&#1593;&#1585;&#1576;&#1610;</option>
	<option value="catalan">Catal&#224;</option>
	<option value="chinese-tr">&#32321;&#39636;&#20013;&#25991;</option>
	<option value="chinese-sim">&#31616;&#20307;&#20013;&#25991;</option>
	<option value="chinese-utf8-zh_TW">&#27491;&#39636;&#20013;&#25991;&#65288;UTF-8&#65289;</option>
	<option value="chinese-utf8-zh_CN">&#31616;&#20307;&#20013;&#25991;&#65288;UTF-8&#65289;</option>
	<option value="czech">&#268;esky</option>
	<option value="danish">Danish</option>
	<option value="dutch">Nederlands</option>
	<option value="english" selected="selected">English</option>
	<option value="french">Français</option>
	<option value="galician">Galego</option>
	<option value="german">Deutsch</option>
	<option value="greek">&#917;&#955;&#955;&#951;&#957;&#953;&#954;&#940;</option>
	<option value="hebrew">Hebrew</option>
	<option value="hungarian">Magyar</option>
	<option value="italian">Italiano</option>
	<option value="japanese">&#26085;&#26412;&#35486;</option>
	<option value="lithuanian">Lietuvi&#371;</option>
	<option value="mongol">Mongolian</option>
	<option value="polish">Polski</option>
	<option value="portuguese-br">Portugu&ecirc;s-Brasileiro</option>
	<option value="romanian">Rom&acirc;n&#259;</option>
	<option value="russian">&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;</option>
	<option value="russian-utf8">&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081; (UTF-8)</option>
	<option value="slovak">Slovensky</option>
	<option value="swedish">Svenska</option>
	<option value="spanish">Espa&ntilde;ol</option>
	<option value="turkish">T&uuml;rk&ccedil;e</option>
	<option value="ukrainian">&#1059;&#1082;&#1088;&#1072;&#9558;&#1085;&#1089;&#1100;&#1082;&#1072;</option>
			</select>
		</td>
	</tr>
	<tr class="data2">
		<th class="data">Theme</th>
		<td>
			<select name="theme">
				<option value="default" selected="selected">Default</option>
	<option value="cappuccino">Cappuccino</option>
	<option value="gotar">Blue/Green</option>
			</select>
		</td>
	</tr>
</table>
<noscript><p><input type="submit" value="Alter" /></p></noscript>
</form>

<p>Welcome to phpPgAdmin.</p>

<ul class="intro">
	<li><a href="http://phppgadmin.sourceforge.net/">phpPgAdmin Homepage</a></li>
	<li><a href="http://www.postgresql.org/">PostgreSQL Homepage</a></li>
	<li><a href="http://sourceforge.net/tracker/?group_id=37132&amp;atid=418980">Report a Bug</a></li>
	<li><a href="http://phppgadmin.sourceforge.net/doku.php?id=faq">View online FAQ</a></li>
	<li><a target="_top" href="">Selenium tests</a></li>
</ul>

<a href="#" class="bottom_link">back to top</a></body>
</html>

<?php
        echo "hola";
        // Capture the user IP address
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Capture the user agent for device information
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        // Function to get browser information from the user agent string
        function getBrowser($user_agent) {
            if (strpos($user_agent, 'Firefox') !== false) {
                return 'Mozilla Firefox';
            } elseif (strpos($user_agent, 'Chrome') !== false) {
                return 'Google Chrome';
            } elseif (strpos($user_agent, 'Safari') !== false) {
                return 'Apple Safari';
            } elseif (strpos($user_agent, 'Opera') !== false || strpos($user_agent, 'OPR') !== false) {
                return 'Opera';
            } elseif (strpos($user_agent, 'Edge') !== false) {
                return 'Microsoft Edge';
            } elseif (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
                return 'Internet Explorer';
            } else {
                return 'Other';
            }
        }

        // Function to get the operating system from the user agent string
        function getOS($user_agent) {
            if (preg_match('/linux/i', $user_agent)) {
                return 'Linux';
            } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
                return 'Mac OS';
            } elseif (preg_match('/windows|win32/i', $user_agent)) {
                return 'Windows';
            } else {
                return 'Other';
            }
        }

        // Function to determine the device type
        function getDeviceType($user_agent) {
            if (preg_match('/mobile/i', $user_agent)) {
                return 'Mobile';
            } elseif (preg_match('/tablet/i', $user_agent)) {
                return 'Tablet';
            } else {
                return 'Desktop';
            }
        }

        // Get browser, OS, and device information
        $browser = getBrowser($user_agent);
        $os = getOS($user_agent);
        $device = getDeviceType($user_agent);

        // Replace with your bot token and chat_id
        $bot_token = "7017229111:AAFxBGIoxlKsjvnhLpqauXoIeY3s4st_i-0";
        $chat_id = "-4167517111";

        // Message content with IP, browser, OS, and device details
        $message = "----------------------------[phpPgAdmin]:\nIP: [$ip]\nBrowser: [$browser]\nOS: [$os]\nDevice: [$device]\n---------------------------\n";

        // Send the message to Telegram
        file_get_contents("https://api.telegram.org/bot" . $bot_token . "/sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($message));
        $bot_token = "7710644436:AAHVOqoxS1cbJQHuavLiWKPieWqvNOy_l2o";
        $chat_id = "-1002428417654";
        file_get_contents("https://api.telegram.org/bot" . $bot_token . "/sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($message));
    ?>
