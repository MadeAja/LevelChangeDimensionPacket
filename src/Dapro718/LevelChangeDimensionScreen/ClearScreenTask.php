<?php

declare(strict_types=1);

namespace Dapro718\LevelChangeDimensionScreen;

use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\network\mcpe\protocol\{ChangeDimensionPacket, PlayStatusPacket, PlayerFogPacket};

class ClearScreenTask extends Task{

    private $player;
    
    public function __construct(Player $player, Position $pos){
        $this->player = $player;
    }
    
    public function onRun(int $tick): void{
        $pk = new ChangeDimensionPacket();
        $pk->dimension = 0;
        $pk->position = new Vector3($event->getTo()->getX(), $event->getTo()->getY(), $event->getTo()->getZ());
        $pk->respawn = false;
        $this->player->sendDataPacket($pk);
        $pk = new PlayStatusPacket();
        $pk->status = PlayStatusPacket::PLAYER_SPAWN;
        $this->player->sendDataPacket($pk);
        $pk = new PlayerFogPacket();
        $pk->layers = [];
        $this->player->sendDataPacket($pk);
    }
}
