# Markov Chain

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Simple Markov Chain implementation.  
You train it by giving it arrays of tokens. Then you can get the occurrences and probability of 
tokens after a given token.

## Install

Via Composer:

``` bash
$ composer require scripturadesign/markov
```

## Usage

``` php
/* Create a new Markov Chain object */
$chain = new Chain();


/* Give it arrays of tokens */
$chain->train(['a', 'b', 'c']);
$chain->train(['c', 'a', 'd', 'b']);
$chain->train(['a', 'c', 'd', 'b']);
$chain->train(['c', 'a', 'b', 'e']);


/* Get all the history of the training */
$chain->history();
// [
//     'a' => ['b' => 2, 'd' => 1, 'c' => 1],
//     'b' => ['c' => 1, 'e' => 1],
//     'c' => ['a' => 2, 'd' => 1],
//     'd' => ['b' => 2],
// ]


/* Get the training history for a given token */
$chain->history('b');
// ['c' => 1, 'e' => 1]


/* Get the whole probability matrix */
$chain->matrix();
// [
//     'a' => ['b' => 0.5, 'd' => 0.25, 'c' => 0.25],
//     'b' => ['c' => 0.5, 'e' => 0.5],
//     'c' => ['a' => 0.66666666666666663, 'd' => 0.33333333333333331],
//     'd' => ['b' => 1],
// ]


/* Get the probability matrix for a given token */
$chain->matrix('b');
// ['c' => 0.5, 'e' => 0.5]
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Code Style

Easily check the code style against [PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) by running:

``` bash
$ composer sniff
```

And automatically fix them with this:

``` bash
$ composer fix
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email martindilling@gmail.com instead of using the issue tracker.

## Credits

- [Martin Dilling-Hansen][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/scripturadesign/markov.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/scripturadesign/markov/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/scripturadesign/markov.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/scripturadesign/markov.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/scripturadesign/markov.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/scripturadesign/markov
[link-travis]: https://travis-ci.org/scripturadesign/markov
[link-scrutinizer]: https://scrutinizer-ci.com/g/scripturadesign/markov/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/scripturadesign/markov
[link-downloads]: https://packagist.org/packages/scripturadesign/markov
[link-author]: https://github.com/martindilling
[link-contributors]: ../../contributors
