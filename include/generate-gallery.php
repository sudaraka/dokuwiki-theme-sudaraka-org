<?php
/**
 *  generate-gallery.php: Gallery page Wiki text generator
 *  
 *  Created: 08/11/2012
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

$rebuild = !empty($_GET['purge']);
$stored_hash = null;
$generated_hash = null;
$gallery_namespaces = array(
	'photographs' => 'Photographs',
	'web-design' => 'Web Design',
);

// Check hash file for determine rebuild status
$file_name = preg_replace('/\.txt$/', '', wikiFN(':gallery'));
if(!is_file($file_name . '.hash') || !is_file($file_name . '.txt')) $rebuild = true;
else {
	$stored_hash = file_get_contents($file_name . '.hash');
	if(empty($stored_hash)) {
		$generated_hash = generate_hash($gallery_namespaces);
		$rebuild = true;
	}
	else {
		// Check TTL
		if(
		   (time() - filemtime($file_name . '.hash')) > tpl_getConf('gallery_ttl') ||
		   (time() - filemtime($file_name . '.txt')) > tpl_getConf('gallery_ttl')
		) {
			$generated_hash = generate_hash($gallery_namespaces);
			if($generated_hash != $stored_hash)	$rebuild = true;
			else touch($file_name . '.hash');
		}
	}
}

if($rebuild) { // rebuild gallery wiki text

	$wiki_text = <<<WIKI
====== Gallery ======

  * [[#photographs|Photographs]]
  * [[#web-design|Web Design]]
\\\\
\\\\
Photographs, Web designs and various other creative content by Sudaraka.
WIKI;
	
	foreach($gallery_namespaces as $ns => $text) {
		$wiki_text .= '===== ' . $text . ' =====' . PHP_EOL;
		$wiki_text .= '<html><div class="' . $ns . '"></html>' . PHP_EOL;
		
		$file_list = get_gallery_file_list($ns);
		foreach($file_list as $file) {
			$file = basename($file);
			$id = str_replace('.', '', $file);
			
			$wiki_text .= '[[#popup-' . $id . '|{{:gallery:' . $ns . ':' . $file . '?290x163}}]]' . PHP_EOL;
			$wiki_text .= '<html><span id="close-' . $id . '"></span><div class="popup-overlay" id="popup-' . $id . '"></html>' . PHP_EOL;
			$wiki_text .= '[[#close-' . $id . '|{{:gallery:' . $ns . ':' . $file . '}}]]' . PHP_EOL;
			$wiki_text .= '<html></div></html>' . PHP_EOL;

		}
		
		$wiki_text .= '<html></div><div class="clear"></div></html>\\\\' . PHP_EOL;
	}
	
	// Save wiki text
	$fh = fopen($file_name . '.txt', 'w');
	if(is_resource($fh)) {
		fwrite($fh, $wiki_text);
		fclose($fh);
	}
	
	// Save new hash
	$fh = @fopen($file_name . '.hash', 'w');
	if(is_resource($fh)) {
		@fwrite($fh, $generated_hash);
		@fclose($fh);
	}

}

function generate_hash($namespace_list) {
	$hash = null;
	
	if(is_array($namespace_list)) {
		foreach($namespace_list as $ns => $text) {
			$file_list = get_gallery_file_list($ns);
			foreach($file_list as $file) {
				$hash = md5($hash . $file . filemtime($file) . filesize($file));
			}
		}
		
	}
	
	return $hash;
}

function get_gallery_file_list($namespace) {
	$file_list = array();
	$dir = mediaFN(':gallery:'. $namespace);
	
	exec('ls -t ' . $dir . ' 2>/dev/null', $file_list);
	foreach($file_list as  $idx => $file) {
		rename($dir . '/' . $file, $dir . '/' . strtolower($file));
		$file_list[$idx] = $dir . '/' . strtolower($file);
	}
	
	return $file_list;
}