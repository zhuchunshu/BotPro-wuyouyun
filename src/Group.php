<?php 
namespace App\Plugins\wuyouyun\src;

use Curl\Curl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class Group{
    /**
     * 接收到的数据
     *
     * @var object
     */
    public $data;

    /**
     * 插件信息
     *
     * @var array
     */
    public $value;
    
    public $order;

    public $orderCount;

    /**
     * 注册方法
     *
     * @param object 接收到的数据 $data
     * @param array 插件信息 $value
     * @return void
     */
    public function register($data,$value){
        $this->data = $data;
        $this->value = $value;
        $this->order = $order = GetZhiling($data,"#");
        $this->orderCount = count($order);
        $this->boot();
    }

    public function boot(){
        if($this->orderCount==1 && $this->order[0]=="域名过白"){
            sendMsg([
                'group_id' => $this->data->group_id,
                'message' => "[CQ:reply,id=".$this->data->message_id."]目前仅支持无忧云雅安、成都机房自动过白\n\n使用方法:域名过白#雅安或成都#域名\n\n举个例子:域名过白#雅安#node.tax"
            ], "send_group_msg");
        }

        if($this->orderCount===3 && $this->order[0]=="域名过白"){
            switch ($this->order[1]) {
                case '雅安':
                    $id = "ya";
                    break;
                case '雅安机房':
                    $id="ya";
                    break;
                case '成都':
                    $id="cd";
                    break;
                case '成都机房':
                    $id="cd";
                    break;
                default:
                    $id=null;
                    break;
            }
            if($id){
                if(!Cache::has('wuyouyun.api')){
                    $response = Http::withHeaders([
                        "content-type" => "application/x-www-form-urlencoded; charset=UTF-8",
                        "accept" => "application/json, text/javascript, */*; q=0.01",
                        "x-requested-with" => "XMLHttpRequest"
                    ])->post('https://support.wuyouyun.com/index/user/login.html',[
                        'keeplogin' =>1,
                        'password' => get_setting_value("无忧云过白接口登陆密码"),
                        "account" => get_setting_value("无忧云过白接口登陆账号"),
                        "user-agent" => "Mozilla/5.0 (Linux; Android 11; M2102K1C) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.70 Mobile Safari/537.36",
                    ]);
                    $cookie = $response->header("Set-Cookie");
                    $token = $response->json()['data']['token'];
                    $curl = new Curl();
                    $curl->setHeaders([
                        "content-type" => "application/x-www-form-urlencoded; charset=UTF-8",
                        "accept" => "application/json, text/javascript, */*; q=0.01",
                        "x-requested-with" => "XMLHttpRequest",
                        "user-agent" => "Mozilla/5.0 (Linux; Android 11; M2102K1C) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.70 Mobile Safari/537.36",
                        "cookie" => $cookie,
                    ]);
                    $curl->post('https://support.wuyouyun.com/index/user/login.html',[
                        'keeplogin' =>1,
                        'password' => get_setting_value("无忧云过白接口登陆密码"),
                        "account" => get_setting_value("无忧云过白接口登陆账号"),
                        "__token__"=>$token
                    ]);
                    $cookie2 = $cookie.";uid=".$curl->getResponseCookies()['uid']." ; token=".$curl->getResponseCookies()['token'];
                    Cache::put('wuyouyun.api', $cookie2, 600);
                }
                $ck = Cache::get('wuyouyun.api');
                $rp = Http::withHeaders([
                    "cookie" => $ck
                ])->get("https://support.wuyouyun.com/api/".$id."_white/addWhite?domain=".$this->order[2]."&ip=".gethostbyname($this->order[2]));
                sendMsg([
                    'group_id' => $this->data->group_id,
                    'message' => "[CQ:reply,id=".$this->data->message_id."]".$rp->json('msg')
                ], "send_group_msg");
            }else{
                sendMsg([
                    'group_id' => $this->data->group_id,
                    'message' => "[CQ:reply,id=".$this->data->message_id."]机房:".$this->order[1]."不存在"
                ], "send_group_msg");
            }
        }

    }
}