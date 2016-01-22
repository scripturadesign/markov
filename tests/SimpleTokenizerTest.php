<?php
/**
 * @package    Scriptura\Markov
 * @author     Martin Dilling-Hansen <martindilling@gmail.com>
 * @copyright  Copyright (c) 2016, Martin Dilling-Hansen
 * @license    http://opensource.org/licenses/MIT MIT License
 * @link       https://github.com/scripturadesign/markov
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
