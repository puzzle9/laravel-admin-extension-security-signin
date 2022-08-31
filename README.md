# laravel-admin extension signin security

适用于 `laravel-admin` 的 安全登录扩展,支持`登录`时`验证码`、`限流`、`rsa加密`等

# 安装

```shell
composer require puzzle9/laravel-admin-extension-security-signin -vvv
```

## 推送资源文件

```shell
php artisan vendor:publish --provider="Encore\SecuritySignin\SecuritySigninServiceProvider"
```

## 添加配置文件

`config/admin.php` 文件 `extensions` 中

```php
'extensions' => [
    // ...
    'security-signin' => [
        'enable'          => true,
        // 未更改过框架默认视图的话 默认即可
        'view_path'       => '/views/laravel-admin/security-signin',
        'captcha'         => [
            // 是否开启验证码功能
            'enable'                      => true,
            // 只有首次登录失败才展示验证码
            'first_login_failure_display' => true,
        ],
        'throttles'       => [
            // 是否开启登录限流
            'enable'        => true,
            // 最大尝试次数
            'max_attempts'  => 3,
            // 到达最大尝试次数后锁定时间 分钟
            'decay_minutes' => 1,
        ],
        'form_encryption' => [
            // 是否开启登录rsa加密
            'enable'      => false,
            // 私钥
            'private_key' => '',
            // 公钥
            'public_key'  => '',
        ],
    ],
]
```

# 使用

## 验证码

