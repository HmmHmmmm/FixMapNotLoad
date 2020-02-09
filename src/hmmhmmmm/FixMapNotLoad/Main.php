<?php

namespace hmmhmmmm\FixMapNotLoad;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\generator\VoidGenerator;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
   private static $instance = null;
   private $world = "void2";
   private $cmd = "nlm";
   public $array = [];
   
   public static function getInstance(){
      return self::$instance;
   }
   public function onLoad(){
      self::$instance = $this;
   } 
   public function onEnable(){
      if(!file_exists($this->getServer()->getDataPath()."worlds/".$this->world)){
         $this->getServer()->generateLevel($this->world, null, VoidGenerator::class);
      }
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
   }
   public function FixMapNotLoad(Player $player): void{
      $this->array[$player->getName()]["v3"] = new Vector3($player->getX(), $player->getY(), $player->getZ());
      $this->array[$player->getName()]["world"] = $player->getLevel()->getFolderName();
      $this->getServer()->loadLevel($this->world);
      $void2 = $this->getServer()->getLevelByName($this->world);
      $void2->loadChunk($void2->getSafeSpawn()->getFloorX(), $void2->getSafeSpawn()->getFloorZ());
      $this->getServer()->loadLevel($this->array[$player->getName()]["world"]);
      $FixMapNotLoad = $this->getServer()->getLevelByName($this->array[$player->getName()]["world"]);
      $FixMapNotLoad->loadChunk($FixMapNotLoad->getSafeSpawn()->getFloorX(), $FixMapNotLoad->getSafeSpawn()->getFloorZ());
      $player->teleport($void2->getSafeSpawn());
      $x = $this->array[$player->getName()]["v3"]->getX();
      $y = $this->array[$player->getName()]["v3"]->getY();
      $z = $this->array[$player->getName()]["v3"]->getZ();
      $player->teleport(new Position($x, $y + 2, $z, $FixMapNotLoad));
      unset($this->array[$player->getName()]);
   }
   public function onEntityTeleport(EntityTeleportEvent $event){
      $entity = $event->getEntity();
      if($entity instanceof Player){ 
         $entity->sendMessage("§eหากแมพไม่โหลด? ให้คุณพิมพ์แชท ".$this->cmd);
      }
   }
   public function onPlayerChat(PlayerChatEvent $event){
      $player = $event->getPlayer();
      $message = $event->getMessage();
      if($message == $this->cmd){
         $event->setCancelled(true);
         $this->FixMapNotLoad($player);
         $player->sendMessage("§fคุณได้พิมพ์แก้บัคแมพไม่โหลด..");
      }
   }
}