<?php
declare(strict_types=1);

namespace Scriptura\Markov\Tests;

use PHPUnit\Framework\TestCase;
use Scriptura\Markov\Link;

class LinkTest extends TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\Link::state
     */
    public function can_get_state() : void
    {
        $link = new Link(['a']);

        $this->assertSame(['a'], $link->state());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::transitions
     */
    public function defaults_to_empty_transitions() : void
    {
        $link = new Link(['a']);

        $this->assertSame([], $link->transitions());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::null
     */
    public function can_construct_null() : void
    {
        $link = Link::null();

        $this->assertSame([], $link->state());
        $this->assertSame([], $link->transitions());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::isNull
     */
    public function can_check_if_null() : void
    {
        $link = Link::null();

        $this->assertTrue($link->isNull());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::add
     */
    public function can_add_transitions() : void
    {
        $link = new Link(['a']);
        $link->add('b');
        $link->add('b');
        $link->add('c');
        $link->add('d');

        $this->assertSame([
            'b' => 2,
            'c' => 1,
            'd' => 1,
        ], $link->transitions());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::__construct
     */
    public function can_construct_with_transitions() : void
    {
        $link = new Link(['a'], [
            'b' => 2,
            'c' => 1,
            'd' => 1,
        ]);

        $this->assertSame([
            'b' => 2,
            'c' => 1,
            'd' => 1,
        ], $link->transitions());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::predictions
     */
    public function defaults_to_empty_predictions() : void
    {
        $link = new Link(['a']);

        $this->assertSame([], $link->predictions());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::predictions
     */
    public function calculates_predictions() : void
    {
        $link = new Link(['a']);
        $link->add('b');
        $link->add('b');
        $link->add('c');
        $link->add('d');

        $this->assertSame([
            'b' => 0.5,
            'c' => 0.25,
            'd' => 0.25,
        ], $link->predictions());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Link::predictions
     * @covers \Scriptura\Markov\Link::recalculate
     */
    public function recalculates_predictions() : void
    {
        $link = new Link(['a']);
        $link->add('b');
        $link->add('c');
        $initial = $link->predictions();
        $link->add('b');
        $link->add('d');
        $recalculated = $link->predictions();

        $this->assertSame([
            'b' => 0.5,
            'c' => 0.5,
        ], $initial);
        $this->assertSame([
            'b' => 0.5,
            'c' => 0.25,
            'd' => 0.25,
        ], $recalculated);
    }
}
