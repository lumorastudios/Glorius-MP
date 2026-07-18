<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari UpdateSubChunkBlocksPacketEntry::write() versi 1.26.0.
 */

namespace pocketmine\multiversion\legacy;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\types\UpdateSubChunkBlocksPacketEntry;

final class LegacyUpdateSubChunkBlocksPacketEntry{

	private function __construct(){
		//NOOP
	}

	public static function write(ByteBufferWriter $out, UpdateSubChunkBlocksPacketEntry $entry) : void{
		LegacyBlockPosition::write($out, $entry->getBlockPosition());
		VarInt::writeUnsignedInt($out, $entry->getBlockRuntimeId());
		VarInt::writeUnsignedInt($out, $entry->getFlags());
		VarInt::writeUnsignedLong($out, $entry->getSyncedUpdateActorUniqueId());
		VarInt::writeUnsignedInt($out, $entry->getSyncedUpdateType());
	}
}
