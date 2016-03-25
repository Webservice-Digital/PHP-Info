<?php
/**
 * phpinfo() in a pretty view
 * 
 * @author      SynCap < syncap@ya.ru >
 * @copyright	(c)2009,2013,2015 Constantin Loskutov, www.syncap.ru
 *
 */

define('PPI_VERSION', '2016-37');
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

	public $FILTER_FORM = '
<form action="" class="filterForm">
	<input type="text" id="filterText">
	<input type="reset" class="btn" value="&#10060;">
</form>
';

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
		$this->nav = "<div class=\"phpinfo-nav\">\n".$this->FILTER_FORM."\n<ul>\n$this->nav\n</ul></div>\n";
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
<link rel="stylesheet" href="css/info.css">
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

<script src="info.js"></script>
<script></script>
</body>
</html>