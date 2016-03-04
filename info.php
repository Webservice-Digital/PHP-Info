<?php
/**
 * phpinfo() in a pretty view
 * 
 * @author      SynCap < syncap@ya.ru >
 * @copyright	(c)2009,2013,2015 Constantin Loskutov, www.syncap.ru
 *
 */

define('PPI_VERSION', '2016-34');
define('PPI_GITHUB_SOURCE_PATH', 'https://raw.githubusercontent.com/SynCap/PHP-Info/master/info.php');

/*

  Very old school mini router for some additional commands

  native - launch native phpinfo($mode) with `mode` as another GET param
  update - get fresh version from GitHub and launch it

*/
if (isset($_GET['do'])) {

	switch ($_GET['do']) {
		case 'native':
			/*
				If we need, we can call native phpinfo(). In this case we don't need any other porcessings.
				Just do it, then die. ;)

				INFO_GENERAL        1
				INFO_CREDITS        2
				INFO_CONFIGURATION  4
				INFO_MODULES        8
				INFO_ENVIRONMENT   16
				INFO_VARIABLES     32
				INFO_LICENSE       64
				INFO_ALL           -1

				if requested mode is NOT integer in range of INFO_GENERAL..INFO_LICENSE, 
				for example some textual or non-legal integer, we assume default value INFO_ALL
			*/
			$mode = 0+$_GET['mode'] & 64 > 0 
				?$_GET['mode'] 
				: -1 ;
			phpinfo($mode);
			die();
			break;
		
		case 'update':
			$remoteSource = @file_get_contents(PPI_GITHUB_SOURCE_PATH);
			if (
					($remoteSource !== FALSE)
					&&
					(file_put_contents(__FILE__, $remoteSource ) !== FALSE)
				) {
				header('Location: '.$_SERVER["SCRIPT_NAME"]);
				exit('Starting with updated version');
			}
			;
			break;
	}

}

class prettyPhpInfo
{
	public $nav = "";
	public $content = "";
	public $info_arr = array();

	const FILTER_FORM = <<<'FLTF'
<form action="" class="filterForm">
	<input type="text" id="filterText">
	<input type="reset" class="btn" value="&#10060;">
</form>
FLTF;

	/**
	 * Grep the all info from built-in PHP function
	 */
	protected function phpinfo_array() {
		ob_start();
		phpinfo(INFO_GENERAL);
		phpinfo(INFO_CONFIGURATION);
		phpinfo(INFO_ENVIRONMENT);
		phpinfo(INFO_VARIABLES);
		phpinfo(INFO_MODULES);
		$info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
		$cat = "General";
		foreach($info_lines as $line) {
			// new cat?
			preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
			if
				(
					preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)
					OR
					preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)
				)
					$info_arr[$cat][$val[1]] = str_replace(';','; ', $val[2]);// 2016: the same made in JS, but better: on non Windows servers replaced `:` but not `;`
		}
        return $info_arr;
	}

	function __construct() {
		$this->info_arr = $this->phpinfo_array();
		foreach( $this->info_arr as $cat=>$vals ) {
			$catID = str_replace(' ', '_', $cat);
			// add navigation pane item
			$this->nav .= "<li><a href=\"#$catID\">$cat</a></li>";
			// add a section to main page
			// Q: Why not use original tables?
			// A: Because we need an our own structure, IDs and classes. 
			//    We need an ability to show exact section on startup, we need a headers separated from tables, and so on...
			$this->content .= "<section id=\"$catID\" class=\"phpinfo-section\">\n<h2>$cat <a class=\"mark\" href=\"#$catID\">#</a></h2>\n<table>\n<tbody>\n";
			foreach($vals as $key=>$val) {
				$this->content .= "<tr><td>$key</td><td>$val</td>\n";
			}
			$this->content .= "</tbody>\n</table>\n</section>\n";
		}
		$this->nav = "<div class=\"phpinfo-nav\">\n".$this::FILTER_FORM."\n<ul>\n$this->nav\n</ul></div>\n";
		return $this;
	}
}

