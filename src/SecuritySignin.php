<?php

namespace Encore\SecuritySignin;

use Encore\Admin\Extension;

class SecuritySignin extends Extension
{
    public $name = 'security-signin';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Securitysignin',
        'path'  => 'security-signin',
        'icon'  => 'fa-gears',
    ];
}
