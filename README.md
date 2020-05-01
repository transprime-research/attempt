<p align="center">
<img src="https://github.com/transprime-research/assets/blob/master/attempt/twitter_header_photo_2.png">
</p>

<p align="center">
<a href="https://travis-ci.com/transprime-research/attempt"> <img src="https://travis-ci.org/transprime-research/attempt.svg?branch=master" alt="Build Status"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/v/stable" alt="Latest Stable Version"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/downloads" alt="Total Downloads"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/v/unstable" alt="Latest Unstable Version"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/d/monthly" alt="Latest Monthly Downloads"/></a>
  <a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/license" alt="License"/></a>

## About Attempt
Try and catch in php objected oriented way

## Usage

```php
attempt(fn() => $this->client->request($method, $uri, $options))
    ->catch(ConnectException::class)
    ->done();
```


## Additional Information

Be aware that this package is part of a series of "The Proof Of Concept".

See other packages in this series here:

- https://github.com/transprime-research/piper [Smart Piping in PHP]
- https://github.com/omitobi/conditional [A smart PHP if...elseif...else statement]
- https://github.com/transprime-research/arrayed [A smarter Array now like an object]
- https://github.com/omitobi/carbonate [A smart Carbon + Collection package]
- https://github.com/omitobi/laravel-habitue [Jsonable Http Request(er) package with Collections response]

## Similar packages

- TBA

## Licence

MIT (See LICENCE file)
