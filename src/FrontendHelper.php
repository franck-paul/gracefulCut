<?php
/**
 * @brief gracefulCut, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\gracefulCut;

class FrontendHelper
{
    /**
     * graceful_cut can truncate a string up to a number of characters while preserving whole words and HTML tags
     * Author: Alan Whipple (http://alanwhipple.com/2011/05/25/php-truncate-string-preserving-html-tags-words/)
     *
     * @param string    $str        String to truncate.
     * @param int       $l          Length of returned string, including ellipsis.
     * @param bool      $html       If true, HTML tags would be handled correctly
     * @param string    $ending     Ending to be appended to the trimmed string.
     * @param bool      $exact      If false, $str will not be cut mid-word
     *
     * @return string Trimmed string.
     */
    public static function graceful_cut(
        string $str,
        int $l = 100,
        bool $html = true,
        string $ending = '<span class="ellipsis">&nbsp;[&#8230;]</span>',
        bool $exact = false
    ): string {
        if ($html) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen((string) preg_replace('/<.*?>/', '', (string) $str)) <= $l) {
                return $str;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', (string) $str, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags    = [];
            $truncate     = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without html-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen((string) preg_replace('/&[0-9a-z]{2,8};|&#\d{1,7};|[0-9a-f]{1,6};/i', ' ', (string) $line_matchings[2]));
                if ($total_length + $content_length > $l) {
                    // the number of characters which are left
                    $left            = $l - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#\d{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left + $entities_length);

                    // maximum lenght is reached, so get off the loop
                    break;
                }
                $truncate .= $line_matchings[2];
                $total_length += $content_length;

                // if the maximum length is reached, get off the loop
                if ($total_length >= $l) {
                    break;
                }
            }
        } else {
            if (strlen($str) <= $l) {
                return $str;
            }
            $truncate = substr($str, 0, $l - strlen($ending));
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if ($spacepos !== false) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($html) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }
}
