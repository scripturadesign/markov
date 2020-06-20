# Markov Chain

[![Latest Version on Packagist][ico-version]][link-packagist]
[![GitHub Tests Action Status][ico-workflows]][link-workflows]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]][link-license]

Markov Chain implementation.  
You train it by giving it arrays of tokens. Then you can get the occurrences and probability of 
tokens after a given token.

## Installation

You can install the package via composer:

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

## Testing

``` bash
$ composer test
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email martindilling@gmail.com instead of using the issue tracker.

## Credits

- [Martin Dilling-Hansen][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File][link-license] for more information.

[ico-version]: https://img.shields.io/packagist/v/scripturadesign/markov.svg?style=flat-square
[ico-workflows]: https://img.shields.io/github/workflow/status/scripturadesign/markov/run-tests?label=tests
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/scripturadesign/markov.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/scripturadesign/markov
[link-workflows]: https://github.com/scripturadesign/markov/actions?query=workflow%3Arun-tests+branch%3Amaster
[link-downloads]: https://packagist.org/packages/scripturadesign/markov
[link-license]: LICENSE.md
[link-author]: https://github.com/martindilling
[link-contributors]: ../../contributors
