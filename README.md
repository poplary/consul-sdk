# poplary/consul

## 安装

使用以下命令

```shell
composer require poplary/consul
```

## 使用

Consul 服务已经搭建完成，URL 为 http://xxx.xxx.xxx.xxx:8500

```php
// 获取 Consul 中已经注册的服务列表
$consulService = new ServiceFactory(['base_uri' => 'http://xxx.xxx.xxx.xxx:8500']);

// 获取不同 API 的服务
$catalog = $consulService->get(CatalogInterface::class);

// 调用 Consul 的 API 接口，返回的 $response 变量为一个 Poplary\Consul\ConsulResponse 对象，可以使用类的方法进行解析
$response = $catalog->service('service_name');

```

这个包提供的主要 API 都在 src/Services 目录下，对应的 API 可以在 Consul 的 [API 文档](https://www.consul.io/api-docs) 查看

```
AgentInterface.php
CatalogInterface.php
HealthInterface.php
KVInterface.php
SessionInterface.php
```
