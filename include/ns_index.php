<?php
/**
 *  ns_index.php: Namespace index html generator
 *  
 *  Created: 08/09/2012
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

$ns_dir = preg_replace('/\.txt$/', '', wikiFN($INFO['id']));

if(is_dir($ns_dir) && !empty($INFO['id'])):


$file_list=array();
@exec('ls -t ' . $ns_dir . '/*.txt 2>/dev/null', $file_list);

?>

<ul class="ns_index">
<?php
foreach($file_list as $file) {
	$title = '';
	$link = '';
	$desc = '';
	
	$file_name = preg_replace('/\.txt$/', '', basename($file));
	
	// Checkif link is mentioned in the page
	$tmp = file_get_contents($INFO['filepath']);
	if(-1 < strpos($tmp, $file_name)) continue;
	
	$tmp = file_get_contents($file);
	preg_match('/(={6})\s*(.+)\s*(\1)/', $tmp, $title);
	if(empty($title[2])) $title = $file_name;
	else $title = trim($title[2]);
	
	$meta_file = preg_replace('/\.txt$/', '.meta', $file);
	if(is_file($meta_file)) {
		$desc = file($meta_file);
		if(isset($desc[0])) $desc = trim($desc[0]);
		else $desc = '';
	}
	
	$ns = explode(':', $INFO['id']);
	$link = join('/', $ns) . '/' . $file_name;
?>
	<li>
		<strong><a href="<?php echo $link; ?>" class="wikilink1" title="<?php echo $title; ?>"><?php echo $title; ?></a></strong>
		<em>(<?php echo date('F jS, Y h:ia', filemtime($file)); ?>)</em>
		<?php if(!empty($desc) && $title != $desc): ?><p><?php echo $desc; ?></p><?php endif; ?>
	</li>
<?php
}
?>
</ul>
<?php


endif; //if(is_dir($ns_dir))