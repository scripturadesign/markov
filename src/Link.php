<?php
declare(strict_types=1);

namespace Scriptura\Markov;

class Link
{
    private bool $needsRecalculation = true;
    private array $state;
    private array $transitions;
    /** @var array{string, float}|array<empty, empty> */
    private array $predictions = [];

    /**
     * @param array{int, string}|array<empty, empty> $state
     * @param array{string, int}|array<empty, empty> $transitions
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

    public function next() : string
    {
        $transitions = array_map(fn ($t) : int => $t * 100, $this->transitions);

        try {
            $rand = random_int(0, array_sum($transitions));
        } catch (\Exception $e) {
            return '';
        }

        foreach ($transitions as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }

        return '';
    }
}
