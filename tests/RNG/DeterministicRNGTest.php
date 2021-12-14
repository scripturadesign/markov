<?php

namespace Scriptura\Markov\Tests\RNG;

use PHPUnit\Framework\TestCase;
use Scriptura\Markov\RNG\DeterministicRNG;

class DeterministicRNGTest extends TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\RNG\DeterministicRNG::between
     */
    public function default_to_zero(): void
    {
        $rng = new DeterministicRNG();

        $this->assertSame(0, $rng->between(0, 100));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\RNG\DeterministicRNG::between
     */
    public function can_change_determined_result(): void
    {
        $rng = new DeterministicRNG();
        $rng->determinedResult = 10;

        $this->assertSame(10, $rng->between(0, 100));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\RNG\DeterministicRNG::between
     */
    public function will_cap_to_lowest(): void
    {
        $rng = new DeterministicRNG();
        $rng->determinedResult = -1;

        $this->assertSame(0, $rng->between(0, 100));
    }

    /**
     * @test
     * @covers \Scriptura\Markov\RNG\DeterministicRNG::between
     */
    public function will_cap_to_highest(): void
    {
        $rng = new DeterministicRNG();
        $rng->determinedResult = 101;

        $this->assertSame(100, $rng->between(0, 100));
    }
}