$phpinfo = new prettyPhpInfo();
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<title>PHP info :: <?php echo  $_SERVER['HTTP_HOST'].' – '.PHP_VERSION ?></title>
<link rel="shortcut icon" id="docIcon" type="image/png">
<style type="text/css">
body{background-color:#fff;color:#555;font-family:Calibri,Tahoma,sans-serif;font-size:14px;margin:0 auto 33%;max-width:950px}
article{margin-left:180px;max-width:770px}
h1{color:#379}
header{background:#fff;border-radius:0 0 13px 13px;box-shadow:0 0 15px #777;padding:0 1em .5em 170px;position:fixed;top:0;width:790px;z-index:1}
header h1{margin:0 2em 0 -1.6em;display:inline-block}
header .topmenu{display:inline-block;margin:0;padding:0}
header .topmenu #gold,header .topmenu a{color:#379;cursor:pointer;text-decoration:underline;text-decoration-line:dashed;font-style:normal;font-weight:100}
header .topmenu li{display:inline-block;margin-right:1em}
header .topmenu select{padding:.15em;margin:0;color:#379;border:1px solid;border-color:rgba(68,136,255,.1)}
header .topmenu .btn{margin:0;width:1.5em;height:1.5em}
nav#toc{bottom:0;font-size:13px;position:fixed;top:0;z-index:2}
.php-logo{background:url(data:image/png;base64,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);
	color:transparent;display:block;font-size:0;height:70px;margin:0 0 0 10px;text-decoration:none;width:120px;background-repeat:no-repeat;background-position:center}
.hide{display:none}
form .btn{border:none;color:#fff;padding:.1em;border-radius:.2em;cursor:pointer;font-size:1.2em;line-height:1em;background:rgba(0,68,136,.1)}
.filterForm{border:1px solid rgba(0,68,136,.1);width:120px;margin:0 auto;white-space:nowrap}
.filterForm .btn{width:1.2em;height:1.1em}
.filterForm #filterText{font-size:1.2em;font-weight:700;border:none;width:95px;color:#379;text-align:center}
.phpinfo-nav{bottom:0;padding-left:1.2em;position:fixed;top:70px;width:150px}
.phpinfo-nav ul{overflow:auto;list-style:none;padding:.5em 0;margin:0;height:95%}
.phpinfo-nav .shade{border:0;margin:0;padding:0;display:block;height:.5em;position:absolute;background:#fff}
.phpinfo-nav .shade .top{box-shadow:0 0 10px #fff;top:0}
.phpinfo-nav .shade .bottom{box-shadow:0 0 10px #fff;bottom:0}
.phpinfo-nav a{color:#37c;padding:.05em .5em;text-decoration:none;display:inline-block}
.phpinfo-nav a:hover{text-decoration:underline;text-decoration:dashed}
.phpinfo-nav li{border-bottom:1px solid rgba(0,68,136,.1);padding:0 0}
.phpinfo-nav li:nth-child(odd){background-color:rgba(68,136,255,.1)}
.phpinfo-section{padding:5em 0 0 0}
.phpinfo-section h2{color:#379;margin:0 0 0 -10px;position:relative}
.phpinfo-section .mark{color:#444;display:inline-block;font-size:1.5em;opacity:.3;position:absolute;right:.5em;text-decoration:none;top:0}
.phpinfo-section .mark:hover{opacity:1}
.phpinfo-section table{border-collapse:collapse;margin:.5em auto;table-layout:auto;text-align:left}
.phpinfo-section td{border-bottom:1px solid #39a;padding:.2em .5em;vertical-align:top}
.phpinfo-section td:nth-child(1){font-weight:700;white-space:nowrap}
.phpinfo-section td:nth-child(2){word-break:break-word}
.phpinfo-section tr:nth-child(odd) td{background-color:#cdf}
::-webkit-scrollbar{width:9px;height:9px}
::-webkit-scrollbar-button{width:0;height:0}
::-webkit-scrollbar-thumb{background:rgba(68,136,255,.1);border:0 none transparent;border-radius:50px}
::-webkit-scrollbar-track{background:0 0;border:0 none #fff;border:1px solid rgba(68,136,255,.1);border-radius:50px}
::-webkit-scrollbar-track:hover{background:rgba(68,136,255,.1)}
::-webkit-scrollbar-track:active{background:rgba(68,136,255,.1)}
::-webkit-scrollbar-corner{background:0 0}
.golden h1{color:#c90}
.golden header .topmenu #gold,.golden header .topmenu a{color:#c90}
.golden header .topmenu select{color:#c90;border-color:rgba(204,153,0,.2)}
.golden .php-logo{background-image:url(data:image/png;base64,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)}
.golden form .btn{background:rgba(204,153,0,.2)}
.golden .filterForm{border-color:rgba(204,153,0,.3)}
.golden .filterForm #filterText{color:#c90}
.golden .phpinfo-nav{border-right-color:transparent}
.golden .phpinfo-nav li{border-bottom-color:rgba(204,153,0,.3)}
.golden .phpinfo-nav li:nth-child(odd){background-color:rgba(204,153,0,.2)}
.golden .phpinfo-nav a{color:#c90}
.golden .phpinfo-section h2{color:#c90}
.golden .phpinfo-section .mark{color:#444}
.golden .phpinfo-section td{border-bottom-color:#c90}
.golden .phpinfo-section tr:nth-child(odd) td{background-color:rgba(204,153,0,.3)}
.golden ::-webkit-scrollbar-thumb{background:rgba(204,153,0,.3)}
.golden ::-webkit-scrollbar-track{border-color:rgba(204,153,0,.3)}
.golden ::-webkit-scrollbar-track:hover{background:rgba(204,153,0,.3)}
.golden ::-webkit-scrollbar-track:active{background:rgba(204,153,0,.3)}
</style>
</head>
<body>
	<nav id="toc">
		<a href="<?php echo  $_SERVER['PHP_SELF'] ?>" title="" class="php-logo">Renew</a>
		<?php echo  $phpinfo->nav ?>
	</nav>
	<header>
		<h1>v.<?php echo  PHP_VERSION ?> </h1>
		<ul class="topmenu">
			<li><em id="gold">Colors</em></li>
			<li>
				<form action="" method="GET" id="formShowNative">					
					<input type="hidden" name="do" value="native">
					<select name="mode" id="nativeMode">
						<optgroup>
							<option selected>Show native with…</option>
						</optgroup>
						<optgroup>
							<option value="1" >INFO_GENERAL</option>
							<option value="2" >INFO_CREDITS</option>
							<option value="4" >INFO_CONFIGURATION</option>
							<option value="8" >INFO_MODULES</option>
							<option value="16">INFO_ENVIRONMENT</option>
							<option value="32">INFO_VARIABLES</option>
							<option value="64">INFO_LICENSE</option>
						</optgroup>
						<optgroup>
							<option value="-1">INFO_ALL</option>
						</optgroup>
					</select>
					<button type="submit" class="btn">&#10151;</button>
				</form>
			</li>
			<li><a href="?do=update" title="Force update from GitHub">v.<?= PPI_VERSION ?></a></li>
		</ul>
	</header>
	<article>
	<?php echo  $phpinfo->content ?>
	</article>

<script>
(function(b){function c(a,b){Array.prototype.forEach.call(a,b)}function d(a){return a.innerText||a.textContent}function g(){k.href=window.getComputedStyle(b.getElementsByClassName("php-logo")[0],null).getPropertyValue("background-image").match(/url\(("?)(.+)\1\)/)[2]}window.$=function(a){return b[{"#":"getElementById",".":"getElementsByClassName","@":"getElementsByName","=":"getElementsByTagName"}[a[0]]||"querySelectorAll"](a)};"true"===localStorage.getItem("phpInfoGold")&&b.body.classList.add("golden");
var k=b.getElementById("docIcon");g();b.getElementById("gold").addEventListener("click",function(a){b.body.classList.toggle("golden");localStorage.setItem("phpInfoGold",b.body.classList.contains("golden"));g()});var e=$("td:nth-child(2)"),l="Windows"===d(e[0]).match(/^\w+/)[0]?/[;,]/g:/[:,]/g;c(e,function(a,b){e[b].innerHTML=d(a).replace(l,"$& ")});var h=$(" .phpinfo-nav li"),f=$(" .phpinfo-section");b.getElementById("filterText").addEventListener("input",function(a){var b=new RegExp(a.target.value,
"i");c(h,function(a,c){0>d(a).search(b)?(a.classList.add("hide"),f[c].classList.add("hide")):(a.classList.remove("hide"),f[c].classList.remove("hide"))})});b.getElementsByClassName("filterForm")[0].addEventListener("reset",function(a){c(h,function(a,b){a.classList.remove("hide");f[b].classList.remove("hide")})});b.body.addEventListener("keyup",function(a){"Escape"===a.code&&b.getElementsByClassName("filterForm")[0].reset()});nativeMode.addEventListener("change",function(a){formShowNative.submit()})})(document);
</script>
</body>
</html>