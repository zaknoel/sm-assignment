<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class NoopCalculator extends AbstractCalculator
{
    protected const UNITS = 'posts';
    /**
     * @var array
     */
    private array $users = [];

    /**
     * @var int
     */
    private int $postsCount = 0;
    /**
     * @var float
     */
    private float $float_result=0.0;
    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $this->postsCount++;
        $this->users[$postTo->getAuthorId()]=1;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $usersCount=count($this->users);
        $this->float_result = $this->postsCount > 0 && $usersCount>0
            ? $this->postsCount / $usersCount
            : 0.0;

        return (new StatisticsTo())->setValue(round($this->float_result ,0));
    }
    public function getFloatResult(): float
    {
        return round($this->float_result, 2);
    }
}
