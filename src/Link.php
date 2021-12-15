<?php

declare(strict_types=1);

namespace Scriptura\Markov;

class Link
{
    private bool $needsRecalculation = true;
    /** @var array<string, float> */
    private array $predictions = [];

    final public function __construct(
        /** @var array<int, string> */
        private array $state = [],
        /** @var array<string, int> */
        private array $transitions = [],
    ) {
    }

    public static function null(): static
    {
        return new static();
    }

    public function isNull(): bool
    {
        return $this->state === [] && $this->transitions === [];
    }

    public function state(): array
    {
        return $this->state;
    }

    public function transitions(): array
    {
        return $this->transitions;
    }

    public function predictions(): array
    {
        if ($this->needsRecalculation) {
            $this->recalculate();
            $this->needsRecalculation = false;
        }

        return $this->predictions;
    }

    private function recalculate(): void
    {
        $this->predictions = [];

        $total = array_sum($this->transitions);
        foreach ($this->transitions as $transition => $count) {
            $this->predictions[$transition] = $count / $total;
        }
    }

    public function add(string $next): void
    {
        if (! isset($this->transitions[$next])) {
            $this->transitions[$next] = 0;
        }
        $this->transitions[$next]++;
        $this->needsRecalculation = true;
    }
}
