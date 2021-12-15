<?php

declare(strict_types=1);

namespace Scriptura\Markov\Tests;

use PHPUnit\Framework\TestCase;
use Scriptura\Markov\Chain;
use Scriptura\Markov\Generator;
use Scriptura\Markov\Link;
use Scriptura\Markov\RNG\DeterministicRNG;
use Scriptura\Markov\RNG\RandomIntRNG;

class GeneratorTest extends TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\Generator::__construct
     */
    public function construct_without_errors(): void
    {
        $rng = new RandomIntRNG();
        $chain = new Chain(1);
        $generator = new Generator($rng, $chain);

        $this->assertInstanceOf(Generator::class, $generator);
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::next
     */
    public function next_suggestion_empty(): void
    {
        $link = new Link(['a']);
        $chain = new Chain(1, [$link]);
        $generator = new Generator(new RandomIntRNG(), $chain);

        $this->assertSame('', $generator->next($chain->find(['a'])));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::next
     */
    public function get_suggestion_to_next_b(): void
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'c']);
        $rng = new DeterministicRNG();
        $rng->determinedResult = 0;
        $generator = new Generator($rng, $chain);

        $this->assertSame('b', $generator->next($chain->find(['a'])));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::next
     */
    public function get_suggestion_to_next_c(): void
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'c']);
        $rng = new DeterministicRNG();
        $rng->determinedResult = (1 * 100) + 1;
        $generator = new Generator($rng, $chain);

        $this->assertSame('c', $generator->next($chain->find(['a'])));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::generate
     */
    public function generate_empty_result(): void
    {
        $chain = new Chain(1);
        $generator = new Generator(new RandomIntRNG(), $chain);

        $this->assertEquals([], $generator->generate());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::generate
     */
    public function generate_simple_result(): void
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b', 'c']);
        $generator = new Generator(new RandomIntRNG(), $chain);

        $this->assertEquals(['a', 'b', 'c'], $generator->generate());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::generate
     */
    public function generate_result(): void
    {
        $count = ['ab' => 0, 'ac' => 0];
        $chain = new Chain(1);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'c']);
        $generator = new Generator(new RandomIntRNG(), $chain);

        foreach (range(1, 100) as $index) {
            $next = implode('', $generator->generate());
            $count[$next]++;
        }

        $this->assertGreaterThan($count['ac'], $count['ab']);
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::generate
     */
    public function generate_b(): void
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'c']);
        $rng = new DeterministicRNG();
        $rng->determinedResult = 1;
        $generator = new Generator($rng, $chain);

        $this->assertSame(['a', 'b'], $generator->generate());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::generate
     */
    public function generate_c(): void
    {
        $chain = new Chain(1);
        $chain->learn(['a', 'b']);
        $chain->learn(['a', 'c']);
        $rng = new DeterministicRNG();
        $rng->determinedResult = 2;
        $generator = new Generator($rng, $chain);

        $this->assertSame(['a', 'c'], $generator->generate());
    }

    /**
     * @test
     * @covers \Scriptura\Markov\Generator::generate
     */
    public function generate_result_higher_order(): void
    {
        $chain = new Chain(3);
        $chain->learn(str_split('aaabbb'));
        $chain->learn(str_split('bbbccc'));
        $generator = new Generator(new RandomIntRNG(), $chain);

        $found = [];
        foreach (range(1, 50) as $index) {
            $generated = implode('', $generator->generate());
            $found[$generated] = true;
        }
        ksort($found);

        $this->assertSame(['aaabbb', 'aaabbbccc', 'bbb', 'bbbccc'], array_keys($found));
    }
}
