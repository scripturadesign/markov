<?php
/**
 * Copyright (c) 2016 Martin Dilling-Hansen <martindilling@gmail.com>.
 * https://github.com/scripturadesign/markov
 */

namespace Scriptura\Markov\Tests;

use Scriptura\Markov\Chain;
use Scriptura\Markov\SimpleTokenizer;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function defaults_to_empty_history()
    {
        $chain = new Chain(new SimpleTokenizer());

        assertThat($chain->history(), is(emptyArray()));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::__construct
     * @covers \Scriptura\Markov\Chain::history
     */
    public function can_create_a_chain_with_history_data()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['c' => 1],
        ];
        $chain = new Chain(new SimpleTokenizer(), $history);

        assertThat($chain->history(), is(identicalTo($history)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::train
     */
    public function training_generates_history()
    {
        $chain = new Chain(new SimpleTokenizer());
        $chain->train('a b b b c');

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
        $chain = new Chain(new SimpleTokenizer(), $history);
        $chain->train('a b a');

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
        $chain = new Chain(new SimpleTokenizer());
        $chain->train('a b c');
        $chain->train('c a d b');
        $chain->train('a c d b');
        $chain->train('c a b e');

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
     * @covers \Scriptura\Markov\Chain::history
     */
    public function empty_array_if_get_undefined_key_in_history()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['c' => 1],
        ];
        $chain = new Chain(new SimpleTokenizer(), $history);

        assertThat($chain->history('no'), is(emptyArray()));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function get_data_for_a_key_in_history()
    {
        $history = [
            'a' => ['b' => 2, 'd' => 1, 'c' => 1],
            'b' => ['c' => 1, 'e' => 1],
            'c' => ['a' => 2, 'd' => 1],
            'd' => ['b' => 2],
        ];
        $chain = new Chain(new SimpleTokenizer(), $history);

        $expected = ['c' => 1, 'e' => 1];

        assertThat($chain->history('b'), is(identicalTo($expected)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     * @covers \Scriptura\Markov\Chain::recalculateMatrix
     * @covers \Scriptura\Markov\Chain::calculateTransitionsProbability
     */
    public function calculates_probability_matrix()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['b' => 2, 'c' => 1, 'd' => 1],
        ];
        $chain = new Chain(new SimpleTokenizer(), $history);

        $expected = [
            'a' => ['b' => 1],
            'b' => ['b' => 0.5, 'c' => 0.25, 'd' => 0.25],
        ];

        assertThat($chain->matrix(), is(identicalTo($expected)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     * @covers \Scriptura\Markov\Chain::recalculateMatrix
     * @covers \Scriptura\Markov\Chain::calculateTransitionsProbability
     */
    public function training_recalculates_probability_matrix()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['b' => 2, 'c' => 1, 'd' => 1],
        ];
        $chain = new Chain(new SimpleTokenizer(), $history);

        $matrixBefore = $chain->matrix();
        $chain->matrix();
        $chain->train('a c c a');
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

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     */
    public function empty_array_if_get_undefined_key_in_probability_matrix()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['b' => 2, 'c' => 1, 'd' => 1],
        ];
        $chain = new Chain(new SimpleTokenizer(), $history);

        assertThat($chain->matrix('no'), is(emptyArray()));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     */
    public function get_data_for_a_key_in_probability_matrix()
    {
        $history = [
            'a' => ['b' => 1],
            'b' => ['b' => 2, 'c' => 1, 'd' => 1],
        ];
        $chain = new Chain(new SimpleTokenizer(), $history);

        $expected = ['b' => 0.5, 'c' => 0.25, 'd' => 0.25];

        assertThat($chain->matrix('b'), is(identicalTo($expected)));
    }
}
