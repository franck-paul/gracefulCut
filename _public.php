<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of gracefulCut, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('publicBeforeContentFilter', array('gracefulCut','publicBeforeContentFilter'));
$core->addBehavior('publicAfterContentFilter', array('gracefulCut','publicAfterContentFilter'));

class gracefulCut
{
	public static function publicBeforeContentFilter($core,$tag,$args)
	{
		// Check if we need to cope with filters (cut_string present, remove_html optional)
		if (!empty($args['cut_string']) && (integer) $attr['cut_string'] > 0) {
			$args[] = $args[0];
			$args[] = 'graceful_cut';
		}
	}
	public static function publicAfterContentFilter($core,$tag,$args)
	{
		if ($args[count($args)-1] === 'graceful_cut') {
			$html = true;
			// Remove graceful_cut flag
			array_pop($args);
			// Get the original string
			$str = array_pop($args);
			if ((integer) !empty($attr['remove_html'])) {
				$str = context::remove_html($str);
				$str = preg_replace('/\s+/',' ',$str);
				$html = false;
			}
			$str = self::graceful_cut($str,(integer)$args['cut_string'],$html);
			$args[0] = $str;
		}
	}

	/**
	 * graceful_cut can truncate a string up to a number of characters while preserving whole words and HTML tags
	 * Author: Alan Whipple (http://alanwhipple.com/2011/05/25/php-truncate-string-preserving-html-tags-words/)
	 *
	 * @param string $str String to truncate.
	 * @param integer $l Length of returned string, including ellipsis.
	 * @param boolean $html If true, HTML tags would be handled correctly
	 * @param string $ending Ending to be appended to the trimmed string.
	 * @param boolean $exact If false, $str will not be cut mid-word
	 *
	 * @return string Trimmed string.
	 */
	private static function graceful_cut($str,
		$l = 100,
		$html = true,
		$ending = '<span class="ellipsis">&nbsp;[&#8230;]</span>',
		$exact = false
		)
	{
		if ($html) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $str)) <= $l) {
				return $str;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $str, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
						unset($open_tags[$pos]);
						}
					// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $l) {
					// the number of characters which are left
					$left = $l - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $l) {
					break;
				}
			}
		} else {
			if (strlen($str) <= $l) {
				return $str;
			} else {
				$truncate = substr($str, 0, $l - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($html) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
}
