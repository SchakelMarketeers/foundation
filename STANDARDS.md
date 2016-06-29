# Programming Standards

For this project we will be using [PSR-1][psr1], [PSR-2][psr2] and
[PSR-4][psr4], with a few modifications as listed below.

## Amendments to PSR-1

PSR-1 is applied as-is, without amendments.

## Amendments to PSR-2

In [chapter 4.3][psr2-43], the following style guide is mentioned:

> Method names MUST NOT be declared with a space after the method name. The
> opening brace MUST go on its own line, and the closing brace MUST go on the
> next line following the body. There MUST NOT be a space after the opening
> parenthesis, and there MUST NOT be a space before the closing parenthesis.
>
> — <cite>"PSR-2: Coding Style Guide" - Paul M. Jones, PHP-FIG</cite>

We will deviate from this, and our definition is as follows.

Method names MUST NOT be declared with a space after the method name. The opening brace MUST go *on the same line as the method declaration*, and the closing brace MUST go on the next line following the body. There MUST be a space before the opening parenthesis, but there MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.

With our amendments, a method declaration looks like the following. Note the
placement of parentheses, commas, spaces, and braces:

```php
<?php
namespace Vendor\Package;

class ClassName
{
    public function fooBarBaz($arg1, &$arg2, $arg3 = []) {
        // method body
    }
}
```

There are no further amendments.

## Amendments to PSR-4

PSR-4 is applied as-is, without amendments. The rules will be enforced by
[Composer][composer] and we will define our autoloaders in the `composer.json`
file.

[psr1]: http://www.php-fig.org/psr/psr-1/
[psr2]: http://www.php-fig.org/psr/psr-2/
[psr4]: http://www.php-fig.org/psr/psr-4/

[psr2-43]: http://www.php-fig.org/psr/psr-2/#4-3-methods
[composer]: https://www.getcomposer.org/