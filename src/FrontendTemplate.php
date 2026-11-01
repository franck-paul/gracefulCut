<?php

/**
 * @brief gracefulCut, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\gracefulCut;

use ArrayObject;
use Dotclear\App;
use Dotclear\Plugin\TemplateHelper\Code;

class FrontendTemplate
{
    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function IfGracefulCut(array|ArrayObject $attr, string $content): string
    {
        if (empty($attr['cut_string']) && empty($attr['graceful_cut'])) {
            return '';
        }

        // Get short version of attributes
        $params_short = App::frontend()->template()->getFiltersParams($attr);

        // Get full version of attributes
        $cut_string = isset($attr['cut_string']) && is_numeric($cut_string = $attr['cut_string']) ? (int) $cut_string : 0;
        if ($cut_string === 1) {
            $attr['cut_string'] = 0;
        }

        $graceful_cut = isset($attr['graceful_cut']) && is_numeric($graceful_cut = $attr['graceful_cut']) ? (int) $graceful_cut : 0;
        if ($graceful_cut === 1) {
            $attr['graceful_cut'] = 0;
        }

        $params_full = App::frontend()->template()->getFiltersParams($attr);

        // Restore args
        if ($cut_string === 1) {
            $attr['cut_string'] = $cut_string;
        }

        if ($graceful_cut === 1) {
            $attr['graceful_cut'] = $graceful_cut;
        }

        return Code::getPHPCode(
            FrontendTemplateCode::IfGracefulCut(...),
            [
                !empty($attr['absolute_urls']),
                !empty($attr['full']),
                $content,
                $params_short,
                $params_full,
                App::frontend()->template()->getCurrentTag(),
            ],
        );
    }
}
