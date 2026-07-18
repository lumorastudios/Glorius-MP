<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari UpdateBlockPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Packet ini clientbound-only (server -> client), jadi cuma butuh encode().
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyBlockPosition;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

final class UpdateBlockPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(UpdateBlockPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		LegacyBlockPosition::write($out, $packet->blockPosition);
		VarInt::writeUnsignedInt($out, $packet->blockRuntimeId);
		VarInt::writeUnsignedInt($out, $packet->flags);
		VarInt::writeUnsignedInt($out, $packet->dataLayerId);

		return $out->getData();
	}
}
