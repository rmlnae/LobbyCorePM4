
<?php

namespace RynzDev\Core;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\lang\BaseLang;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\entity\Entity;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\utils\Textformat as Color;

class Main extends PluginBase implements Listener {
    
    public function onEnable(): void{
        $this->getServer()->getLogger()->info("[LobbyCorePM4] Plugin Enabled");
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }
    public function onJoin(PlayerJoinEvent $ev) {
        
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        
        $player = $ev->getPlayer();
        $name = $player->getName();
        $player->getInventory()->clearAll();
        $player->setHealth(20);
        $player->getHungerManager()->setFood(20);
        
        $player->getInventory()->setItem(0, VanillaItems::GRAY_DYE()->setCustomName("§7Hide Players"));
        $player->getInventory()->setItem(4, VanillaItems::COMPASS()->setCustomName($this->getConfig()->get("item-4-name")));
        $player->getInventory()->setItem(8, VanillaItems::INK_SAC()->setCustomName($this->getConfig()->get("item-8-name")));
    }
    public function onInteract(PlayerInteractEvent $ev){

        $player = $ev->getPlayer();
        $item = $ev->getItem();
        
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        
        if($item->getCustomName() == "§7Hide Players"){
            $player->getInventory()->setItem(0, VanillaItems::LIME_DYE()->setCustomName("§aShow Players"));
            $this->hideall[] = $player;
            $player->sendMessage("§aAll Players Invisible For You");
            
        }elseif($item->getCustomName() == "§aShow Players"){
            unset($this->hideall[array_search($player, $this->hideall)]);
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $player->showPlayer($p);
            }
            $player->getInventory()->setItem(0, VanillaItems::GRAY_DYE()->setCustomName("§7Hide Players"));
            $player->sendMessage("§aAll Players Visible For You");
            
        }elseif($item->getCustomName() == $this->getConfig()->get("item-4-name")){
            $this->getServer()->getCommandMap()->dispatch($player, ($this->getConfig()->get("item-4-cmd")));
            
        }elseif($item->getCustomName() == $this->getConfig()->get("item-8-name")){
            $this->getServer()->getCommandMap()->dispatch($player, ($this->getConfig()->get("item-8-cmd")));
        }
    }
}
