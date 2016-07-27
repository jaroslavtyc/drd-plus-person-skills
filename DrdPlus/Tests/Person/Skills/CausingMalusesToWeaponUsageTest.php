<?php
namespace DrdPlus\Tests\Person\Skills\Combined;

use DrdPlus\Person\Skills\CausingMalusesToWeaponUsage;
use DrdPlus\Tables\Armaments\Weapons\MissingWeaponSkillsTable;
use Granam\Tests\Tools\TestWithMockery;

abstract class CausingMalusesToWeaponUsageTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_malus_to_fight_number()
    {
        $sutClasses = $this->getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage $sut */
            $sut = new $sutClass();
            self::assertSame(
                'bar',
                $sut->getMalusToFightNumber($this->createMissingWeaponSkillsTable('foo', 'bar'))
            );
        }
    }

    /**
     * @return string[]
     */
    protected function getSutClasses()
    {
        $reflection = new \ReflectionClass($this->getSutClass());
        $sutClasses = [];
        foreach (new \DirectoryIterator(dirname($reflection->getFileName())) as $file) {
            if ($file->isFile()) {
                $baseName = $file->getBasename('.php');
                $foundClass = $reflection->getNamespaceName() . '\\' . $baseName;
                if (is_subclass_of($foundClass, $this->getSutClass(), true)) {
                    $sutClasses[] = $foundClass;
                }
            }
        }

        return $sutClasses;
    }

    /**
     * @return string
     */
    protected function getSutClass()
    {
        return preg_replace('~[\\\]Tests([\\\].+)Test$~', '$1', static::class);
    }

    /**
     * @param $expectedSkillRank
     * @param $result
     * @return \Mockery\MockInterface|MissingWeaponSkillsTable
     */
    protected function createMissingWeaponSkillsTable($expectedSkillRank, $result)
    {
        $missingWeaponSkillsTable = $this->mockery(MissingWeaponSkillsTable::class);
        $missingWeaponSkillsTable->shouldReceive('getFightNumberForWeaponSkill')
            ->with($expectedSkillRank)
            ->andReturn($result);
        $missingWeaponSkillsTable->shouldReceive('getAttackNumberForWeaponSkill')
            ->with($expectedSkillRank)
            ->andReturn($result);
        $missingWeaponSkillsTable->shouldReceive('getCoverForWeaponSkill')
            ->with($expectedSkillRank)
            ->andReturn($result);
        $missingWeaponSkillsTable->shouldReceive('getBaseOfWoundsForWeaponSkill')
            ->with($expectedSkillRank)
            ->andReturn($result);

        return $missingWeaponSkillsTable;
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_attack_number()
    {
        $sutClasses = $this->getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage $sut */
            $sut = new $sutClass();
            self::assertSame(
                'bar',
                $sut->getMalusToAttackNumber($this->createMissingWeaponSkillsTable('foo', 'bar'))
            );
        }
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_cover()
    {
        $sutClasses = $this->getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage $sut */
            $sut = new $sutClass();
            self::assertSame(
                'bar',
                $sut->getMalusToCover($this->createMissingWeaponSkillsTable('foo', 'bar'))
            );
        }
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_base_of_wounds()
    {
        $sutClasses = $this->getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage $sut */
            $sut = new $sutClass();
            self::assertSame(
                'bar',
                $sut->getMalusToBaseOfWounds($this->createMissingWeaponSkillsTable('foo', 'bar'))
            );
        }
    }
}