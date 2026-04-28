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
        if (App::frontend()->context()->posts instanceof \Dotclear\Database\MetaRecord) {
            $graceful_cut_excerpt      = $_full_ && is_string($graceful_cut_excerpt = App::frontend()->context()->posts->getExcerpt($_absolute_urls_)) ? $graceful_cut_excerpt : '';
            $graceful_cut_content      = is_string($graceful_cut_content = App::frontend()->context()->posts->getExcerpt($_absolute_urls_)) ? $graceful_cut_content : '';
            $graceful_cut_buffer       = implode(' ', array_filter([$graceful_cut_excerpt, $graceful_cut_content]));
            $graceful_cut_buffer_short = App::frontend()->context()::global_filters($graceful_cut_buffer, $_params_short_, $_tag_);
            $graceful_cut_buffer_full  = App::frontend()->context()::global_filters($graceful_cut_buffer, $_params_full_, $_tag_);
            if (mb_strlen((string) $graceful_cut_buffer_full) > mb_strlen((string) $graceful_cut_buffer_short)) : ?>
            $_content_HTML
        <?php endif;
            unset($graceful_cut_excerpt, $graceful_cut_content, $graceful_cut_buffer, $graceful_cut_buffer_short, $graceful_cut_buffer_full);
        }
    }
}
