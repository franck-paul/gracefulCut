<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of Gravatar, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    'gracefulCut',                                                     // Name
    'Gracefully cut_string filter alternative for Dotclear templates', // Description
    'Franck Paul',                                                     // Author
    '0.3',                                                             // Version
    array(
        'requires'    => array(array('core', '2.14')),
        'permissions' => 'admin',
        'type'        => 'plugin'
    )
);
