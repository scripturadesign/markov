<?php

declare(strict_types=1);

namespace Scriptura\Markov;

class Chain
{
    public function __construct(
        private int $order,
        /** @var Link[] */
        private array $history = [],
    ) {
    }

    public function order(): int
    {
        return $this->order;
    }

    /**
     * @return Link[]
     */
    public function history(): array
    {
        return $this->history;
    }

    /**
     * @param string[] $tokens
     */
    public function learn(array $tokens): void
    {
        $tokens[] = '';
        $count = count($tokens);
        $state = array_fill(0, $this->order, '');

        for ($i = 0; $i < $count; ++$i) {
            $transition = $tokens[$i];

            $this->learnPart($state, $transition);

            // Remove head from state
            array_shift($state);
            // Push next to tail of state
            array_push($state, $transition);
        }
    }

    public function learnPart(array $state, string $transition): void
    {
        $link = $this->find($state);

        if ($link->isNull()) {
            $link = new Link($state);
            $this->history[] = $link;
        }

        $link->add($transition);
    }

    /**
     * @param string[] $state
     * @return \Scriptura\Markov\Link
     */
    public function find(array $state): Link
    {
        foreach ($this->history as $link) {
            if ($link->state() === $state) {
                return $link;
            }
        }

        return Link::null();
    }
}
