<?php

declare(strict_types=1);

namespace Dapro718\LevelChangeDimensionScreen;

use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\network\mcpe\protocol\{ChangeDimensionPacket, PlayStatusPacket, PlayerFogPacket, LevelChunkPacket};

final class ClearScreenTask extends Task{

    private $player;
    private $position;
    
    public function __construct(Player $player, Position $position){
        $this->player = $player;
        $this->position = $position;
    }
    
    public function onRun(int $tick): void{
        $level = $this->position->getLevel();
        $x = intval($this->position->getX());
        $z = intval($this->position->getY());
        for($i=-2;$i<=2;$i++){
            for($j=-2;$j<=2;$j++){
                $xx = $x + (16 * $i);
                $zz = $z + (16 * $j);
                $level->loadChunk($xx, $zz);
                $chunk = $level->getChunk($xx, $zz);
                $pk = LevelChunkPacket::withoutCache($xx >> 4, $zz >> 4, count($chunk->getSubChunks()), $chunk->networkSerialize());
                $this->player->sendDataPacket($pk);
            }
        }
        /*$pk = new ChangeDimensionPacket();
        $pk->dimension = 0;
        $pk->position = new Vector3($this->position->getX(), $this->position->getY(), $this->position->getZ());
        $pk->respawn = false;
        $this->player->sendDataPacket($pk);
        $pk = new PlayStatusPacket();
        $pk->status = PlayStatusPacket::PLAYER_SPAWN;
        $this->player->sendDataPacket($pk);*/
        $this->player->sendDataPacket(PlayerFogPacket::create(array()));
    }
}
