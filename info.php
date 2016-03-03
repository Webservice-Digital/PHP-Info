<?php
	/**
	 * phpinfo() in a pretty view
	 * 
	 * @author      SynCap < syncap@ya.ru >
	 * @copyright	(c)2009,2013,2015 Constantin Loskutov, www.syncap.ru
	 *
	 */

	define('PPI_VERSION', '2016-33');
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
<style>
body{background-color:#fff;color:#555;font-family:Calibri,Tahoma,sans-serif;font-size:14px;margin:0 auto 33%;max-width:950px}article{margin-left:180px;max-width:770px}h1{color:#379}header{background:#fff;border-radius:0 0 13px 13px;box-shadow:0 0 15px #777;padding:0 1em .5em 170px;position:fixed;top:0;width:790px;z-index:1}header h1{margin:0 2em 0 -1.6em;display:inline-block}header .topmenu{display:inline-block;margin:0;padding:0}header .topmenu #gold,header .topmenu a{color:#379;cursor:pointer;text-decoration:underline;text-decoration-line:dashed;font-style:normal;font-weight:100}header .topmenu li{display:inline-block;margin-right:1em}header .topmenu select{padding:.15em;margin:0;color:#379;border:1px solid;border-color:rgba(68,136,255,.1)}header .topmenu .btn{margin:0;width:1.5em;height:1.5em}nav#toc{bottom:0;font-size:13px;position:fixed;top:0;z-index:2}.php-logo{background:url(../img/b2.png);color:transparent;display:block;font-size:0;height:70px;margin:0 0 0 10px;text-decoration:none;width:120px;background-repeat:no-repeat;background-position:center}.hide{display:none}form .btn{border:none;color:#fff;padding:.1em;border-radius:.2em;cursor:pointer;font-size:1.2em;line-height:1em;background:rgba(0,68,136,.1)}.filterForm{border:1px solid rgba(0,68,136,.1);width:120px;margin:0 auto;white-space:nowrap}.filterForm .btn{width:1.2em;height:1.1em}.filterForm #filterText{font-size:1.2em;font-weight:700;border:none;width:95px;color:#379;text-align:center}.phpinfo-nav{bottom:0;padding-left:1.2em;position:fixed;top:70px;width:150px}.phpinfo-nav ul{overflow:auto;list-style:none;padding:.5em 0;margin:0;height:95%}.phpinfo-nav .shade{border:0;margin:0;padding:0;display:block;height:.5em;position:absolute;background:#fff}.phpinfo-nav .shade .top{box-shadow:0 0 10px #fff;top:0}.phpinfo-nav .shade .bottom{box-shadow:0 0 10px #fff;bottom:0}.phpinfo-nav a{color:#37c;padding:.05em .5em;text-decoration:none;display:inline-block}.phpinfo-nav a:hover{text-decoration:underline;text-decoration:dashed}.phpinfo-nav li{border-bottom:1px solid rgba(0,68,136,.1);padding:0 0}.phpinfo-nav li:nth-child(odd){background-color:rgba(68,136,255,.1)}.phpinfo-section{padding:5em 0 0 0}.phpinfo-section h2{color:#379;margin:0 0 0 -10px;position:relative}.phpinfo-section .mark{color:#444;display:inline-block;font-size:1.5em;opacity:.3;position:absolute;right:.5em;text-decoration:none;top:0}.phpinfo-section .mark:hover{opacity:1}.phpinfo-section table{border-collapse:collapse;margin:.5em auto;table-layout:auto;text-align:left}.phpinfo-section td{border-bottom:1px solid #39a;padding:.2em .5em;vertical-align:top}.phpinfo-section td:nth-child(1){font-weight:700;white-space:nowrap}.phpinfo-section td:nth-child(2){word-break:break-word}.phpinfo-section tr:nth-child(odd) td{background-color:#cdf}::-webkit-scrollbar{width:9px;height:9px}::-webkit-scrollbar-button{width:0;height:0}::-webkit-scrollbar-thumb{background:rgba(68,136,255,.1);border:0 none transparent;border-radius:50px}::-webkit-scrollbar-track{background:0 0;border:0 none #fff;border:1px solid rgba(68,136,255,.1);border-radius:50px}::-webkit-scrollbar-track:hover{background:rgba(68,136,255,.1)}::-webkit-scrollbar-track:active{background:rgba(68,136,255,.1)}::-webkit-scrollbar-corner{background:0 0}.golden h1{color:#c90}.golden header .topmenu #gold,.golden header .topmenu a{color:#c90}.golden header .topmenu select{color:#c90;border-color:rgba(204,153,0,.2)}.golden .php-logo{background-image:url(../img/g1.png)}.golden form .btn{background:rgba(204,153,0,.2)}.golden .filterForm{border-color:rgba(204,153,0,.3)}.golden .filterForm #filterText{color:#c90}.golden .phpinfo-nav{border-right-color:transparent}.golden .phpinfo-nav li{border-bottom-color:rgba(204,153,0,.3)}.golden .phpinfo-nav li:nth-child(odd){background-color:rgba(204,153,0,.2)}.golden .phpinfo-nav a{color:#c90}.golden .phpinfo-section h2{color:#c90}.golden .phpinfo-section .mark{color:#444}.golden .phpinfo-section td{border-bottom-color:#c90}.golden .phpinfo-section tr:nth-child(odd) td{background-color:rgba(204,153,0,.3)}.golden ::-webkit-scrollbar-thumb{background:rgba(204,153,0,.3)}.golden ::-webkit-scrollbar-track{border-color:rgba(204,153,0,.3)}.golden ::-webkit-scrollbar-track:hover{background:rgba(204,153,0,.3)}.golden ::-webkit-scrollbar-track:active{background:rgba(204,153,0,.3)}
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
						<option selected>Show native with…</option>
						<option value="-1">INFO_ALL</option>
						<option value="1" >INFO_GENERAL</option>
						<option value="2" >INFO_CREDITS</option>
						<option value="4" >INFO_CONFIGURATION</option>
						<option value="8" >INFO_MODULES</option>
						<option value="16">INFO_ENVIRONMENT</option>
						<option value="32">INFO_VARIABLES</option>
						<option value="64">INFO_LICENSE</option>
					</select>
					<button type="submit" class="btn">&#10151;</button>
				</form>
			</li>
			<li><a href="?do=update">v. <?php echo PPI_VERSION ?></a></li>
		</ul>
	</header>
	<article>
	<?php echo  $phpinfo->content ?>
	</article>

<!-- <script src="info.js"></script> -->
<script>
(function(a){function d(b,a){Array.prototype.forEach.call(b,a)}function e(b){return b.innerText||b.textContent}function g(){k.href=window.getComputedStyle(a.getElementsByClassName("php-logo")[0],null).getPropertyValue("background-image").match(/url\(("?)(.+)\1\)/)[2]}window.$=function(b){return a[{"#":"getElementById",".":"getElementsByClassName","@":"getElementsByName","=":"getElementsByTagName"}[b[0]]||"querySelectorAll"](b)};"true"===localStorage.getItem("phpInfoGold")&&a.body.classList.add("golden");
var k=a.getElementById("docIcon");g();a.getElementById("gold").addEventListener("click",function(b){a.body.classList.toggle("golden");localStorage.setItem("phpInfoGold",a.body.classList.contains("golden"));g()});var c=$("td:nth-child(2)"),l="Windows"===e(c[0]).match(/^\w+/)[0]?/[;,]/g:/[:,]/g;d(c,function(b,a){c[a].innerHTML=e(b).replace(l,"$& ")});var h=$(" .phpinfo-nav li"),f=$(" .phpinfo-section");a.getElementById("filterText").addEventListener("input",function(a){var c=new RegExp(a.target.value,
"i");d(h,function(a,b){0>e(a).search(c)?(a.classList.add("hide"),f[b].classList.add("hide")):(a.classList.remove("hide"),f[b].classList.remove("hide"))})});a.getElementsByClassName("filterForm")[0].addEventListener("reset",function(a){d(h,function(a,b){a.classList.remove("hide");f[b].classList.remove("hide")})});a.body.addEventListener("keyup",function(b){"Escape"===b.code&&a.getElementsByClassName("filterForm")[0].reset()})})(document);
</script>
</body>
</html>