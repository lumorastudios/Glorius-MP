<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari InventoryContentPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Clientbound-only.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyItemStackWrapper;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use function count;

final class InventoryContentPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(InventoryContentPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		VarInt::writeUnsignedInt($out, $packet->windowId);
		VarInt::writeUnsignedInt($out, count($packet->items));
		foreach($packet->items as $item){
			LegacyItemStackWrapper::write($out, $item);
		}
		$packet->containerName->write($out);
		LegacyItemStackWrapper::write($out, $packet->storage);

		return $out->getData();
	}
}
