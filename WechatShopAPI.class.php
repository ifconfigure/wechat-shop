<?php
// +----------------------------------------------------------------------
// | 微信小店API接口
// +----------------------------------------------------------------------
// | 从微信小店官方API获取、同步 数据
// +----------------------------------------------------------------------
// | 界面层不直接与数据层交互，提高代码的扩展性与健壮性
// +----------------------------------------------------------------------

namespace Logic;

class WechatShopAPI {
	public $appid 	= '';
	public $secret 	= '';
	public $access_token='';

	function __construct() {
		$this->appid 	= C("appid");
		$this->secret 	= C("secret");
	}

    /**
     * [addStock 增加商品库存]
     * @param [type] $p_id     [description]
     * @param [type] $quantity [description]
     */
    public function addStock($p_id ,$quantity){
        $url = "https://api.weixin.qq.com/merchant/stock/add";
        $this->access_token = $this->getAccessToken();
        $param['access_token'] = $this->access_token;
        $map['product_id'] = $p_id;
        $mao['sku_info'] = "";
        $map['quantity'] = $quantity;
        $map = json_encode($map);
        $data = $this->http($url ,$param ,$map , 'POST');
        $data = json_decode($data ,true);
        return $data;
    }
    /**
     * [addStock 减少商品库存]
     * @param [type] $p_id     [description]
     * @param [type] $quantity [description]
     */
    public function reduceStock($p_id ,$quantity){
        $url = "https://api.weixin.qq.com/merchant/stock/reduce";
        $this->access_token = $this->getAccessToken();
        $param['access_token'] = $this->access_token;
        $map['product_id'] = $p_id;
        $mao['sku_info'] = "";
        $map['quantity'] = $quantity;
        $map = json_encode($map);
        $data = $this->http($url ,$param ,$map , 'POST');
        $data = json_decode($data ,true);
        return $data;
    }

    /**
     * [chgProductStatus 更改产品上下架状态]
     * @param  [type] $p_id   [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function chgProductStatus($p_id ,$status){
        $url = "https://api.weixin.qq.com/merchant/modproductstatus";
        $this->access_token = $this->getAccessToken();
        $param['access_token'] = $this->access_token;
        $map['product_id'] = $p_id;
        $map['status'] = $status;
        $map = json_encode($map);
        $data = $this->http($url ,$param ,$map , 'POST');
        $data = json_decode($data ,true);
        return $data;
    }

    /**
     * [closeOrder 关闭订单]
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    public function closeOrder($order_id){
        $url = "https://api.weixin.qq.com/merchant/order/close";
        $this->access_token = $this->getAccessToken();
        $param['access_token'] = $this->access_token;
        $map['order_id'] = $order_id;
        $map = json_encode($map);
        $data = $this->http($url ,$param ,$map , 'POST');
        $data = json_decode($data ,true);
        return $data;
    }
    /**
     * [deliveryToWeixin 设置发货接口]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function deliveryToWeixin($arr){
        $url = "https://api.weixin.qq.com/merchant/order/setdelivery";
        $this->access_token = $this->getAccessToken();
        $param['access_token'] = $this->access_token;
        $map['order_id'] = $id;
        $map = json_encode($arr);
        $data = $this->http($url ,$param ,$map , 'POST');
        $data = json_decode($data ,true);
        return $data;
    }

    /**
     * [getOrderDetailByID]
     * 微信小店接口，1.1   根据订单ID获取订单详情
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getOrderDetailByID($id){
        $url = "https://api.weixin.qq.com/merchant/order/getbyid";
        $this->access_token = $this->getAccessToken();
        $param['access_token'] = $this->access_token;
        $map['order_id'] = $id;
        $map = json_encode($map);
        $data = $this->http($url ,$param ,$map , 'POST');
        $data = json_decode($data ,true);
        return $data['order'];
    }

    /**
     * [downloadOrderList 从微信小店接口下载订单列表]
     * @return [type] [description]
     */
    public function downloadOrderList($begin ,$end){
        $url = "https://api.weixin.qq.com/merchant/order/getbyfilter";
        $this->access_token = $this->getAccessToken();
        $param['access_token'] = $this->access_token;
        $post['begintime'] = $begin;
        $post['endtime'] = $end;
        $map = json_encode($post);
        $data = $this->http($url ,$param ,$map , 'POST');
        // return $begin;
        // return $this->access_token;
        // return $post;
        return  json_decode($data ,true);
    }

	/**
	 * [syncFromWeixin 获取微信小店所有商品]
	 * @return [type] [description]
	 */
	public function getList(){
		$url = "https://api.weixin.qq.com/merchant/getbystatus";
		$this->access_token = $this->getAccessToken();
		$param['access_token'] = $this->access_token;
		$post['status'] = 0;
		$data = $this->http($url ,$param ,$post , 'POST');
        // return $data;//json
		return  json_decode($data ,true);
	}

    public function syncProductToWechat($p_id ,$price ,$ori_price){
        $url = "https://api.weixin.qq.com/merchant/update";
        return $this->access_token = $this->getAccessToken();

        // $json=''

        $param['access_token'] = $this->access_token;
        $map['product_id'] = $p_id;
        $map[0]['price'] = $price;
        $map[0]['ori_price'] = $ori_price;
        $data = $this->http($url ,$param ,$map , 'POST');
        
        return $data;
    }


	/**
	 * [getAccessToken 微信通用，获取AccessToken]
	 * @return [type] [description]
	 */
	public function getAccessToken(){
	    $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
	    $json = file_get_contents($url);
	    $access_arr = json_decode($json,true);
	    $this->access_token = $access_arr['access_token'];
	    return $access_arr['access_token'];
	}

    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param  string $url    请求URL
     * @param  array  $param  GET参数数组
     * @param  array  $data   POST的数据，GET请求时该参数无效
     * @param  string $method 请求方法GET/POST
     * @return array          响应数据
     */
    protected static function http($url, $param, $data = '', $method = 'GET'){
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );

        /* 根据请求类型设置特定参数 */
        $opts[CURLOPT_URL] = $url . '?' . http_build_query($param);

        if(strtoupper($method) == 'POST'){
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $data;
            
            if(is_string($data)){ //发送JSON数据
                $opts[CURLOPT_HTTPHEADER] = array(
                    'Content-Type: application/json; charset=utf-8',  
                    'Content-Length: ' . strlen($data),
                );
            }
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        //发生错误，抛出异常
        if($error) throw new \Exception('请求发生错误：' . $error);

        return  $data;
    }
}