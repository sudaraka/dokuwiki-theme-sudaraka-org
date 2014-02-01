<?php
/**
 *  css.php: CSS minifyier
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

$css_file = str_replace($_SERVER['SCRIPT_NAME'], dirname(__FILE__) . '/css', $_SERVER['REQUEST_URI']);
if(!is_file($css_file)) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	exit;
}

$css =file_get_contents($css_file);
$last_modified = substr(gmdate('r', filemtime($css_file)), 0, -5) . 'GMT';

//Minify css
$css = preg_replace('/\s+/', ' ', $css);
$css = preg_replace('/\/\*.*?\*\//', ' ', $css);

header('Content-Type: text/css');
header('Content-Disposition: filename="' . basename($css_file) . '"');
header('Last-Modified: ' . $last_modified);
header('ETag: ' . md5($last_modified));

echo $css;
