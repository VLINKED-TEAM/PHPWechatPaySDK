
VLINKED PHP 拓展工具包

# 使用
composer.json 设置如下


```json
{
"require": {
    "vlinked/utils": "~1.0.0"
  }
}

```


或者使用命令行

```shell
composer require vlinked/utils

```

获得最新的开发版本，后续会整合固定版本


# 规则

## ⚠注意⚠

⚠**一律禁止出现任何账户安全信息的方法或者配置出现**⚠

只能以形式参数传递进来


### 命名规则

方法命名统一使用驼峰命名

#### 简写

|方法前缀|意思|
|---|---|
|gen|生成|
|get|获取|
|to|转化|


### PHPDoc规则

必须
- 函数的中文解释
- 参数中文注解 类型限制
- 抛出异常注解
- 返回值注解

非必须

- 作者注解
- 版本限制注解

# 文件说明

- Arrays 数组相关系
- Date 时间日期相关的
- String 字符串处理
- Validators 验证类


>积微成著 微联可达