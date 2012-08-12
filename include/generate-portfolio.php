<?php
/**
 *  generate-portfolio.php: Portfolio page Wiki text generator
 *  
 *  Created: 08/12/2012
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

// Check hash file for determine rebuild status
$file_name = preg_replace('/\.txt$/', '', wikiFN(':portfolio'));
if(!is_file($file_name . '.hash') || !is_file($file_name . '.txt')) $rebuild = true;
else {
	$stored_hash = file_get_contents($file_name . '.hash');
	if(empty($stored_hash)) {
		$generated_hash = generate_hash();
		$rebuild = true;
	}
	else {
		// Check TTL
		if(
		   (time() - filemtime($file_name . '.hash')) > tpl_getConf('portfolio_ttl') ||
		   (time() - filemtime($file_name . '.txt')) > tpl_getConf('portfolio_ttl')
		) {
			$generated_hash = generate_hash($gallery_namespaces);
			if($generated_hash != $stored_hash)	$rebuild = true;
			else touch($file_name . '.hash');
		}
	}
}

if($rebuild) { // rebuild gallery wiki text

	$wiki_text = <<<WIKI
====== Portfolio ======

//My bread & butter - list of project from my freelance & entrepreneurial
career that contributed to me experience since 2004.//

WIKI;
	
	$file_list = get_portfolio_file_list();
	foreach($file_list as $file) {
		$title = '';
		$tmp = file_get_contents($file);
		preg_match('/(={6})\s*(.+)\s*(\1)/', $tmp, $title);
		if(isset($title[2])) $title = $title[2];
		else $title = '';
		
		$id = preg_replace('/\.txt$/', '', basename($file));
		
		$wiki_text .= '<html><div class="project"><h5>' . $title . '</h5></html>' . PHP_EOL;
		$wiki_text .= '[[:portfolio:' . $id . '|{{:portfolio:' . $id . '.jpg?390x220}}]]' . PHP_EOL;
		$wiki_text .= '<html></div></html>' . PHP_EOL;
	}
	
	// Save wiki text
	$fh = fopen($file_name . '.txt', 'w');
	if(is_resource($fh)) {
		fwrite($fh, $wiki_text);
		fclose($fh);
	}
	
	//// Save new hash
	//$fh = @fopen($file_name . '.hash', 'w');
	//if(is_resource($fh)) {
	//	@fwrite($fh, $generated_hash);
	//	@fclose($fh);
	//}

}

function generate_hash() {
	$hash = null;
	
	$file_list = get_portfolio_file_list();
	foreach($file_list as $file) {
		$hash = md5($hash . $file . filemtime($file) . filesize($file));
	}
	
	return $hash;
}

function get_portfolio_file_list() {
	$file_list = array();
	$dir = dirname(wikiFN(':portfolio:nx-page-name'));
	
	exec('ls -t ' . $dir . '/*.txt 2>/dev/null', $file_list);
	foreach($file_list as  $idx => $file) {
		$file_list[$idx] = $file;
	}
	
	return $file_list;
}