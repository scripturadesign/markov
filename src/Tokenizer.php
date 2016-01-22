<?php
/**
 * @package    Scriptura\Markov
 * @author     Martin Dilling-Hansen <martindilling@gmail.com>
 * @copyright  Copyright (c) 2016, Martin Dilling-Hansen
 * @license    http://opensource.org/licenses/MIT MIT License
 * @link       https://github.com/scripturadesign/markov
 */

namespace Scriptura\Markov;

interface Tokenizer
{
    /**
     * Convert a string into an array of tokens.
     *
     * @param string $string
     *
     * @return array
     */
    public function tokenize($string);
}
