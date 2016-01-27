<?php
/**
 * Copyright (c) 2016 Martin Dilling-Hansen <martindilling@gmail.com>
 * https://github.com/scripturadesign/markov
 */

namespace Scriptura\Markov\Tests;

use Scriptura\Markov\Chain;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function history_for_first_order_chain()
    {
        $chain = new Chain(1);
        $chain->learn(['the', 'falcon', 'likes', 'the', 'snake']);

        assertThat($chain->history(), is(
            [
                [
                    0 => [''],
                    1 => ['the'],
                    2 => ['falcon'],
                    3 => ['likes'],
                    4 => ['snake'],
                ],
                [
                    0 => ['the' => 1],
                    1 => ['falcon' => 1, 'snake' => 1],
                    2 => ['likes' => 1],
                    3 => ['the' => 1],
                    4 => ['' => 1],
                ],
            ]
        ));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function history_for_second_order_chain()
    {
        $chain = new Chain(2);
        $chain->learn(['the', 'falcon', 'likes', 'the', 'snake']);

        assertThat($chain->history(), is(
            [
                [
                    0 => ['', ''],
                    1 => ['', 'the'],
                    2 => ['the', 'falcon'],
                    3 => ['falcon', 'likes'],
                    4 => ['likes', 'the'],
                    5 => ['the', 'snake'],
                ],
                [
                    0 => ['the' => 1],
                    1 => ['falcon' => 1],
                    2 => ['likes' => 1],
                    3 => ['the' => 1],
                    4 => ['snake' => 1],
                    5 => ['' => 1],
                ],
            ]
        ));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function history_for_third_order_chain()
    {
        $chain = new Chain(3);
        $chain->learn(['the', 'falcon', 'likes', 'the', 'snake']);

        assertThat($chain->history(), is(
            [
                [
                    0 => ['', '', ''],
                    1 => ['', '', 'the'],
                    2 => ['', 'the', 'falcon'],
                    3 => ['the', 'falcon', 'likes'],
                    4 => ['falcon', 'likes', 'the'],
                    5 => ['likes', 'the', 'snake'],
                ],
                [
                    0 => ['the' => 1],
                    1 => ['falcon' => 1],
                    2 => ['likes' => 1],
                    3 => ['the' => 1],
                    4 => ['snake' => 1],
                    5 => ['' => 1],
                ],
            ]
        ));
    }

}
