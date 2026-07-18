<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari PlayerEnchantOptionsPacket versi bedrock-protocol 55.0.0
 * (1.26.0). Clientbound-only, dikirim saat player membuka enchant table.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyEnchantOption;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\PlayerEnchantOptionsPacket;
use function count;

final class PlayerEnchantOptionsPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(PlayerEnchantOptionsPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		VarInt::writeUnsignedInt($out, count($packet->getOptions()));
		foreach($packet->getOptions() as $option){
			LegacyEnchantOption::write($out, $option);
		}

		return $out->getData();
	}
}
