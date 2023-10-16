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

use ArrayObject;
use dcCore;

class FrontendTemplate
{
    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     *
     * @return     string
     */
    public static function IfGracefulCut(array|ArrayObject $attr, string $content): string
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
}
