<?php
/**
 * 替换模板标签
 * @param array $tags
 * @param string $template
 */
function replace_tags($tags, $template){
	foreach (array_keys($tags) as $v){
		$pattern[] = '/\{'.$v.'\}/i';
	}
	$replacement = array_values($tags);
	return preg_replace($pattern, $replacement, $template);
}