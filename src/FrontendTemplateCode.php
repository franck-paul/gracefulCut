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

use Dotclear\App;

class FrontendTemplateCode
{
    /**
     * PHP code for tpl:IfGracefulCut block
     *
     * @param      array<int|string, mixed>     $_params_short_  The parameters for short text (using cut/gracefulcut attribute)
     * @param      array<int|string, mixed>     $_params_full_   The parameters for full text
     */
    public static function IfGracefulCut(
        bool $_absolute_urls_,
        bool $_full_,
        string $_content_HTML,
        array $_params_short_,
        array $_params_full_,
        string $_tag_
    ): void {
        $graceful_cut_buffer = implode(' ', array_filter([
            $_full_ ? App::frontend()->context()->posts->getExcerpt($_absolute_urls_) : '',
            App::frontend()->context()->posts->getContent($_absolute_urls_),
        ]));
        $graceful_cut_buffer_short = \Dotclear\Core\Frontend\Ctx::global_filters($graceful_cut_buffer, $_params_short_, $_tag_);
        $graceful_cut_buffer_full  = \Dotclear\Core\Frontend\Ctx::global_filters($graceful_cut_buffer, $_params_full_, $_tag_);
        if (mb_strlen($graceful_cut_buffer_full) > mb_strlen($graceful_cut_buffer_short)) : ?>
            $_content_HTML
        <?php endif;
        unset($graceful_cut_buffer, $graceful_cut_buffer_short, $graceful_cut_buffer_full);
    }
}
