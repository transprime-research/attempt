# trycatch
Try and catch in php objected oriented way

## Usage

```php
attempt(function ($method, $uri, $options) {
      return $this->client->request('get','http://ninja.example/users');
  })
    ->using($method, $uri, $options)
    ->catch(ConnectException::class)
    ->then();
```
