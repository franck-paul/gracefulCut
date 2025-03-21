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
        $cut         = $attr['cut_string']   ?? 0;
        $gracefulcut = $attr['graceful_cut'] ?? 0;
        if ($cut) {
            $attr['cut_string'] = 0;
        }

        if ($gracefulcut) {
            $attr['graceful_cut'] = 0;
        }

        $params_full = App::frontend()->template()->getFiltersParams($attr);

        // Restore args
        if ($cut) {
            $attr['cut_string'] = $cut;
        }

        if ($gracefulcut) {
            $attr['graceful_cut'] = $gracefulcut;
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
