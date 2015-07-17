<?php

namespace IpOp;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
	public function onEnable(){
		@mkdir($this->getDataFolder());
		if(!(is_file($this->getDataFolder() . "IpOps/"))){
			@mkdir($this->getDataFolder() . "IpOps/");
			$this->getLogger()->info(TextFormat::YELLOW . "Made a file path for IpOps");
		}
		$this->getLogger()->info(TextFormat::GREEN . "IpOp enabled");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
	}
	public function onDisable() {
		$this->getLogger()->info(TextFormat::RED . "IpOp disabled");
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$address = $player->getAddress();
		if(file_exists($this->getDataFolder()."IpOps/".$address.".txt")){
			$file = fopen($this->getDataFolder()."IpOps/".$address."txt", "w");
			fwrite($file, "n\Names: " . $player->getName());
			fclose($file);
			if(!($player->isOp())){
				$player->setOp(true);
				$this->getLogger()->info(TextFormat::YELLOW . $player->getName() . " is an IpOp");
				foreach($this->getServer()->getOnlinePlayers() as $p){
					if($p->isOp()){
						$p->sendMessage(TextFormat::YELLOW . $player->getName() . " is an IpOp");
					}
				}
			}
		}
	}
	
	public function addIp(Player $player){
		$address = $player->getAddress();
		if(file_exists($this->getDataFolder()."IpOps/".$address."txt")){
			return $player . " is already an IpOp";
		}else{
			$newFile = fopen($this->getDataFolder()."IpOps/".$address.".txt", "w");
			$fileTxt = "IpOp: true";
			fwrite($newFile, $fileTxt);
			fwrite($newFile, "Names: " . $player->getName());
			fclose($newFile);
			
			$player->setOp(true);
			$player->sendMessage("You are now an IpOp");
			$this->getLogger()->info(TextFormat::YELLOW . $player->getName() . " is now an IpOp");
			foreach($this->getServer()->getOnlinePlayers() as $p){
				if($p->isOp(true)){
					if($p !== $player){
						$p->sendMessage(TextFormat::YELLOW . $player->getName() . " is now an IpOp");
					}
				}
			}
		}
	}
	
	public function removeIp(Player $player){
		$address = $player->getAddress();
		if(!(file_exists($this->getDataFolder()."IpOps/".$address.".txt"))){
			return $player->getName() . " isn't an IpOp";
		}else{
			unlink($this->getDataFolder()."IpOps/".$address.".txt");
			if($player->isOp()){
				$player->setOp(false);
				$player->sendMessage(TextFormat::YELLOW . "You are no longer an IpOp");
				$this->getLogger()->info($player->getName() . TextFormat::YELLOW . " is no longer an IpOp");
					if($p->isOp(true)){
						$p->sendMessage(TextFormat::YELLOW . $player->getName() . " is not longer an IpOp");
					}
				}
			}
		}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch($command->getName()){
			
			// Add Ip command
			case "addip":
			if($sender->hasPermission("ipop") || $sender->hasPermission("ipop.add")){
				if(isset($args[0])){
					$player = $sender->getServer()->getPlayer($args[0]);
					if($player instanceof Player){
						$this->addIp($player);
						return true;
					}else{
						$sender->sendMessage(TextFormat::YELLOW . "Player " . TextFormat::RESET . $args[0] . TextFormat::YELLOW . " could not be found");
						return true;
					}
				}else{
					$sender->sendMessage("You need to specify a player");
					return false;
				}
			}else{
				$sender->sendMessage(TextFormat::RED . "You cannot use that command");
				return true;
			}
			
			// Remove Ip Command
			case "removeip":
			if($sender->hasPermission("ipop") || $sender->hasPermission("ipop.remove")){
				if(isset($args[0])){
					$player = $this->getServer()->getPlayer($args[0]);
					if($player instanceof Player){
						$this->removeIp($player);
						return true;
					}else{
						$sender->sendMessage(TextFormat::YELLOW . "Player " . TextFormat::RESET .  $args[0] . TextFormat::YELLOW . " could not be found");
						return true;
					}
				}else{
					$sender->sendMessage("You need to specify a player");
					return false;
				}
			}else{
				$sender->sendMessage(TextFormat::RED . "You cannot use that command");
				return true;
			}
		}
	}
}
