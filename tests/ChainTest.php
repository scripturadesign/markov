<?php
declare(strict_types=1);

namespace Scriptura\Markov\Tests;

use PHPUnit\Framework\TestCase;
use Scriptura\Markov\Chain;
use Scriptura\Markov\Link;

class ChainTest extends TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function defaults_to_empty_history() : void
    {
        $chain = new Chain(1);

        $this->assertEquals([], $chain->history());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::order
     */
    public function can_get_order() : void
    {
        $chain = new Chain(3);

        $this->assertEquals(3, $chain->order());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::__construct
     * @covers \Scriptura\Markov\Chain::history
     */
    public function can_create_a_chain_with_history_data() : void
    {
        $history = [
            new Link([''], ['a' => 1]),
            new Link(['a'], ['b' => 1]),
            new Link(['b'], ['' => 1]),
        ];
        $chain = new Chain(1, $history);

        $this->assertEquals($history, $chain->history());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::learn
     */
    public function learning_generates_history() : void
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b']);

        $expectedHistory = [
            new Link([''], ['a' => 1]),
            new Link(['a'], ['b' => 1]),
            new Link(['b'], ['' => 1]),
        ];

        $this->assertEquals($expectedHistory, $chain->history());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::learn
     */
    public function learning_builds_on_existing_history() : void
    {
        $history = [
            new Link([''], ['a' => 1]),
            new Link(['a'], ['b' => 1]),
            new Link(['b'], ['' => 1]),
        ];
        $chain = new Chain(1, $history);
        $chain->learn(['a', 'b', 'a']);

        $expectedHistory = [
            new Link([''], ['a' => 2]),
            new Link(['a'], ['b' => 2, '' => 1]),
            new Link(['b'], ['' => 1, 'a' => 1]),
        ];

        $this->assertEquals($expectedHistory, $chain->history());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::learn
     */
    public function learn_multiple_times() : void
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'a']);
        $chain->learn(['b', 'a']);

        $expectedHistory = [
            new Link([''], ['a' => 2, 'b' => 1]),
            new Link(['a'], ['b' => 1, '' => 2, 'a' => 1]),
            new Link(['b'], ['' => 1, 'a' => 1]),
        ];

        $this->assertEquals($expectedHistory, $chain->history());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::find
     */
    public function empty_array_if_get_undefined_key_in_history() : void
    {
        $history = [
            new Link([''], ['a' => 1]),
            new Link(['a'], ['b' => 1]),
            new Link(['b'], ['' => 1]),
        ];
        $chain = new Chain(1, $history);

        $this->assertEquals(Link::null(), $chain->find(['no']));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::find
     */
    public function get_data_for_a_key_in_history() : void
    {
        $history = [
            new Link([''], ['a' => 1]),
            new Link(['a'], ['b' => 1]),
            $expected = new Link(['b'], ['' => 1]),
        ];
        $chain = new Chain(1, $history);

        $this->assertEquals($expected, $chain->find(['b']));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function history_for_second_order_chain() : void
    {
        $chain = new Chain(2);
        $chain->learn(['a', 'n', 'a', 'n', 'a', 's']);

        $expectedHistory = [
            new Link(['', ''], ['a' => 1]),
            new Link(['', 'a'], ['n' => 1]),
            new Link(['a', 'n'], ['a' => 2]),
            new Link(['n', 'a'], ['n' => 1, 's' => 1]),
            new Link(['a', 's'], ['' => 1]),
        ];

        $this->assertEquals($expectedHistory, $chain->history());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Chain::history
     */
    public function history_for_third_order_chain() : void
    {
        $chain = new Chain(3);
        $chain->learn(['a', 'n', 'a', 'n', 'a', 's']);

        $expectedHistory = [
            new Link(['', '', ''], ['a' => 1]),
            new Link(['', '', 'a'], ['n' => 1]),
            new Link(['', 'a', 'n'], ['a' => 1]),
            new Link(['a', 'n', 'a'], ['n' => 1, 's' => 1]),
            new Link(['n', 'a', 'n'], ['a' => 1]),
            new Link(['n', 'a', 's'], ['' => 1]),
        ];

        $this->assertEquals($expectedHistory, $chain->history());
    }
}
