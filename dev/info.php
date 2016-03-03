<?php
/**
 * phpinfo() in a pretty view
 * 
 * @author      SynCap < syncap@ya.ru >
 * @copyright	(c)2009,2013,2015 Constantin Loskutov, www.syncap.ru
 *
 */
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
<link rel="stylesheet" href="css/info.css">
<link rel="shortcut icon" id="docIcon" type="image/png" href="img/b2.png">
</head>
<body>
  <nav>
	<a href="<?php echo  $_SERVER['PHP_SELF'] ?>" title="" class="php-logo">Renew</a>
	<?php echo  $phpinfo->nav ?>
  </nav>
	<header>
		<h1>v.<?php echo  PHP_VERSION ?> <em id="gold">Gold</em></h1>
		
	</header>
  <article>
  <?php echo  $phpinfo->content ?>
  </article>

<script src="info.js"></script>
<script></script>
</body>
</html>