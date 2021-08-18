<?php
session_start();
require(__DIR__.'/../helpers/security.php');
require_once('/var/www/api.gotravlon.com/html/vendor/autoload.php');
define("SITE_NAME", "goTravlon");
define("SITE_URL", "https://api.gotravlon.com");
define('SITE_URL_CLEAN', "gotravlon.com");
define("ADMIN_EMAIL", "marc@webearn.com");
define("ROOM_PAGINATION", 25);
define('URL_PATH', 'https://api.gotravlon.com');
define('FACEBOOK_APP_ID', '981943269246999');
define('FACEBOOK_APP_SECRET', '0a55c65b63832544e15aa18ba9ca00ad');
define('TRIPOSO_ACCOUNT', '12EQ8PXQ');
define('TRIPOSO_TOKEN', 'qs9xlko5ytk8jsrk3o0ewbsjgrqwz2sp');
define('TWITTER_ACCESS_TOKEN', '1397303523973472257-HV2ub31hrUsx0OAMaiqumYYN3XdJ9Y');     
define('TWITTER_ACCESS_TOKEN_SECRET', 'f6ursuh6f88rQeP7QeN08qDWS7mWwM7tfmEG9C1Cykvop');
define('CONSUMER_KEY', '8lNNROhXqwS6FR1E9AtekTdVM');
define('CONSUMER_SECRET', 'r9y3LJB5J25DQVsWQA8TCoaDnGnS4jIl5QhxjEGTOglAexiQyW');
define('API_HEADER_KEY', 'eLfzWQLQMrevBHrYC6naF');
$l_sDBHost = 'localhost';
$l_sDBUsername = 'travl_db';
$l_sDBPassword = 'L1Zab8BrltFoaBm5';
$l_sDBName = 'travl_db';



// require_once(__DIR__ . '/../vendor/autoload.php');

require(__DIR__.'/../core/App.php');
App::getInstance()->setConnectionDetails($l_sDBHost, $l_sDBUsername, $l_sDBPassword, $l_sDBName);
$con = App::getInstance()->getConnection();
// App::getInstance()->setMongoConnection();

//////////////////////////////////////////////////////

if (isset($_SERVER['REQUEST_URI'])) {
    $current_page_uri = $_SERVER['REQUEST_URI'];
}
$time = time();
// $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
// if($ip == ""){
if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
}
// }
$email_signature = "<BR><BR>-<BR>".SITE_NAME;
define('EMAIL_SIGNATURE', '<BR><BR>-<BR>'.SITE_NAME);

////////////////
// FUNCTIONS
function generateSalt($length = 10) {
    $salt = null;
    $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));

    for($i = 0; $i < $length; $i++) {
        $salt .= $salt_chars[array_rand($salt_chars)];
    }

    return $salt;
}

function time_ago($mtime)
{
$xtime = time() - $mtime;

if ($xtime < 60)
{
    return 'just now';
}

$a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
            );

foreach ($a as $secs => $str)
{
    $d = $xtime / $secs;
    if ($d >= 1)
    {
        $r = round($d);
        return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
    }
}}

function cleanUrl($title){
    $title_url = str_replace("&", "", $title);
    $title_url = str_replace("?", "", $title_url);
    $title_url = str_replace("#", "", $title_url);
    $title_url = str_replace("*", "", $title_url);
    $title_url = str_replace("^", "", $title_url);
    $title_url = str_replace("$", "", $title_url);
    $title_url = str_replace("@", "", $title_url);
    $title_url = str_replace("!", "", $title_url);
    $title_url = str_replace(">", "", $title_url);
    $title_url = str_replace("<", "", $title_url);
    $title_url = str_replace("%", "", $title_url);
    $title_url = str_replace("/", "", $title_url);
    $title_url = str_replace(" ", "-", $title_url);
    $title_url = strtolower($title_url);
    return $title_url;
}

function countdown_time($ending_time){
    $rem = $ending_time - time();
    $day = floor($rem / 86400);
    $hr  = floor(($rem % 86400) / 3600);
    $min = floor(($rem % 3600) / 60);
    $sec = ($rem % 60);
    $return_this = "";
    if($day) $return_this .= "$day days ";
    if($hr) $return_this .= "$hr hours ";
    if($min) $return_this .= "$min minutes ";
    if($sec) $return_this .= "$sec seconds ";
    return $return_this;
}

function makeClickableLinks($text)
{
    $text = html_entity_decode($text);
    $text = " ".$text;
    $text= preg_replace("/(^|[\n ])([\w]*?)([\w]*?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" target='_blank' >$3</a>", $text);
    $text= preg_replace("/(^|[\n ])([\w]*?)((www|wap)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" target='_blank' >$3</a>", $text);
    $text= preg_replace("/(^|[\n ])([\w]*?)((ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"$4://$3\" target='_blank' >$3</a>", $text);
    $text= preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\" target='_blank'>$2@$3</a>", $text);
    $text= preg_replace("/(^|[\n ])(mailto:[a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"$2@$3\" target='_blank'>$2@$3</a>", $text);
    $text= preg_replace("/(^|[\n ])(skype:[^ \,\"\t\n\r<]*)/i", "$1<a href=\"$2\">$2</a>", $text);
    return $text;
}

function randomString($length = 6) {
	$str = "";
	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}

function is_valid_domain_name($domain_name)
{
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
}

////////////////
// mail gun
define('MAILGUN_URL', 'https://api.mailgun.net/v3/email.socialr.com');
define('MAILGUN_KEY', '6895c3073f2c1b72124fec08f3b3c500-2ae2c6f3-a559e3cb');
function send_mail_by_mailgun($to,$toname,$mailfromname,$mailfrom,$subject,$html,$text,$tag,$replyto){
    $array_data = array(
        'from'=> $mailfromname .'<'.$mailfrom.'>',
        'to'=>$toname.'<'.$to.'>',
        'subject'=>$subject,
        'html'=>$html,
        'text'=>$text,
        'o:tracking'=>'no',
        'o:tracking-clicks'=>'no',
        'o:tracking-opens'=>'no',
        'o:tag'=>$tag,
        'h:Reply-To'=>$replyto
    );
    $session = curl_init(MAILGUN_URL.'/messages');
    curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($session, CURLOPT_USERPWD, 'api:'.MAILGUN_KEY);
    curl_setopt($session, CURLOPT_POST, true);
    curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($session);
    curl_close($session);
    $results = json_decode($response, true);
}

?>
