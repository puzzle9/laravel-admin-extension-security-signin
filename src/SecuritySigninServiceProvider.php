<?php

namespace Encore\SecuritySignin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class SecuritySigninServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(SecuritySignin $extension)
    {
        if (!SecuritySignin::boot()) {
            return;
        }

        $view_path = resource_path(SecuritySignin::config('view_path'));
        $views = $extension->views();
        // todo: 概率不会出现只存在文件夹不存在文件情况
        $this->loadViewsFrom(File::exists($view_path) ? $view_path : $views, 'admin');

        if ($this->app->runningInConsole()) {
            $assets = $extension->assets();
            $this->publishes([
                $views => $view_path,
                $assets => public_path('vendor/laravel-admin-ext/security-signin'),
            ]);
        }

        $this->app->booted(function () {
            SecuritySignin::routes(__DIR__ . '/../routes/web.php');
        });

    }
}
