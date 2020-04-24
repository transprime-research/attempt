# trycatch
Try and catch in php objected oriented way

## Usage

```php
attempt(fn() => $this->client->request($method, $uri, $options))
    ->catch(ConnectException::class)
    ->done();
```
