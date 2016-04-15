<?php
namespace DrdPlus\Tests\Person\Skills;

use Doctrineum\Tests\Entity\AbstractDoctrineEntitiesTest;
use DrdPlus\Person\Background\BackgroundParts\BackgroundSkillPoints;
use DrdPlus\Person\Background\BackgroundParts\Heritage;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Person\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Person\Skills\Combined\CombinedSkillRank;
use DrdPlus\Person\Skills\Combined\PersonCombinedSkill;
use DrdPlus\Person\Skills\Combined\PersonCombinedSkills;
use DrdPlus\Person\Skills\EnumTypes\PersonSkillsEnumsRegistrar;
use DrdPlus\Person\Skills\PersonSkills;
use DrdPlus\Person\Skills\Physical\PersonPhysicalSkill;
use DrdPlus\Person\Skills\Physical\PersonPhysicalSkills;
use DrdPlus\Person\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Person\Skills\Physical\PhysicalSkillRank;
use DrdPlus\Person\Skills\Psychical\PersonPsychicalSkill;
use DrdPlus\Person\Skills\Psychical\PersonPsychicalSkills;
use DrdPlus\Person\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Person\Skills\Psychical\PsychicalSkillRank;
use DrdPlus\Professions\Fighter;
use DrdPlus\Professions\Priest;
use DrdPlus\Professions\Ranger;
use DrdPlus\Professions\Theurgist;
use DrdPlus\Professions\Thief;
use DrdPlus\Professions\Wizard;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerObject;

class DoctrineEntitiesTest extends AbstractDoctrineEntitiesTest
{
    protected function setUp()
    {
        PersonSkillsEnumsRegistrar::registerAll();
        parent::setUp();
    }

    protected function getDirsWithEntities()
    {
        $personSkillsReflection = new \ReflectionClass(PersonSkills::class);
        $professionLevelReflection = new \ReflectionClass(ProfessionLevel::class);

        return [
            dirname($professionLevelReflection->getFileName()),
            dirname($personSkillsReflection->getFileName()),
        ];
    }

    protected function createEntitiesToPersist()
    {
        $tables = new Tables();

        return array_merge(
            [
                PersonSkills::createPersonSkills(
                    ProfessionLevels::createIt(
                        ProfessionFirstLevel::createFirstLevel($profession = Fighter::getIt()),
                        [ProfessionNextLevel::createNextLevel(
                            $profession,
                            LevelRank::getIt(2),
                            Strength::getIt(0),
                            Agility::getIt(1),
                            Knack::getIt(0),
                            Will::getIt(0),
                            Intelligence::getIt(1),
                            Charisma::getIt(0)
                        )]
                    ),
                    BackgroundSkillPoints::getIt(2, Heritage::getIt(7)),
                    $tables,
                    new PersonPhysicalSkills(),
                    new PersonPsychicalSkills(),
                    new PersonCombinedSkills()
                ),
                ProfessionFirstLevel::createFirstLevel(Theurgist::getIt()),
                ProfessionNextLevel::createNextLevel(
                    Priest::getIt(),
                    LevelRank::getIt(2),
                    Strength::getIt(0),
                    Agility::getIt(0),
                    Knack::getIt(0),
                    Will::getIt(1),
                    Intelligence::getIt(0),
                    Charisma::getIt(1)
                ),
                ProfessionLevels::createIt(
                    ProfessionFirstLevel::createFirstLevel($profession = Fighter::getIt()),
                    [
                        ProfessionNextLevel::createNextLevel(
                            $profession,
                            LevelRank::getIt(2),
                            Strength::getIt(0),
                            Agility::getIt(1),
                            Knack::getIt(0),
                            Will::getIt(0),
                            Intelligence::getIt(1),
                            Charisma::getIt(0)
                        ),
                        ProfessionNextLevel::createNextLevel(
                            $profession,
                            LevelRank::getIt(3),
                            Strength::getIt(1),
                            Agility::getIt(0),
                            Knack::getIt(0),
                            Will::getIt(0),
                            Intelligence::getIt(0),
                            Charisma::getIt(1)
                        )
                    ]
                )
            ],
            $this->createPhysicalSkillEntities($tables, ProfessionFirstLevel::createFirstLevel(Wizard::getIt())),
            $this->createPsychicalSkillEntities($tables, ProfessionFirstLevel::createFirstLevel(Thief::getIt())),
            $this->createCombinedSkillEntities($tables, ProfessionFirstLevel::createFirstLevel(Ranger::getIt()))
        );
    }

