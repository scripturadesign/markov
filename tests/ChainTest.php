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
    public function defaults_to_empty_history()
    {
        $chain = new Chain(1);

        assertThat($chain->history(), is(['states' => [], 'transitions' => []]));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::__construct
     * @covers \Scriptura\Markov\Chain::history
     */
    public function can_create_a_chain_with_history_data()
    {
        $history = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
            ],
            'transitions' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['' => 1],
            ],

        ];
        $chain = new Chain(1, $history);

        assertThat($chain->history(), is(identicalTo($history)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::learn
     */
    public function learning_generates_history()
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b', 'b', 'b', 'c']);

        $expectedHistory = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
                3 => ['c'],
            ],
            'transitions' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['b' => 2, 'c' => 1],
                3 => ['' => 1],
            ],
        ];

        assertThat($chain->history(), is(identicalTo($expectedHistory)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::learn
     */
    public function learning_builds_on_existing_history()
    {
        $history = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
            ],
            'transitions' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['' => 1],
            ],
        ];
        $chain = new Chain(1, $history);
        $chain->learn(['a', 'b', 'a']);

        $expectedHistory = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
            ],
            'transitions' => [
                0 => ['a' => 2],
                1 => ['b' => 2, '' => 1],
                2 => ['' => 1, 'a' => 1],
            ],
        ];

        assertThat($chain->history(), is(identicalTo($expectedHistory)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::learn
     */
    public function learn_multiple_times()
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b', 'c']);
        $chain->learn(['c', 'a', 'd', 'b']);
        $chain->learn(['a', 'c', 'd', 'b']);
        $chain->learn(['c', 'a', 'b', 'e']);

        $expectedHistory = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
                3 => ['c'],
                4 => ['d'],
                5 => ['e'],
            ],
            'transitions' => [
                0 => ['a' => 2, 'c' => 2],
                1 => ['b' => 2, 'd' => 1, 'c' => 1],
                2 => ['c' => 1, '' => 2, 'e' => 1],
                3 => ['' => 1, 'a' => 2, 'd' => 1],
                4 => ['b' => 2],
                5 => ['' => 1],
            ],
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
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
                3 => ['c'],
            ],
            'transitions' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['c' => 1],
                3 => ['' => 1],
            ],
        ];
        $chain = new Chain(1, $history);

        assertThat($chain->history(['no']), is(['state' => [], 'transitions' => []]));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function get_data_for_a_key_in_history()
    {
        $history = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
                3 => ['c'],
                4 => ['d'],
                5 => ['e'],
            ],
            'transitions' => [
                0 => ['a' => 2, 'c' => 2],
                1 => ['b' => 2, 'd' => 1, 'c' => 1],
                2 => ['c' => 1, '' => 2, 'e' => 1],
                3 => ['' => 1, 'a' => 2, 'd' => 1],
                4 => ['b' => 2],
                5 => ['' => 1],
            ],
        ];
        $chain = new Chain(1, $history);

        $expected = [
            'state' => ['b'],
            'transitions' => ['c' => 1, '' => 2, 'e' => 1],
        ];

        assertThat($chain->history(['b']), is(identicalTo($expected)));
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
            'states' => [
                0 => ['a'],
                1 => ['b'],
            ],
            'transitions' => [
                0 => ['b' => 1],
                1 => ['b' => 2, 'c' => 1, 'd' => 1],
            ],
        ];
        $chain = new Chain(1, $history);

        $expected = [
            'states' => [
                0 => ['a'],
                1 => ['b'],
            ],
            'probabilities' => [
                0 => ['b' => 1],
                1 => ['b' => 0.5, 'c' => 0.25, 'd' => 0.25],
            ],
        ];

        assertThat($chain->matrix(), is(identicalTo($expected)));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     * @covers \Scriptura\Markov\Chain::recalculateMatrix
     * @covers \Scriptura\Markov\Chain::calculateTransitionsProbability
     */
    public function learning_recalculates_probability_matrix()
    {
        $history = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
            ],
            'transitions' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['b' => 1, '' => 1],
            ],
        ];
        $chain = new Chain(1, $history);

        $matrixBefore = $chain->matrix();
        $chain->matrix();
        $chain->learn(['b', 'b', 'c', 'c']);
        $matrixAfter = $chain->matrix();

        $expectedBefore = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
            ],
            'probabilities' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['b' => 0.5, '' => 0.5],
            ],
        ];

        $expectedAfter = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
                3 => ['c'],
            ],
            'probabilities' => [
                0 => ['a' => 0.5, 'b' => 0.5],
                1 => ['b' => 1],
                2 => ['b' => 0.5, '' => 0.25, 'c' => 0.25],
                3 => ['c' => 0.5, '' => 0.5],
            ],
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
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
            ],
            'transitions' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['b' => 1, '' => 1],
            ],
        ];
        $chain = new Chain(1, $history);

        assertThat($chain->matrix('no'), is(['state' => [], 'probabilities' => []]));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::matrix
     */
    public function get_data_for_a_key_in_probability_matrix()
    {
        $history = [
            'states' => [
                0 => [''],
                1 => ['a'],
                2 => ['b'],
            ],
            'transitions' => [
                0 => ['a' => 1],
                1 => ['b' => 1],
                2 => ['b' => 1, '' => 1],
            ],
        ];
        $chain = new Chain(1, $history);

        $expected = [
            'state' => ['b'],
            'probabilities' => ['b' => 0.5, '' => 0.5],
        ];

        assertThat($chain->matrix(['b']), is(identicalTo($expected)));
    }


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
                'states' => [
                    0 => [''],
                    1 => ['the'],
                    2 => ['falcon'],
                    3 => ['likes'],
                    4 => ['snake'],
                ],
                'transitions' => [
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
                'states' => [
                    0 => ['', ''],
                    1 => ['', 'the'],
                    2 => ['the', 'falcon'],
                    3 => ['falcon', 'likes'],
                    4 => ['likes', 'the'],
                    5 => ['the', 'snake'],
                ],
                'transitions' => [
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
                'states' => [
                    0 => ['', '', ''],
                    1 => ['', '', 'the'],
                    2 => ['', 'the', 'falcon'],
                    3 => ['the', 'falcon', 'likes'],
                    4 => ['falcon', 'likes', 'the'],
                    5 => ['likes', 'the', 'snake'],
                ],
                'transitions' => [
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
     */
    public function names_example_first_order()
    {
        $chain = new Chain(1);
        $chain->learn(str_split('emma'));
        $chain->learn(str_split('alma'));
        $chain->learn(str_split('agnes'));
        $chain->learn(str_split('anna'));
        $chain->learn(str_split('ella'));
        $chain->learn(str_split('ellie'));
        $chain->learn(str_split('alberte'));
        $chain->learn(str_split('asta'));
        $chain->learn(str_split('aya'));
        $chain->learn(str_split('ellen'));
        $chain->learn(str_split('esther'));
        $chain->learn(str_split('astrid'));
        $chain->learn(str_split('andrea'));
        $chain->learn(str_split('emilie'));
        $chain->learn(str_split('alba'));

        assertThat($chain->history(), is(
            [
                'states' => [
                    0 => [''],
                    1 => ['e'],
                    2 => ['m'],
                    3 => ['a'],
                    4 => ['l'],
                    5 => ['g'],
                    6 => ['n'],
                    7 => ['s'],
                    8 => ['i'],
                    9 => ['b'],
                    10 => ['r'],
                    11 => ['t'],
                    12 => ['y'],
                    13 => ['h'],
                    14 => ['d'],
                ],
                'transitions' => [
                    0 => ['e' => 6, 'a' => 9],
                    1 => ['m' => 2, 's' => 2, 'l' => 3, '' => 3, 'r' => 2, 'n' => 1, 'a' => 1],
                    2 => ['m' => 1, 'a' => 2, 'i' => 1],
                    3 => ['' => 8, 'l' => 3, 'g' => 1, 'n' => 2, 's' => 2, 'y' => 1],
                    4 => ['m' => 1, 'l' => 3, 'a' => 1, 'i' => 2, 'b' => 2, 'e' => 1],
                    5 => ['n' => 1],
                    6 => ['e' => 1, 'n' => 1, 'a' => 1, '' => 1, 'd' => 1],
                    7 => ['' => 1, 't' => 3],
                    8 => ['e' => 2, 'd' => 1, 'l' => 1],
                    9 => ['e' => 1, 'a' => 1],
                    10 => ['t' => 1, '' => 1, 'i' => 1, 'e' => 1],
                    11 => ['e' => 1, 'a' => 1, 'h' => 1, 'r' => 1],
                    12 => ['a' => 1],
                    13 => ['e' => 1],
                    14 => ['' => 1, 'r' => 1],
                ],
            ]
        ));

        assertThat($chain->matrix(), is(
            [
                'states' => [
                    0 => [''],
                    1 => ['e'],
                    2 => ['m'],
                    3 => ['a'],
                    4 => ['l'],
                    5 => ['g'],
                    6 => ['n'],
                    7 => ['s'],
                    8 => ['i'],
                    9 => ['b'],
                    10 => ['r'],
                    11 => ['t'],
                    12 => ['y'],
                    13 => ['h'],
                    14 => ['d'],
                ],
                'probabilities' => [
                    0 => ['e' => 0.4, 'a' => 0.6],
                    1 => [
                        'm' => 0.14285714285714285,
                        's' => 0.14285714285714285,
                        'l' => 0.21428571428571427,
                        '' => 0.21428571428571427,
                        'r' => 0.14285714285714285,
                        'n' => 0.07142857142857142,
                        'a' => 0.07142857142857142,
                    ],
                    2 => ['m' => 0.25, 'a' => 0.5, 'i' => 0.25],
                    3 => [
                        '' => 0.47058823529411764,
                        'l' => 0.17647058823529413,
                        'g' => 0.058823529411764705,
                        'n' => 0.11764705882352941,
                        's' => 0.11764705882352941,
                        'y' => 0.058823529411764705,
                    ],
                    4 => ['m' => 0.1, 'l' => 0.3, 'a' => 0.1, 'i' => 0.2, 'b' => 0.2, 'e' => 0.1],
                    5 => ['n' => 1],
                    6 => ['e' => 0.2, 'n' => 0.2, 'a' => 0.2, '' => 0.2, 'd' => 0.2],
                    7 => ['' => 0.25, 't' => 0.75],
                    8 => ['e' => 0.5, 'd' => 0.25, 'l' => 0.25],
                    9 => ['e' => 0.5, 'a' => 0.5],
                    10 => ['t' => 0.25, '' => 0.25, 'i' => 0.25, 'e' => 0.25],
                    11 => ['e' => 0.25, 'a' => 0.25, 'h' => 0.25, 'r' => 0.25],
                    12 => ['a' => 1],
                    13 => ['e' => 1],
                    14 => ['' => 0.5, 'r' => 0.5],
                ],
            ]
        ));
    }

    /**
     * @test
     */
    public function names_example_third_order()
    {
        $chain = new Chain(3);
        $chain->learn(str_split('emma'));
        $chain->learn(str_split('alma'));
        $chain->learn(str_split('ella'));
        $chain->learn(str_split('ellie'));
        $chain->learn(str_split('ellen'));
        $chain->learn(str_split('emilie'));
        $chain->learn(str_split('alba'));

        assertThat($chain->history(), is(
            [
                'states' => [
                    0 => ['', '', ''],
                    1 => ['', '', 'e'],
                    2 => ['', 'e', 'm'],
                    3 => ['e', 'm', 'm'],
                    4 => ['m', 'm', 'a'],
                    5 => ['', '', 'a'],
                    6 => ['', 'a', 'l'],
                    7 => ['a', 'l', 'm'],
                    8 => ['l', 'm', 'a'],
                    9 => ['', 'e', 'l'],
                    10 => ['e', 'l', 'l'],
                    11 => ['l', 'l', 'a'],
                    12 => ['l', 'l', 'i'],
                    13 => ['l', 'i', 'e'],
                    14 => ['l', 'l', 'e'],
                    15 => ['l', 'e', 'n'],
                    16 => ['e', 'm', 'i'],
                    17 => ['m', 'i', 'l'],
                    18 => ['i', 'l', 'i'],
                    19 => ['a', 'l', 'b'],
                    20 => ['l', 'b', 'a'],
                ],
                'transitions' => [
                    0 => ['e' => 5, 'a' => 2],
                    1 => ['m' => 2, 'l' => 3],
                    2 => ['m' => 1, 'i' => 1],
                    3 => ['a' => 1],
                    4 => ['' => 1],
                    5 => ['l' => 2],
                    6 => ['m' => 1, 'b' => 1],
                    7 => ['a' => 1],
                    8 => ['' => 1],
                    9 => ['l' => 3],
                    10 => ['a' => 1, 'i' => 1, 'e' => 1],
                    11 => ['' => 1],
                    12 => ['e' => 1],
                    13 => ['' => 2],
                    14 => ['n' => 1],
                    15 => ['' => 1],
                    16 => ['l' => 1],
                    17 => ['i' => 1],
                    18 => ['e' => 1],
                    19 => ['a' => 1],
                    20 => ['' => 1],
                ],
            ]
        ));

        assertThat($chain->matrix(), is(
            [
                'states' => [
                    0 => ['', '', ''],
                    1 => ['', '', 'e'],
                    2 => ['', 'e', 'm'],
                    3 => ['e', 'm', 'm'],
                    4 => ['m', 'm', 'a'],
                    5 => ['', '', 'a'],
                    6 => ['', 'a', 'l'],
                    7 => ['a', 'l', 'm'],
                    8 => ['l', 'm', 'a'],
                    9 => ['', 'e', 'l'],
                    10 => ['e', 'l', 'l'],
                    11 => ['l', 'l', 'a'],
                    12 => ['l', 'l', 'i'],
                    13 => ['l', 'i', 'e'],
                    14 => ['l', 'l', 'e'],
                    15 => ['l', 'e', 'n'],
                    16 => ['e', 'm', 'i'],
                    17 => ['m', 'i', 'l'],
                    18 => ['i', 'l', 'i'],
                    19 => ['a', 'l', 'b'],
                    20 => ['l', 'b', 'a'],
                ],
                'probabilities' => [
                    0 => ['e' => 0.7142857142857143, 'a' => 0.2857142857142857],
                    1 => ['m' => 0.4, 'l' => 0.6],
                    2 => ['m' => 0.5, 'i' => 0.5],
                    3 => ['a' => 1],
                    4 => ['' => 1],
                    5 => ['l' => 1],
                    6 => ['m' => 0.5, 'b' => 0.5],
                    7 => ['a' => 1],
                    8 => ['' => 1],
                    9 => ['l' => 1],
                    10 => ['a' => 0.3333333333333333, 'i' => 0.3333333333333333, 'e' => 0.3333333333333333],
                    11 => ['' => 1],
                    12 => ['e' => 1],
                    13 => ['' => 1],
                    14 => ['n' => 1],
                    15 => ['' => 1],
                    16 => ['l' => 1],
                    17 => ['i' => 1],
                    18 => ['e' => 1],
                    19 => ['a' => 1],
                    20 => ['' => 1],
                ],
            ]
        ));
    }
}
