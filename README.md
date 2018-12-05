# 微信接口SDK v1.2.4

使用方法:

####1 公众号相关接口调用示例

1.1 实例化一个微信实例
````
    $config = [
       'appid'  => 'wxf854059ff2a1243b',
       'secret' => '11351fedf52a81095a5c780e3bb4a7e8',
    ];
    
    $wx = new \doyzheng\weixin\Weixin($config);
````

1.2 获取access_token
````
    $config = [
       'appid'  => 'wxf854059ff2a1243b',
       'secret' => '11351fedf52a81095a5c780e3bb4a7e8',
    ];
    
    $wx = new \doyzheng\weixin\Weixin($config);
    $access_token = $wx->accessToken->getToken();
    echo $access_token;
    // 或 accessToken对象当做字符串处理时会默认返回token字符串
    $access_token = $wx->accessToken . '';
    $access_token = (string)$wx->accessToken . '';
    echo $access_token;
````

1.3 设置access_token,如果通过其它方式已经获取token则可以重新设置,以后的接口就会使用新设置的token

````
    $wx->accessToken->setToken($token);
````

1.4 使用公众号进行授权登录

````
    if(mepty($_SESSION['wxUser'])){
        $_SESSION['wxUser'] = $wx->mp->auth->getUserInfo();
        // 保存用户信息后调用此方法可以重定向到授权来源页
        $wx->mp->auth->redirect();
    }
    print_r( $_SESSION['wxUser']);  
````
1.5 获取微信JsSDK配置参数

````
    $js = $wx->mp->js;
    // 设置使用jsApi页面的Url
    $js->setUrl('htpp://***.com');
    $config = $js->getConfigArray();
    print_r($config );
````

####2 项目文件介绍

2.1 核心类 
````
    \doyzheng\weixin\base\AccessToken    微信接口token类
    \doyzheng\weixin\base\Cache          缓存类(默认使用文件方式)
    \doyzheng\weixin\base\Exception      异常统一处理类
    \doyzheng\weixin\base\Helper         助手方法类(默认使用文件方式)
    \doyzheng\weixin\base\Log            全局日志记录类(包含请求日志、错误日志、接口回调日志)
    \doyzheng\weixin\base\Request        接口请求类（默认使用Curl方法）
````
2.2 核心模块
````        
      \doyzheng\weixin\mini\Module       小程序接口模块
      \doyzheng\weixin\mp\Module         公众号接口模块
      \doyzheng\weixin\open\Module       开放平台接口模块
      \doyzheng\weixin\parking\Module    停车场接口模块
````      

####3 替换内部核心类

3.1 替换缓存类 \doyzheng\weixin\base\Cache
````
    // 新的的缓存类必须继承\doyzheng\weixin\base\BaseCache,并且实现\doyzheng\weixin\base\interfaces\CacheInterface中的全部接口方法
    $wx->cache = new MyCache();
````

3.2 替换\doyzheng\weixin\base\AccessToken
````
    // 新的AccessToken类必须继续\doyzheng\weixin\base\BaseObject,并且实现\doyzheng\weixin\base\interfaces\AccessTokenInterface中的全部接口方法
    $wx->accessToken = new MyAccessToken();
````

####4 配置
````
    $wx->runtimePath = '/runtime/'  // 设置项目允许目录(缓存文件,日志文件等储存目录)
    $wx->appDebug = true;           // 开启调试模式,默认不开启(调试模式下会抛出错误异常)
    $wx->log->disable = false;      // 禁止记录日志(请求日志,错误日志,接口回调日志等)    
````

####4 异常处理

4.1 自定义处理异常
```
    $wx->errorHandler = function($exception){
        .... 
        return true;// 如果有返回, 就不会执行系统默认异常处理程序了
    }
```
