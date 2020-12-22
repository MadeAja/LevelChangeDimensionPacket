<?php

declare(strict_types=1);

namespace Dapro718\LevelChangeDimensionScreen;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\{ChangeDimensionPacket, PlayStatusPacket};
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\math\Vector3;

final class LCDSMain extends PluginBase implements Listener{

    private $levels = [];
    
    public function onEnable(): void{
        foreach($this->getConfig()->get("levels") as $name=>$id){
            $this->levels[strtolower($name)] = $id["dimension"];
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onLevelChange(EntityTeleportEvent $event): void{
        $player = $event->getEntity();
        if($player instanceof Player){
            if($event->getTo()->getLevel()->getFolderName() !== $player->getLevel()->getFolderName()){
                if(isset($this->levels[strtolower($event->getTo()->getLevel()->getFolderName())])){
                    if(!$player->hasPermission("levelchangedimensionscreen.noscreen")){
                        if($event->getFrom()->getLevel() === $event->getTo()->getLevel()) return; // If same world no need the loading screen
						$pk = new ChangeDimensionPacket();
						$pk->dimension = 1; // Nether first
						$pk->position = new Vector3($event->getTo()->getX(), $event->getTo()->getY(), $event->getTo()->getZ());
						$pk->respawn = false;
						$player->sendDataPacket($pk);
						$pk = new ChangeDimensionPacket();
						$pk->dimension = 0; // Next Overworld
						$pk->position = new Vector3($event->getTo()->getX(), $event->getTo()->getY(), $event->getTo()->getZ());
						$pk->respawn = false;
						$player->sendDataPacket($pk);
						$pk = new PlayStatusPacket();
						$pk->status = PlayStatusPacket::PLAYER_SPAWN; // No more loading screen stuck
						$player->sendDataPacket($pk);
                    }
                }
            }
        }
    }
}