    private function createCombinedSkillEntities(Tables $tables, ProfessionFirstLevel $firstLevel)
    {
        $personCombinedSkillReflection = new \ReflectionClass(PersonCombinedSkill::class);
        $combinedSkillClasses = [];
        foreach (scandir(dirname($personCombinedSkillReflection->getFileName())) as $file) {
            if ($file === '..' || $file === '.') {
                continue;
            }
            $className = $personCombinedSkillReflection->getNamespaceName() . '\\' . basename($file, '.php');
            if (get_parent_class($className) !== PersonCombinedSkill::class) {
                continue;
            }
            $combinedSkillClasses[] = $className;
        }

        // TODO this entity should be unique for whole universe
        $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelBackgroundSkillPoints(
            $firstLevel,
            BackgroundSkillPoints::getIt(3, Heritage::getIt(5)),
            $tables
        );
        $requiredRankValue = new IntegerObject(1);
        // TODO this entity should be unique for whole universe
        $combinedSkillRank = new CombinedSkillRank($combinedSkillPoint, $requiredRankValue);

        $personCombinedSkillList = array_map(
            function ($combinedSkillClass) use ($combinedSkillRank) {
                return new $combinedSkillClass($combinedSkillRank);
            },
            $combinedSkillClasses
        );

        $personCombinedSkills = new PersonCombinedSkills();
        foreach ($personCombinedSkillList as $personCombinedSkill) {
            $personCombinedSkills->addCombinedSkill($personCombinedSkill);
        }
        $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelBackgroundSkillPoints(
            $firstLevel,
            BackgroundSkillPoints::getIt(3, Heritage::getIt(5)),
            $tables
        );
        $combinedSkillRank = new CombinedSkillRank(
            CombinedSkillPoint::createFromFirstLevelBackgroundSkillPoints(
                $firstLevel,
                BackgroundSkillPoints::getIt(4, Heritage::getIt(5)),
                $tables
            ),
            new IntegerObject(1)
        );

        return array_merge(
            $personCombinedSkillList,
            [
                $personCombinedSkills,
                $combinedSkillPoint,
                $combinedSkillRank
            ]
        );
    }

    private function createPhysicalSkillEntities(Tables $tables, ProfessionFirstLevel $firstLevel)
    {
        $personPhysicalSkillReflection = new \ReflectionClass(PersonPhysicalSkill::class);
        $physicalSkillClasses = [];
        foreach (scandir(dirname($personPhysicalSkillReflection->getFileName())) as $file) {
            if ($file === '..' || $file === '.') {
                continue;
            }
            $className = $personPhysicalSkillReflection->getNamespaceName() . '\\' . basename($file, '.php');
            if (get_parent_class($className) !== PersonPhysicalSkill::class) {
                continue;
            }
            $physicalSkillClasses[] = $className;
        }

        // TODO this entity should be unique for whole universe
        $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelBackgroundSkillPoints(
            $firstLevel,
            BackgroundSkillPoints::getIt(3, Heritage::getIt(5)),
            $tables
        );
        $requiredRankValue = new IntegerObject(1);
        // TODO this entity should be unique for whole universe
        $physicalSkillRank = new PhysicalSkillRank($physicalSkillPoint, $requiredRankValue);

        $personPhysicalSkillList = array_map(
            function ($physicalSkillClass) use ($physicalSkillRank) {
                return new $physicalSkillClass($physicalSkillRank);
            },
            $physicalSkillClasses
        );

        $personPhysicalSkills = new PersonPhysicalSkills();
        foreach ($personPhysicalSkillList as $personPhysicalSkill) {
            $personPhysicalSkills->addPhysicalSkill($personPhysicalSkill);
        }
        $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelBackgroundSkillPoints(
            $firstLevel,
            BackgroundSkillPoints::getIt(3, Heritage::getIt(5)),
            $tables
        );
        $physicalSkillRank = new PhysicalSkillRank(
            PhysicalSkillPoint::createFromFirstLevelBackgroundSkillPoints(
                $firstLevel,
                BackgroundSkillPoints::getIt(4, Heritage::getIt(5)),
                $tables
            ),
            new IntegerObject(1)
        );

        return array_merge(
            $personPhysicalSkillList,
            [
                $personPhysicalSkills,
                $physicalSkillPoint,
                $physicalSkillRank
            ]
        );
    }

    private function createPsychicalSkillEntities(Tables $tables, ProfessionFirstLevel $firstLevel)
    {
        $personPsychicalSkillReflection = new \ReflectionClass(PersonPsychicalSkill::class);
        $psychicalSkillClasses = [];
        foreach (scandir(dirname($personPsychicalSkillReflection->getFileName())) as $file) {
            if ($file === '..' || $file === '.') {
                continue;
            }
            $className = $personPsychicalSkillReflection->getNamespaceName() . '\\' . basename($file, '.php');
            if (get_parent_class($className) !== PersonPsychicalSkill::class) {
                continue;
            }
            $psychicalSkillClasses[] = $className;
        }

        // TODO this entity should be unique for whole universe
        $psychicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelBackgroundSkillPoints(
            $firstLevel,
            BackgroundSkillPoints::getIt(3, Heritage::getIt(5)),
            $tables
        );
        $requiredRankValue = new IntegerObject(1);
        // TODO this entity should be unique for whole universe
        $psychicalSkillRank = new PsychicalSkillRank($psychicalSkillPoint, $requiredRankValue);

        $personPsychicalSkillList = array_map(
            function ($psychicalSkillClass) use ($psychicalSkillRank) {
                return new $psychicalSkillClass($psychicalSkillRank);
            },
            $psychicalSkillClasses
        );

        $personPsychicalSkills = new PersonPsychicalSkills();
        foreach ($personPsychicalSkillList as $personPsychicalSkill) {
            $personPsychicalSkills->addPsychicalSkill($personPsychicalSkill);
        }

        $psychicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelBackgroundSkillPoints(
            $firstLevel,
            BackgroundSkillPoints::getIt(4, Heritage::getIt(3)),
            $tables
        );

        $psychicalSkillRank = new PsychicalSkillRank(
            PsychicalSkillPoint::createFromFirstLevelBackgroundSkillPoints(
                $firstLevel,
                BackgroundSkillPoints::getIt(4, Heritage::getIt(5)),
                $tables
            ),
            new IntegerObject(1)
        );

        return array_merge(
            $personPsychicalSkillList,
            [
                $personPsychicalSkills,
                $psychicalSkillPoint,
                $psychicalSkillRank
            ]
        );
    }

}