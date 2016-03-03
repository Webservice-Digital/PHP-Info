<?php
/**
* (c)2013,Constantin Loskutov,www.syncap.ru
*
* phpinfo in pretty view
*/
function phpinfo_array() {
	$info_arr = array();
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
		preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;if
			(
				preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)
				OR
				preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)
			)
				$info_arr[$cat][$val[1]] = str_replace(';','; ', $val[2]);
	}
	return $info_arr;
}

function ptyPInfo() {
	$nav = "";
  $content = "";
	$info_arr = phpinfo_array();
  foreach( $info_arr as $cat=>$vals ) {
    $catID = str_replace(' ', '_', $cat);
		$nav .= "<li><a href=\"#$catID\">$cat</a></li>";
		$content .= "<section id=\"$catID\" class=\"phpinfo-section\">\n<h2>$cat <a class=\"mark\" href=\"#$catID\">^</a></h2>\n<table>\n<tbody>\n";
		foreach($vals as $key=>$val) {
			$content .= "<tr><td>$key</td><td>$val</td>\n";
		}
		$content .= "</tbody>\n</table>\n</section>\n";
	}
	$nav = "<ul class=\"phpinfo-nav\">\n$nav\n</ul>\n";
	return array('nav'=>$nav, 'content'=>$content);
}

$phpinfo = ptyPInfo();
?><!DOCTYPE html>
<html>
<head>
<meta content="charset:utf-8"/>
<title>PHP info - <?php echo  $_SERVER['HTTP_HOST'] ?></title>
<style type="text/css">
body{width:950px;margin:0 auto;color:#555;background-color:#fff;font-size:14px}article{width:770px;margin-left:180px;}
header{position:fixed;top:0;background:#fff;width:790px;box-shadow:inset 0 0 3px #aaa,0 10px 10px #eee;padding:0 1em .5em 150px;border-radius:0 0 13px 13px;z-index:1}h1{margin:0;color:#c90}h2{color:#b80}nav{position:fixed;top:0;bottom:0;width:200px;font-size:12px;z-index:2;}
.php-logo{display:block;width:120px;height:67px;margin:0 0 0 15px;border:none;font-size:0px;color:transparent;text-decoration:none;}
.phpinfo-nav{position:fixed;top:70px;bottom:0;overflow:auto;margin:0;padding:0;padding-left:1.2em}
.phpinfo-nav{list-style:none;padding:.3em 0;border-right:1px solid #ec7;color:#c90}
.phpinfo-nav a{color:#c90;text-decoration:none;padding:.2em}
.phpinfo-nav li{padding:.1em 0;border-bottom:1px solid rgba(204,153,0,0.33)}
.phpinfo-nav li:nth-child(odd){background-color:rgba(204,153,0,0.2)}
.phpinfo-section{padding:4em 0 0 50px;}
.phpinfo-section h2{margin:0 0 0 -50px;position:relative;}
.phpinfo-section .mark{font-size:1.5em;position:absolute;display:inline-block;top:0;right:.5em;opacity:.3;text-decoration:underline;color:#444;}
.phpinfo-section .mark:hover{opacity:1}
.phpinfo-section table{table-layout:auto;border-collapse:collapse;margin:.5em auto;text-align:left}
.phpinfo-section td{padding:.2em .5em;border-bottom:1px solid #c90;vertical-align:top}
.phpinfo-section td:nth-child(1){white-space:nowrap;font-weight:700;color:#750;}
.phpinfo-section tr:nth-child(odd) td{background-color:rgba(204,153,0,0.3)}
</style>
</head>
<body>
  <nav>
		<a href="<?php echo  $_SERVER['PHP_SELF'] ?>" title="" class="php-logo"><img border="0" src="php-logo-gold.png" alt="PHP Logo"></a>
	  <?php echo  $phpinfo['nav'] ?>
  </nav>
	<header>
		<h1>v.<?php echo  PHP_VERSION ?> </h1>
	</header>
  <article>
  <?php echo  $phpinfo['content'] ?>
  </article>

</body>
</html>