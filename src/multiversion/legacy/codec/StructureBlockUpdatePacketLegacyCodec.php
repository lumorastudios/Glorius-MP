<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari StructureBlockUpdatePacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Serverbound-only. StructureEditorData sudah dikonfirmasi tidak berubah
 * antara 1.26.0 dan 1.26.30, jadi tetap pakai CommonTypes bawaan vendor.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferReader;
use pocketmine\multiversion\legacy\LegacyBlockPosition;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\StructureBlockUpdatePacket;

final class StructureBlockUpdatePacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function decodePayload(ByteBufferReader $in) : StructureBlockUpdatePacket{
		$blockPosition = LegacyBlockPosition::read($in);
		$structureEditorData = CommonTypes::getStructureEditorData($in);
		$isPowered = CommonTypes::getBool($in);
		$waterlogged = CommonTypes::getBool($in);
		return StructureBlockUpdatePacket::create($blockPosition, $structureEditorData, $isPowered, $waterlogged);
	}
}
