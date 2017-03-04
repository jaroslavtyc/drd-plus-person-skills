<?php
namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Tests\Skills\WithBonusToSensesTrait;

class TechnologyTest extends WithBonusToIntelligenceFromPsychicalTest
{
    use WithBonusToSensesTrait;

    /**
     * @param int $currentSkillRankValue
     * @return int
     */
    protected function getExpectedBonusToSenses(int $currentSkillRankValue): int
    {
        return $currentSkillRankValue;
    }

}