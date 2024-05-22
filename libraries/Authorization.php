<?php

namespace packages\request;

use packages\userpanel\Authorization as UserPanelAuthorization;

class Authorization extends UserPanelAuthorization
{
    public static function is_accessed($permission, $prefix = 'request')
    {
        return parent::is_accessed($permission, $prefix);
    }

    public static function haveOrFail($permission, $prefix = 'request')
    {
        parent::haveOrFail($permission, $prefix);
    }
}
