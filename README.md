<p align="center">
<img src="https://github.com/transprime-research/assets/blob/master/attempt/twitter_header_photo_2.png">
</p>

<p align="center">
<a href="https://travis-ci.org/transprime-research/attempt"> <img src="https://travis-ci.org/transprime-research/attempt.svg?branch=master" alt="Build Status"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/v/stable" alt="Latest Stable Version"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/downloads" alt="Total Downloads"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/v/unstable" alt="Latest Unstable Version"/></a>
<a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/d/monthly" alt="Latest Monthly Downloads"/></a>
  <a href="https://packagist.org/packages/transprime-research/attempt"> <img src="https://poser.pugx.org/transprime-research/attempt/license" alt="License"/></a>

## About Attempt
Try and catch in php objected oriented way
> Do It like a pro :ok:

## Usage

```php
$response = attempt(fn() => $this->client->get('ninja'))
    ->catch(ConnectException::class)
    ->done(fn() => []); //done can be replaced with ()
```

## Installation:

Minimum requirement is PHP 7.2+ and Composer.

Install with this:

```shell script
composer require transprime-research/attempt
```

## Other usage:

```php
$response = Attempt::on(fn() => $this->client->get('ninja'))
    ->catch(AttemptTestException::class)(); // or ->done()

// Do something with Response
```

`catch` method accepts an Exception object:

```php
$response = Attempt::on(fn() => $this->client->get('ninja'))
    ->catch(\AttemptTestException())(); // or ->done()

// Do something with Response
```

Set a default response:

```php
$response = Attempt::on(fn() => $this->client->get('ninja'))
    ->with(['abc'])
    ->catch(AttemptTestException::class)
    ->done(); // ['abc'] is returned if exception is caught  
```
```php
// with default value
attempt(fn() => $this->execute())
    ->catch(AttemptTestException::class, 'It is done') //returns 'It is done'
    ->done();
    
// closure as default value
attempt(fn() => $this->execute())
    ->catch(AttemptTestException::class, fn() => 'It is done') //returns 'It is done'
    ->done();
    
// handle the resolved default value in done()
attempt(fn() => $this->execute())
    ->catch(AttemptTestException::class, fn() => 'error') //returns 'It is done'
    ->done(fn(Exception $ex, $severity) => logger($severity, $ex));
```
Multiple Exception

```php
$response = Attempt::on(fn() => $this->client->get('ninja'))
    ->catch(AttemptTestException::class, HttpResponseException::class)()

// Do something with Response
```

Multiple Catch block

```php
attempt(fn() => $this->execute())
    ->catch(NinjaException::class, 'Ninja error') //returns 'It is done')
    ->catch(AnotherExeption::class, 'Another error') //returns 'It is done'
    ->done(fn($ex) => logger()->error($ex));
```

Do more with the caught Exception response:

```php
$response = Attempt::on(fn() => $this->client->get('ninja'))
    ->catch(AttemptTestException::class, HttpResponseException::class)
    ->done(fn(\HttpResponseException $e) => logger()->error($e->getMessage()));

// Do something with Response
```

More to come: Pass the execution of a default value to a callable Class

```php
// loading...
```

## Additional Information

This package is part of a series of "The Code Dare".

See other packages in this series here:

- https://github.com/transprime-research/piper [Smart Piping in PHP]
- https://github.com/transprime-research/arrayed [A smarter Array now like an object]
- https://github.com/omitobi/conditional [A smart PHP if...elseif...else statement]
- https://github.com/omitobi/carbonate [A smart Carbon + Collection package]
- https://github.com/omitobi/laravel-habitue [Jsonable Http Request(er) package with Collections response]

## Similar packages

- TBA

## Licence

MIT (See LICENCE file)
