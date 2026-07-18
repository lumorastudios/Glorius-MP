<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari BlockEventPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Clientbound-only.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyBlockPosition;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\BlockEventPacket;

final class BlockEventPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(BlockEventPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		LegacyBlockPosition::write($out, $packet->blockPosition);
		VarInt::writeSignedInt($out, $packet->eventType);
		VarInt::writeSignedInt($out, $packet->eventData);

		return $out->getData();
	}
}
