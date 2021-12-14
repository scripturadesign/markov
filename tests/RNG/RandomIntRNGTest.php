<?php

namespace Scriptura\Markov\Tests\RNG;

use PHPUnit\Framework\TestCase;
use Scriptura\Markov\RNG\RandomIntRNG;

class RandomIntRNGTest extends TestCase
{
    /**
     * @test
     * @covers \Scriptura\Markov\RNG\RandomIntRNG::between
     */
    public function can_get_between_zero_and_zero(): void
    {
        $rng = new RandomIntRNG();
        $random = $rng->between(0, 0);

        $this->assertSame(0, $random);
    }

    /**
     * @test
     * @covers \Scriptura\Markov\RNG\RandomIntRNG::between
     */
    public function can_change_determined_result(): void
    {
        $rng = new RandomIntRNG();
        $random = $rng->between(0, 2);

        $this->assertGreaterThanOrEqual(0, $random);
        $this->assertLessThanOrEqual(2, $random);
    }
}
