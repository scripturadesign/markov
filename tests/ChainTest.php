<?php

namespace Scriptura\Markov\Tests;

use Scriptura\Markov\Chain;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    private function tokenize($string)
    {
        return explode(' ', $string);
    }


    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function returns_empty_history()
    {
        $chain = new Chain();

        assertThat($chain->history(), is(emptyArray()));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function can_create_a_chain_with_history_data()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['c' => 1],
        ];
        $chain = new Chain($history);

        assertThat($chain->history(), is(identicalTo($history)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::train
     */
    public function training_generates_history()
    {
        $chain = new Chain();
        $chain->train($this->tokenize('a b b b c'));

        $expectedHistory = [
            'a' => ['b' => 1],
            'b' => ['b' => 2, 'c' => 1],
        ];

        assertThat($chain->history(), is(identicalTo($expectedHistory)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::train
     */
    public function training_builds_on_existing_history()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['c' => 1],
        ];
        $chain = new Chain($history);
        $chain->train($this->tokenize('a b a'));

        $expectedHistory = [
            'a' => ['b' => 2],
            'b' => ['c' => 1, 'a' => 1],
        ];

        assertThat($chain->history(), is(identicalTo($expectedHistory)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::train
     */
    public function train_multiple_times()
    {
        $chain = new Chain();
        $chain->train($this->tokenize('a b c'));
        $chain->train($this->tokenize('c a d b'));
        $chain->train($this->tokenize('a c d b'));
        $chain->train($this->tokenize('c a b e'));

        $expectedHistory = [
            'a' => ['b' => 2, 'd' => 1, 'c' => 1],
            'b' => ['c' => 1, 'e' => 1],
            'c' => ['a' => 2, 'd' => 1],
            'd' => ['b' => 2],
        ];

        assertThat($chain->history(), is(identicalTo($expectedHistory)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::train
     */
    public function empty_array_if_querying_undefined_key_in_history()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['c' => 1],
        ];
        $chain = new Chain($history);

        assertThat($chain->queryHistory('g'), is(emptyArray()));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::train
     */
    public function query_a_key_in_history()
    {
        $history = [
            'a' => ['b' => 2, 'd' => 1, 'c' => 1],
            'b' => ['c' => 1, 'e' => 1],
            'c' => ['a' => 2, 'd' => 1],
            'd' => ['b' => 2],
        ];
        $chain = new Chain($history);

        $expected = ['c' => 1, 'e' => 1];

        assertThat($chain->queryHistory('b'), is(identicalTo($expected)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     */
    public function calculates_probability_matrix()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['b' => 2, 'c' => 1, 'd' => 1],
        ];
        $chain = new Chain($history);

        $expected = [
            'a' => ['b' => 1],
            'b' => ['b' => 0.5, 'c' => 0.25, 'd' => 0.25],
        ];

        assertThat($chain->matrix(), is(identicalTo($expected)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     */
    public function taining_recalculates_probability_matrix()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['b' => 2, 'c' => 1, 'd' => 1],
        ];
        $chain = new Chain($history);

        $matrixBefore = $chain->matrix();
        $chain->matrix();
        $chain->train($this->tokenize('a c c a'));
        $matrixAfter = $chain->matrix();

        $expectedBefore = [
            'a' => ['b' => 1],
            'b' => ['b' => 0.5, 'c' => 0.25, 'd' => 0.25],
        ];

        $expectedAfter = [
            'a' => ['b' => 0.5, 'c' => 0.5],
            'b' => ['b' => 0.5, 'c' => 0.25, 'd' => 0.25],
            'c' => ['c' => 0.5, 'a' => 0.5],
        ];

        assertThat($matrixBefore, is(identicalTo($expectedBefore)));
        assertThat($matrixAfter, is(identicalTo($expectedAfter)));
    }
}
