<?php

namespace packages\request\Listeners\Settings;

use packages\userpanel\UserType\Permissions;

class UserType
{
    public function permissions_list()
    {
        $permissions = [
            'search',
            'view',
            'delete',
            'edit',
            'lunch',
        ];
        foreach ($permissions as $permission) {
            Permissions::add('request_'.$permission);
        }
    }
}
