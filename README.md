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
/* Create a new first order Markov Chain object */
$chain = new Chain(1);


/* Learn from arrays of tokens */
$chain->learn(['the', 'falcon', 'likes', 'the', 'snake']);


/* Get all the history of the training */
$chain->history();
// [
//     [
//         0 => [''],
//         1 => ['the'],
//         2 => ['falcon'],
//         3 => ['likes'],
//         4 => ['snake'],
//     ],
//     [
//         0 => ['the' => 1],
//         1 => ['falcon' => 1, 'snake' => 1],
//         2 => ['likes' => 1],
//         3 => ['the' => 1],
//         4 => ['' => 1],
//     ],
// ]
```

``` php
/* Create a new second order Markov Chain object */
$chain = new Chain(2);


/* Learn from arrays of tokens */
$chain->learn(['the', 'falcon', 'likes', 'the', 'snake']);


/* Get all the history of the training */
$chain->history();
// [
//     [
//         0 => ['', ''],
//         1 => ['', 'the'],
//         2 => ['the', 'falcon'],
//         3 => ['falcon', 'likes'],
//         4 => ['likes', 'the'],
//         5 => ['the', 'snake'],
//     ],
//     [
//         0 => ['the' => 1],
//         1 => ['falcon' => 1],
//         2 => ['likes' => 1],
//         3 => ['the' => 1],
//         4 => ['snake' => 1],
//         5 => ['' => 1],
//     ],
// ]
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
