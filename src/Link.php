<?php
declare(strict_types=1);

namespace Scriptura\Markov;

class Link
{
    private bool $needsRecalculation = true;
    private array $state;
    private array $transitions;
    /** @var array<string, float> */
    private array $predictions = [];

    /**
     * @param array<int, string> $state
     * @param array<string, int> $transitions
     */
    final public function __construct(array $state = [], array $transitions = [])
    {
        $this->state = $state;
        $this->transitions = $transitions;
    }

    public static function null() : self
    {
        return new static();
    }

    public function isNull() : bool
    {
        return $this->state === [] && $this->transitions === [];
    }

    public function state() : array
    {
        return $this->state;
    }

    public function transitions() : array
    {
        return $this->transitions;
    }

    public function predictions() : array
    {
        if ($this->needsRecalculation) {
            $this->recalculate();
            $this->needsRecalculation = false;
        }

        return $this->predictions;
    }

    private function recalculate() : void
    {
        $this->predictions = [];

        $total = array_sum($this->transitions);
        foreach ($this->transitions as $transition => $count) {
            $this->predictions[$transition] = $count / $total;
        }
    }

    public function add(string $next) : void
    {
        if (! isset($this->transitions[$next])) {
            $this->transitions[$next] = 0;
        }
        $this->transitions[$next]++;
        $this->needsRecalculation = true;
    }
}
