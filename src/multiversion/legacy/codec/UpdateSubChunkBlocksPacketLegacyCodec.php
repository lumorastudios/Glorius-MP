<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari UpdateSubChunkBlocksPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Clientbound-only.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyBlockPosition;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\multiversion\legacy\LegacyUpdateSubChunkBlocksPacketEntry;
use pocketmine\network\mcpe\protocol\UpdateSubChunkBlocksPacket;
use function count;

final class UpdateSubChunkBlocksPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(UpdateSubChunkBlocksPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		LegacyBlockPosition::write($out, $packet->getBaseBlockPosition());

		VarInt::writeUnsignedInt($out, count($packet->getLayer0Updates()));
		foreach($packet->getLayer0Updates() as $update){
			LegacyUpdateSubChunkBlocksPacketEntry::write($out, $update);
		}

		VarInt::writeUnsignedInt($out, count($packet->getLayer1Updates()));
		foreach($packet->getLayer1Updates() as $update){
			LegacyUpdateSubChunkBlocksPacketEntry::write($out, $update);
		}

		return $out->getData();
	}
}
