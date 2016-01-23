<?php
/**
 * Copyright (c) 2016 Martin Dilling-Hansen <martindilling@gmail.com>
 * https://github.com/scripturadesign/markov
 */

namespace Scriptura\Markov\Tests;

use Scriptura\Markov\SimpleTokenizer;

class SimpleTokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\SimpleTokenizer::tokenize
     */
    public function splits_by_spaces()
    {
        $tokenizer = new SimpleTokenizer();

        assertThat($tokenizer->tokenize('a b c'), is(['a', 'b', 'c']));
    }
}
