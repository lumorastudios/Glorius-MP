<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari MobEquipmentPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Bidirectional.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pocketmine\multiversion\legacy\LegacyItemStackWrapper;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;

final class MobEquipmentPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(MobEquipmentPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		CommonTypes::putActorRuntimeId($out, $packet->actorRuntimeId);
		LegacyItemStackWrapper::write($out, $packet->item);
		Byte::writeUnsigned($out, $packet->inventorySlot);
		Byte::writeUnsigned($out, $packet->hotbarSlot);
		Byte::writeUnsigned($out, $packet->windowId);

		return $out->getData();
	}

	public static function decodePayload(ByteBufferReader $in) : MobEquipmentPacket{
		$packet = new MobEquipmentPacket();
		$packet->actorRuntimeId = CommonTypes::getActorRuntimeId($in);
		$packet->item = LegacyItemStackWrapper::read($in);
		$packet->inventorySlot = Byte::readUnsigned($in);
		$packet->hotbarSlot = Byte::readUnsigned($in);
		$packet->windowId = Byte::readUnsigned($in);
		return $packet;
	}
}
