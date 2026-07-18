<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari NetworkInventoryAction::read()/write() versi bedrock-protocol
 * 55.0.0 (1.26.0) - sebelum windowId/sourceFlags jadi nullable+optional-wrapped
 * dan sebelum dipecah jadi readAuthInput/readTransaction di versi 1.26.30.
 */

namespace pocketmine\multiversion\legacy;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;

final class LegacyNetworkInventoryAction{

	private function __construct(){
		//NOOP
	}

	public static function read(ByteBufferReader $in) : NetworkInventoryAction{
		$action = new NetworkInventoryAction();
		$action->sourceType = VarInt::readUnsignedInt($in);

		switch($action->sourceType){
			case NetworkInventoryAction::SOURCE_CONTAINER:
				$action->windowId = VarInt::readSignedInt($in);
				break;
			case NetworkInventoryAction::SOURCE_WORLD:
				$action->sourceFlags = VarInt::readUnsignedInt($in);
				break;
			case NetworkInventoryAction::SOURCE_CREATIVE:
				break;
			case NetworkInventoryAction::SOURCE_TODO:
				$action->windowId = VarInt::readSignedInt($in);
				break;
			default:
				throw new PacketDecodeException("Unknown inventory action source type $action->sourceType");
		}

		$action->inventorySlot = VarInt::readUnsignedInt($in);
		$action->oldItem = LegacyItemStackWrapper::read($in);
		$action->newItem = LegacyItemStackWrapper::read($in);

		return $action;
	}

	public static function write(ByteBufferWriter $out, NetworkInventoryAction $action) : void{
		VarInt::writeUnsignedInt($out, $action->sourceType);

		switch($action->sourceType){
			case NetworkInventoryAction::SOURCE_CONTAINER:
				VarInt::writeSignedInt($out, $action->windowId ?? 0);
				break;
			case NetworkInventoryAction::SOURCE_WORLD:
				VarInt::writeUnsignedInt($out, $action->sourceFlags ?? 0);
				break;
			case NetworkInventoryAction::SOURCE_CREATIVE:
				break;
			case NetworkInventoryAction::SOURCE_TODO:
				VarInt::writeSignedInt($out, $action->windowId ?? 0);
				break;
			default:
				throw new \InvalidArgumentException("Unknown inventory action source type $action->sourceType");
		}

		VarInt::writeUnsignedInt($out, $action->inventorySlot);
		LegacyItemStackWrapper::write($out, $action->oldItem);
		LegacyItemStackWrapper::write($out, $action->newItem);
	}
}
