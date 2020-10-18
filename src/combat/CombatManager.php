<?php

declare(strict_types = 1);

namespace combat;

use core\combat\boss\Boss;
use core\combat\boss\BossException;
use core\combat\boss\types\Alien;
use core\combat\boss\types\CorruptedKing;
use core\combat\boss\types\Witcher;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class CombatManager {

    /** @var CombatListener */
    private $listener;

    /** @var string[] */
    private $bosses = [];

    /** @var Boss[] */
    private $spawned = [];


    /**
     * @throws BossException
     */
    public function init(): void {
        $this->addBoss(CorruptedKing::class);
        $this->addBoss(Alien::class);
        $this->addBoss(Witcher::class);
    }

    /**
     * @param string $bossClass
     *
     * @throws BossException
     */
    public function addBoss(string $bossClass) {
        Entity::registerEntity($bossClass);
        if(isset($this->bosses[constant("$bossClass::BOSS_ID")])) {
            throw new BossException("Unable to register boss due to duplicated boss identifier");
        }
        $this->bosses[constant("$bossClass::BOSS_ID")] = $bossClass;
    }

    /**
     * @param int $identifier
     *
     * @return null|string
     */
    public function getBossNameByIdentifier(int $identifier): ?string {
        return $this->bosses[$identifier] ?? null;
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    public function getIdentifierByName(string $name): ?int {
        return array_search($name, $this->bosses) ?? null;
    }

    /**
     * @param int $bossId
     * @param Level $level
     * @param CompoundTag $tag
     */
    public function createBoss(int $bossId, Level $level, CompoundTag $tag) {
        $class = $this->getBossNameByIdentifier($bossId);
        /** @var Boss $entity */
        $entity = new $class($level, $tag);
        $entity->spawnToAll();
        $this->spawned{$entity->getId()} = $entity;
    }
}
