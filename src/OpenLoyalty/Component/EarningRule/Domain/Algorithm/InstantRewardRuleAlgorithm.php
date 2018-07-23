<?php
/**
 * Copyright © 2018 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Component\EarningRule\Domain\Algorithm;

use Broadway\CommandHandling\CommandBus;
use OpenLoyalty\Component\EarningRule\Domain\Command\ActivateInstantRewardRule;
use OpenLoyalty\Component\EarningRule\Domain\EarningRule;
use OpenLoyalty\Component\EarningRule\Domain\InstantRewardRule;
use OpenLoyalty\Component\EarningRule\Domain\Strategy\EarningRuleStrategy;

/**
 * Class InstantRewardRuleAlgorithm.
 */
class InstantRewardRuleAlgorithm extends AbstractRuleAlgorithm
{
    /**
     * @var EarningRuleStrategy
     */
    private $instantRewardStrategy;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * InstantRewardRuleAlgorithm constructor.
     *
     * @param EarningRuleStrategy $instantRewardStrategy
     * @param CommandBus          $commandBus
     */
    public function __construct(EarningRuleStrategy $instantRewardStrategy, CommandBus $commandBus)
    {
        parent::__construct(self::MEDIUM_PRIORITY);
        $this->instantRewardStrategy = $instantRewardStrategy;
        $this->commandBus = $commandBus;
    }

    /**
     * @param RuleEvaluationContextInterface $context
     * @param EarningRule                    $rule
     */
    public function evaluate(RuleEvaluationContextInterface $context, EarningRule $rule): void
    {
        if (!$rule instanceof InstantRewardRule
            || !$this->instantRewardStrategy->isApplicable($context, $rule)) {
            return;
        }
        $this->commandBus->dispatch(
            new ActivateInstantRewardRule(
                $rule->getEarningRuleId(),
                $context->getCustomerId(),
                $context->getTransaction()->getGrossValue(),
                $rule->getRewardCampaignId()
            )
        );
    }
}
