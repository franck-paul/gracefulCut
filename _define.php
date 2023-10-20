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
$this->registerModule(
    'gracefulCut',
    'Gracefully cut_string filter alternative for Dotclear templates',
    'Franck Paul',
    '3.0',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',

        'details'    => 'https://open-time.net/?q=gracefulCut',
        'support'    => 'https://github.com/franck-paul/gracefulCut',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/gracefulCut/master/dcstore.xml',
    ]
);
