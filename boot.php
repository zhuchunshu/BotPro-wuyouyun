<?php

namespace App\Plugins\wuyouyun;

use Dcat\Admin\Admin;
use Illuminate\Support\Facades\Route;

class boot
{

    public function handle()
    {
        $this->menu();
        $this->route();
        include plugin_path("wuyouyun/vendor/autoload.php");
        include plugin_path("wuyouyun/src/lib/helpers.php");
    }

    public function menu()
    {
        Admin::menu()->add(include __DIR__ . '/src/lib/menu.php', 0);
    }

    public function route()
    {
        Route::middleware(config('admin.route.middleware'))
        ->prefix(config('admin.route.prefix') . "/wuyouyun")
        ->name('admin.wuyouyun.')
        ->group(plugin_path("wuyouyun/src/lib/route.php"));
    }
}
