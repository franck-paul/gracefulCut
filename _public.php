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
if (!defined('DC_RC_PATH')) {
    return;
}

dcCore::app()->addBehavior('publicContentFilter', ['gracefulCut', 'publicContentFilter']);
dcCore::app()->addBehavior('publicAfterContentFilter', ['gracefulCut', 'publicAfterContentFilter']);

dcCore::app()->tpl->addBlock('IfGracefulCut', ['gracefulCut', 'IfGracefulCut']);

class gracefulCut
{
    public static function publicContentFilter($core = null, $tag, $args, $filter)
    {
        if ($filter == 'cut_string') {
            // graceful_cut take place of cut_string, but only if no encode_xml or encode_html
            if (isset($args['cut_string']) && (int) $args['cut_string'] > 0) {
                if ((!isset($args['encode_xml']) || (int) $args['encode_xml'] == 0) && (!isset($args['encode_html']) || (int) $args['encode_html'] == 0)) {
                    // graceful_cut with cut_string length
                    $args[0] = self::graceful_cut($args[0], (int) $args['cut_string'], true);
                    // then stop applying default cut_string filter
                    return '1';
                }
            }
        }
    }

    public static function publicAfterContentFilter($core = null, $tag, $args)
    {
        if (isset($args['graceful_cut']) && (int) $args['graceful_cut'] > 0) {
            // graceful_cut attribute in tag
            $args[0] = self::graceful_cut($args[0], (int) $args['graceful_cut'], true);
        }
    }

    public static function IfGracefulCut($attr, $content)
    {
        if (empty($attr['cut_string']) && empty($attr['graceful_cut'])) {
            return '';
        }

        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        // Get short version of content
        $short = dcCore::app()->tpl->getFilters($attr);

        // Get full version of content
        $cut  = $attr['cut_string']   ?? 0;
        $gcut = $attr['graceful_cut'] ?? 0;
        if ($cut) {
            $attr['cut_string'] = 0;
        }
        if ($gcut) {
            $attr['graceful_cut'] = 0;
        }
        $full = dcCore::app()->tpl->getFilters($attr);

        // Restore args
        if ($cut) {
            $attr['cut_string'] = $cut;
        }
        if ($gcut) {
            $attr['graceful_cut'] = $gcut;
        }

        if (!empty($attr['full'])) {
            return '<?php if (strlen(' . sprintf(
                $full,
                'dcCore::app()->ctx->posts->getExcerpt(' . $urls . ').' .
                    '(strlen(dcCore::app()->ctx->posts->getExcerpt(' . $urls . ')) ? " " : "").' .
                    'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
            ) . ') > ' .
                'strlen(' . sprintf(
                    $short,
                    'dcCore::app()->ctx->posts->getExcerpt(' . $urls . ').' .
                    '(strlen(dcCore::app()->ctx->posts->getExcerpt(' . $urls . ')) ? " " : "").' .
                    'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
                ) . ')) : ?>' .
                $content .
                '<?php endif; ?>';
        }

        return '<?php if (strlen(' . sprintf(
            $full,
            'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
        ) . ') > ' .
                'strlen(' . sprintf(
                    $short,
                    'dcCore::app()->ctx->posts->getContent(' . $urls . ')'
                ) . ')) : ?>' .
                $content .
                '<?php endif; ?>';
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
    private static function graceful_cut(
        $str,
        $l = 100,
        $html = true,
        $ending = '<span class="ellipsis">&nbsp;[&#8230;]</span>',
        $exact = false
    ) {
        if ($html) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', (string) $str)) <= $l) {
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
                    // if it's an "empty element" with or without xhtml-conform closing slash
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
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', (string) $line_matchings[2]));
                if ($total_length + $content_length > $l) {
                    // the number of characters which are left
                    $left            = $l - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
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
