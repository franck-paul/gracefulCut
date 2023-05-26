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

class FrontendBehaviors
{
    public static function publicContentFilter($tag, $args, $filter)
    {
        if ($filter == 'cut_string') {
            // graceful_cut take place of cut_string, but only if no encode_xml or encode_html
            if (isset($args['cut_string']) && (int) $args['cut_string'] > 0) {
                if ((!isset($args['encode_xml']) || (int) $args['encode_xml'] == 0) && (!isset($args['encode_html']) || (int) $args['encode_html'] == 0)) {
                    // graceful_cut with cut_string length
                    $args[0] = FrontendHelper::graceful_cut($args[0], (int) $args['cut_string'], true);
                    // then stop applying default cut_string filter
                    return '1';
                }
            }
        }
    }

    public static function publicAfterContentFilter($tag, $args)
    {
        if (isset($args['graceful_cut']) && (int) $args['graceful_cut'] > 0) {
            // graceful_cut attribute in tag
            $args[0] = FrontendHelper::graceful_cut($args[0], (int) $args['graceful_cut'], true);
        }
    }
}