采用 [mews/captcha](https://packagist.org/packages/mews/captcha)

```shell
php artisan vendor:publish --provider="Mews\Captcha\CaptchaServiceProvider"
```

`config/captcha.php` 中增加 `laravel-admin` 字段

```php
    'laravel-admin' => [
        // 长宽建议如此
        'width' => 120,
        'height' => 36,

        'length' => 4,
        'quality' => 90,
        'math' => false,
    ],
```

## 登录限流

采用自带 `Illuminate\Foundation\Auth\ThrottlesLogins` 组件处理

基于 `username` 和 `ip` 地址进行判断

如果需要清除帐号限制登录的话

不优雅且有效的办法 `php artisan cache:clear`

## `rsa` 加密

```shell
openssl genrsa -out rsa_2048_private.pem 2048
openssl rsa -pubout -in rsa_2048_private.pem -out rsa_2048_public.pem
```

运行后会 生成 `2048` 位 `rsa_2048_private.pem` `私钥` 与 `rsa_2048_public.pem` `公钥` 文件

将两文件内容粘贴至指定位置即可

如

```php
'form_encryption' => [
    // 是否开启登录rsa加密
    'enable'      => true,
    // 私钥
    'private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAwfXZb34rPqCLJW80Iq1UURoWmIvZM9iRGQDHMh5Ae6kieKUn
T4Lp2lupPagDkPGY+/aR4Ewi6w/4bRkaqW/DmkbTMqN47K8tHhYq+UySjz84plrO
BU/GvkC15r0t7pG5bedZ8iq1X0KGwjvwIMqj8yK99m+NFGbIp+Bfr0blCEjbDh0C
9HEdjq82eketlwJ2UABqJItU60Dn+Yfc8dEP4HptrC3NLm5qFCxDb7TKLaIdQ6Pb
IgTArcQJjs9Ld0wfEbLb/xtn78+ZnpINKV3hReXb8twT6oJMfew9fgJ8XS0n/R57
VK8Z6uZLPSrhuFRwDI8zkE/iqTGfWjagKDZsdwIDAQABAoIBAQCBEPHBiTmJkRdG
r7sHoENNPIjwtY6xO1lCRAxJQ2wxXZj8oxmHhVvL29bAmn7VAo+Eis0DbmaF+Lid
EGl3elG05ZBAUBt1lBP3w1SRU+zquW0e9anGSxG7JCBnDFf2Oq4jfiGSTe0nGIPH
2E6v42g5sFKsHArLGqJHY70IS6q4WWmEyP8FWZjXy7p67aAgzIxso5vDJ2OoJSFp
vukN1fkZKGD6d8p8MKCZnDKWlKIZ/sE5oFsv/cW05S7Oit0pvPxv6jAztWigysem
Qnxc10dxgA89wCNoXmwvw30VGHfEhzHe2kKb5FiMhtXgpUxSPGhvIJls2VBK5uOn
6bC0AZ9RAoGBAO4Z5TcdWxFg5sx8arUrhMIGJR97jfwAYgKWyvNB0WZQJ/QbgL+x
rf9utYsq+ikW/9iWKGONfh4XuR67H3aBV91wcMb+A1my/Fz7akDevTvEiE4XrU/l
cXA5mvjPyOr3er0FIJ9fpk5sJanXPZLsrjplpC3LBN8mXeYeTjhbRlQ7AoGBANCK
fg1Whlr475QWxpZBdNJBPStSCIT7ZYauqVA5WAnvLRexJT7eHsUO11SieBqfIoB1
KnoUL4je4wNpuD1gZ1jD131ynUu1lDMuIEfOBEksjja+SwqlsAirBkz6RS+X+b0U
uz9HwHWk3ZF4LSuHrf1S/c3RrMH/ieWAoN4QSnD1AoGAZY1F5ivvG4po5e5q1Lqq
9NYKH1NjS4geRVxrUzVPSoQFhmf0kc4GmFtvstuxtrTIimgA8fT3RX54xlKpKLIh
96BteXH9m9RrLn1M5D4cF9HLEQOoN9t0dpkbL+PvncfP4a6+KztwgyI9LiNPb8ri
xKV1UNK2UTbb9boXQpIrfOECgYAnV7loMsauKwCn45QbjMXJVN2BaryIuhTxT8pJ
hEgDt8e+DIGGVslfS+l74hreit8rdO9KkLxXChWx8yP7EFDvAiOJWzIpslv/O9iD
M27Dj1BVK5lkrNWFDR7nLuAWlolbDqo0ygiZKT1T0GRVLge5Hwsa5U++WcNeNOIk
gtZIcQKBgH7h/5td24CTkJtTnBN566cZHVbWBAGkqG4l+/pUlVJ7wM65vuxB1auc
BJsgcNJzJf+KqMUvVICa8S4aT9hRRd6jteVMRocm+NmL7JRPgPsT+gabKIEaQ22n
Kf6/wL9ZiX+ZRAunCkoIiRjUj0U5qRhMgTthzP4GQYOOzVYYH05j
-----END RSA PRIVATE KEY-----',
    // 公钥
    'public_key'  => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwfXZb34rPqCLJW80Iq1U
URoWmIvZM9iRGQDHMh5Ae6kieKUnT4Lp2lupPagDkPGY+/aR4Ewi6w/4bRkaqW/D
mkbTMqN47K8tHhYq+UySjz84plrOBU/GvkC15r0t7pG5bedZ8iq1X0KGwjvwIMqj
8yK99m+NFGbIp+Bfr0blCEjbDh0C9HEdjq82eketlwJ2UABqJItU60Dn+Yfc8dEP
4HptrC3NLm5qFCxDb7TKLaIdQ6PbIgTArcQJjs9Ld0wfEbLb/xtn78+ZnpINKV3h
ReXb8twT6oJMfew9fgJ8XS0n/R57VK8Z6uZLPSrhuFRwDI8zkE/iqTGfWjagKDZs
dwIDAQAB
-----END PUBLIC KEY-----',
],
```

# todo:

-   [ ] `rsa` 优雅的支持 `文件` `.env` 等配置
-   [ ] 支持 `i18n`
-   [ ] 防重放攻击

# 参考

-   <https://github.com/Iamtong/laravel-admin-login-check-safe>
