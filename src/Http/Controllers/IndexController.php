<?php
namespace App\Plugins\wuyouyun\src\Http\Controllers;

use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Markdown;

class IndexController {
    public function show(Content $content){
        $content->title('Wuyouyun');
        $content->header('Wuyouyun');
        $content->description('Wuyouyun插件信息');
        $content->body(Card::make(
            Markdown::make(read_file(plugin_path("wuyouyun/README.md")))
        ));
        return $content;
    }
}