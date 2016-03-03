# ![][php-info-logo-8] Pretty PHPinfo tool



## Purposes

This one page admin tool was created to help manage several virtual hosting plans with different setting of PHP.

This tool can be easy embedded into any PHP-driven CMS, which in fact was done by me in some projects.

For easy reading and comparing settings on different hosting was done:

### 2002-2008
- single PHP file
- visual sectioning
- section navigation pane
- PHP 4 support
- easy reading colors in 2 stylesheet with embedded base64 graphics
- visual differences when comparing settings on 2 different hostings by using color schemes; see [screenshots](screenshots/)
- NO javascript
- additional non-standard info
- no credits and module descriptions, just technical info by key-value

### 2013
- PHP5 OOP - compact file, easy reuse of PHPinfo code, PHP4 support was gone as unneccessary
- LESS sources for stylesheets
- 2 ready to use PHP files with different stylesheets embedded
- styles optimized for Chrome and Firefox on tablets
- dropped MySQL info and other non-standard info

> to use with PHP4 take a look at `info-2008.php` in `dev` folder

### 2016

- stylesheets combined to single LESS to easy switch style by changing `class` of `BODY` or any another container
- JS added by following reasons: 
	
	1. optimization for browsers I'm use, 
	2. possibility to quick change of a look, including FavIcon, 
	3. possibility to quick modification of data on client side with DevTools
	4. quick search/filters - as modern UI\UX trends: I've become old and lazy, I want press 1 key, but don't scroll :)
	5. remember choosed color scheme in browser, not hosting
	6. just ONE file to deploy

- no 3d party JS libraries are used
- dropped support for IE < 10
- dropped CSS hacks

## Installation

Just copy `info.php` to your virtual hosting by FTP or any another way into directory wich can be reachable by http access.

> **NOTE** check permissions of directory: 
	1) on *NIX hosting file must be written with `644` at least
	2) php scripts must be allowed to launch in this directory (check `.htaccess` files)
	3) directory must be under DOCUMET_ROOT

Strongly recommended to place this file in restricted area of your site or delete them when it become unneccessary: 
PHP info gives large amount system info, which can be used 3d persons to bring lot of hedache to you and lot of destruction to your site.

## Known issues

### FavIcon not supported in IE

Because they do not support PNG icons properly, 
and I don't use IE in everyday task. I see no reasons to prepare and embed `ICO` files. 
Icons in this tools taken from the PNG logos wich (only 2 images) are embedded into CSS.


### Some lacks in old and mobile browsers

Current version did not tested with IE < 11, and any other browsers published before December 2015.

This version is not intended to use through devices with small screens (less than 1024 pixels 
in horizontal dimension and less than 500 px in vertical).

### No subheaders in some tables

Yes, I know. When I'll need it, I'll fix this. If you really need it - modify code at line 39 and 41
where Regular Expressions are reside. You may want to catch `tr` with `class="h"` to determine the subheader.

Check line 60, where new tables are builts.

### Only 2 columns some 3 column tables is retrived

Are you really need it? If so, modify code if regular expressions at line 39 and 41. You may need to catch all 3 values,
and at line 43 they can be added to array, that keep all gathered values.

Check line 60, where new tables are builts.
	
## License

Distribution granted with BSD-style [license](LICENSE.TXT)

[php-info-logo-8]:dev/img/php-info-blue-8.png