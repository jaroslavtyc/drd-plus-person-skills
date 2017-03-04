<?php
namespace DrdPlus\Skills\Combined\RollsOn;

use Drd\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Combined\Showmanship;

/**
 * @method Roll2d6DrdPlus getRoll()
 */
class ShowmanshipGameQuality extends RollOnQuality
{
    /**
     * @param Charisma $charisma
     * @param Showmanship $showmanship
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     */
    public function __construct(Charisma $charisma, Showmanship $showmanship, Roll2d6DrdPlus $roll2D6DrdPlus)
    {
        parent::__construct($charisma->getValue() + $showmanship->getBonus(), $roll2D6DrdPlus);
    }

}