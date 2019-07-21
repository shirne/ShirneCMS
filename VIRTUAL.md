# 虚拟主机不支持web目录之外上传文件的解决办法

1. 将public目录同层的那些文件及目录，归纳到public下新建目录(如： system)
    > 修改后的目录结构 <br />
    ├── public                      // 网站根目录，对应虚拟主机的wwwroot或htdocs等<br />
    │   ├── static      //网站静态文件<br />
    │   ├── uploads                // 文件上传目录<br />
    │   ├── system         // 系统核心文件目录<br />
    │   │   ├──application  应用总目录<br />
    │   │   ├──config  配置文件目录<br />
    │   │   ├──extend  扩展类<br />
    │   │   ├──route  路由配置目录<br />
    │   │   ├──runtime  运行时目录缓存 ，日志等()<br />
    │   │   ├──template  前端模板目录<br />
    │   │   ├──thinkphp  框架目录<br />
    │   │   ├──vender  第三方库目录<br />
    │   ├── index.php                // 入口文件<br />
    │   ├── ...
2. 修改配置文件，自动加载extend目录
    > config/app.php
    ```php
    'root_namespace'         => [
            'extcore' => './system/extend/extcore',
            'shirne'=>'./system/extend/shirne',
        ],
    ```

3. 修改index.php入口文件
    ```php
    // [ 应用入口文件 ]
    namespace think;
    
    define('DOC_ROOT',__DIR__);
    define('APP_PATH',__DIR__. '/system/application/');
    
    // 加载基础文件
    require __DIR__ . '/system/thinkphp/base.php';
    
    
    // 执行应用并响应
    Container::get('app')->path(APP_PATH )->run()->send();
    ```
4. 修改thinkphp核心文件 (使用composer更新thinkphp库时需要再次修改，修改此处不会影响正常目录结构的项目运行)
    > thinkphp/library/think/Loader.php
    ```php
      // 获取应用根目录
      public static function getRootPath()
      {
          if(defined('APP_PATH')){
              return dirname(APP_PATH). DIRECTORY_SEPARATOR;
          }else {
              //...  原方法代码
          }
      }
    ```