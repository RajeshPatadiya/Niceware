# Niceware for PHP

PHP port of [npm: niceware](https://www.npmjs.com/package/niceware) ([GitHub: diracdeltas/niceware](https://github.com/diracdeltas/niceware))

## Why?

I wanted to write something for fun.
Until I put a more reasonable explanation here - assume EXPERIMENTAL status and use at your own risk!

## Public API Reference

```php
namespace Narf\Niceware;

class Narf\Niceware\Niceware {

	public static function generatePassphrase(int $size): string;
	public static function bytesToPassphrase(string $bytes): string;
	public static function passphraseToBytes(string $passphrase): string;
```

*Note: The parameters types are not actually in the method signatures, but
       validated via `is_string()`, `is_int()` calls. This is because PHP's
       ugly ``strict_types`` declaration is non-enforceable.*
