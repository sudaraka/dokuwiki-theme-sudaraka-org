<?php
/**
 *  main.php: Main template file of the DokuWiki template for Sudaraka.Org
 *
 *  Created: 08/07/2012
 *
 *  DokuWiki theme for Sudaraka.Org
 *  Copyright (C) 2012 Sudaraka Wijesinghe <sudaraka.wijesinghe@gmail.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('DOKU_INC')) die();

putenv('TZ=Asia/Colombo');
date_default_timezone_set('Asia/Colombo');

$uri = (1 == $conf['useslash'])?str_replace(':', '/', $INFO['id']):$INFO['id'];

$page_css =  $uri;
if('/' == substr($page_css, -1, 1)) $page_css = substr($page_css, 0, -1);
$page_css .= '.css';
if(!is_file(DOKU_TPLINC . '/css/'. $page_css)) $page_css = null;

$ns_css = explode('/', $uri);
array_pop($ns_css);
if(1 > sizeof($ns_css)) $ns_css = null;
else {
	$ns_css = join('/', $ns_css) . '/.css';
	if(!is_file(DOKU_TPLINC . '/css/'. $ns_css)) $ns_css = null;
}

$meta_lines = array('description', 'keywords');
$meta = array();
$meta_file = preg_replace('/\.txt$/', '.meta', wikiFN($INFO['id']));
if(is_file($meta_file)) {
	$meta = @file($meta_file);
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<title><?php if(empty($INFO['id'])) { echo tpl_getConf('site_name') , ' &mdash; ' . tpl_getConf('site_slogan'); } else { tpl_pagetitle()?> &mdash; <?php echo tpl_getConf('site_name'); } ?></title>

		<meta name="title" content="<?php tpl_pagetitle()?>" />
		<meta name="charset" content="utf-8" />
		<?php
		foreach($meta_lines as $idx => $name) {
			if(empty($meta[$idx])) continue;
		?>
		<meta name="<?php echo $name; ?>" content="<?php echo trim($meta[$idx]); ?>" />
		<?php
		}
		?>

		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="shortcut icon" type="image/png" href="<?php echo DOKU_TPL; ?>images/lamp.png" />
		<link rel="canonical" href="<?php echo DOKU_URL . $uri; ?>" />

		<link rel="stylesheet" type="text/css" media="print" href="<?php echo DOKU_TPL; ?>css.php/print.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DOKU_TPL; ?>css.php/fonts.css" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo DOKU_TPL; ?>css.php/styles.css" />
		<link rel="stylesheet" type="text/css" media="screen and (min-width: 550px) and (max-width: 1267px)" href="<?php echo DOKU_TPL; ?>css.php/top-bar.css" />
		<link rel="stylesheet" type="text/css" media="screen and (max-width: 550px)" href="<?php echo DOKU_TPL; ?>css.php/bottom-bar.css" />
		<?php if(!empty($ns_css)): ?><link rel="stylesheet" type="text/css" media="all" href="<?php echo DOKU_TPL; ?>css.php/<?php echo $ns_css; ?>" /><?php endif; ?>
		<?php if(!empty($page_css)): ?><link rel="stylesheet" type="text/css" media="all" href="<?php echo DOKU_TPL; ?>css.php/<?php echo $page_css; ?>" /><?php endif; ?>
	</head>
	<body>

		<div id="div-content" class="area">
			<article class="wrapper">
				<?php
				$custom_tpl = DOKU_TPLINC . '/' . $uri . '.php';
				if(is_file($custom_tpl)) include_once $custom_tpl;
				else tpl_content(false);

				include 'include/ns_index.php';

				flush();

				?>
			</article>

			<footer class="wrapper">
				<?php if(-1 < strpos($INFO['id'], ':') && is_file($INFO['filepath'])): ?>
				<p>
					Article last modified on <?php echo date('F jS, Y h:ia T', filemtime($INFO['filepath'])); ?>
					<br />
					Author: <cite><a href="/about-sudaraka-wijesinghe/" rel="author">Sudaraka Wijesinghe</a>.</cite>
				</p>
				<?php endif; ?>
			</footer>
		</div>

		<aside id="div-sidebar" class="area">
			<div class="wrapper">

				<div class="title">
					<a href="/" title="<?php echo tpl_getConf('site_slogan'); ?> - <?php echo tpl_getConf('site_name'); ?>" rel="home">
						<span><?php echo tpl_getConf('site_name'); ?></span>
						<p>...<?php echo tpl_getConf('site_slogan'); ?>...</p>
					</a>
				</div>

				<?php if(page_exists('layouts:sn-icons')): ?>
				<div class="social"><?php echo p_wiki_xhtml('layouts:sn-icons'); ?></div>
				<? endif; ?>

				<?php if(page_exists('layouts:top-links')): ?>
				<nav class="links"><?php echo p_wiki_xhtml('layouts:top-links'); ?></nav>
				<? endif; ?>

				<?php
				$toc = tpl_toc(true);
				if(!empty($toc)) echo $toc;
				?>

				<?php if(page_exists('layouts:navigation')): ?>
				<div class="navigation"><?php echo p_wiki_xhtml('layouts:navigation'); ?></div>
				<? endif; ?>

				<div class="clear"></div>

				<div class="crl">

					<?php if(page_exists('layouts:bottom-links')): ?>
					<nav class="links"><?php echo p_wiki_xhtml('layouts:bottom-links'); ?></nav>
					<? endif; ?>

					<cite>Sudaraka.Org &mdash; <a href="/copyright/#content">Copyright</a> 2012 <a rel="me" href="/about-sudaraka-wijesinghe/">Sudaraka Wijesinghe</a>.</cite>
					<p>
						<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/deed.en_US" style="border: none;"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">Sudaraka.Org</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://sudaraka.org/about-sudaraka-wijesinghe/" property="cc:attributionName" rel="cc:attributionURL">Sudaraka Wijesinghe</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/deed.en_US">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>.<br />Based on a work at <a xmlns:dct="http://purl.org/dc/terms/" href="http://sudaraka.org/" rel="dct:source">http://sudaraka.org/</a>.<br />Permissions beyond the scope of this license may be available at <a xmlns:cc="http://creativecommons.org/ns#" href="http://sudaraka.org/terms-and-conditions/" rel="cc:morePermissions">http://sudaraka.org/terms-and-conditions/</a>.
					</p>
					<br />

					<cite>DokuWiki Theme &mdash; <a href="/copyright/#theme">Copyright</a> 2012 <a rel="me" href="/about-sudaraka-wijesinghe/">Sudaraka Wijesinghe</a>.</cite>
					<p>
						This program is free software: you can redistribute it and/or modify
						it under the terms of the GNU Affero General Public License as published by
						the Free Software Foundation, either version 3 of the License, or
						(at your option) any later version.
					</p>
					<p>
						This program is distributed in the hope that it will be useful,
						but WITHOUT ANY WARRANTY; without even the implied warranty of
						MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
						GNU Affero General Public License for more details.
					</p>
					<p>
						You should have received a copy of the GNU Affero General Public License
						along with this program.  If not, see &lt;http://www.gnu.org/licenses/&gt;.
					</p>

					<p>Get the source code from following GIT repositories:</p>
					<ul type="square">
						<li><a target="_blank" href="https://gitorious.org/sudaraka-org/dokuwiki-theme">DokuWiki Theme</a></li>
						<li><a target="_blank" href="https://gitorious.org/sudaraka-org/dokuwiki-mods">Modified DokuWiki Code</a></li>
					</ul>

				</div>

			</div>
		</aside>

		<div class="hidden">
			<?php /* provide DokuWiki housekeeping, required in all templates */ tpl_indexerWebBug()?>
			<?php if('sudaraka.org' == $_SERVER['HTTP_HOST']):?> <img src="http<?php echo (!empty($_SERVER['HTTPS']))?'s':''; ?>://nojsstats.appspot.com/UA-33560559-1/sudaraka.org<?php if(isset($_SERVER['HTTP_REFERER'])) { echo '?r=' . urlencode($_SERVER['HTTP_REFERER']); } ?>" alt="" width="1" height="1" /><?php endif; ?>
		</div>

	</body>
</html>
