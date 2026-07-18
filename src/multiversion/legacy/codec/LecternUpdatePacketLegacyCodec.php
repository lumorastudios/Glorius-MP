<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari LecternUpdatePacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Serverbound-only.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pocketmine\multiversion\legacy\LegacyBlockPosition;
use pocketmine\network\mcpe\protocol\LecternUpdatePacket;

final class LecternUpdatePacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function decodePayload(ByteBufferReader $in) : LecternUpdatePacket{
		$page = Byte::readUnsigned($in);
		$totalPages = Byte::readUnsigned($in);
		$blockPosition = LegacyBlockPosition::read($in);
		return LecternUpdatePacket::create($page, $totalPages, $blockPosition);
	}
}
