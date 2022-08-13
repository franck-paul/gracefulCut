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

$this->registerModule(
    'gracefulCut',                                                     // Name
    'Gracefully cut_string filter alternative for Dotclear templates', // Description
    'Franck Paul',                                                     // Author
    '0.4',
    [
        'requires'    => [['core', '2.23']],
        'permissions' => 'admin',
        'type'        => 'plugin',

        'details'    => 'https://open-time.net/?q=gracefulCut',       // Details URL
        'support'    => 'https://github.com/franck-paul/gracefulCut', // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/gracefulCut/master/dcstore.xml',
    ]
);
