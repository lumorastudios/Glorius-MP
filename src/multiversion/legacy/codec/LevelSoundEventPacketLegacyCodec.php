<?php

declare(strict_types=1);

/*
 * Bidirectional. `sound` was an int (see LegacySoundMap), now a string;
 * `firePosition` doesn't exist in this version.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\multiversion\legacy\LegacySoundMap;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;

final class LevelSoundEventPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(LevelSoundEventPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		VarInt::writeUnsignedInt($out, LegacySoundMap::newStringToOldId($packet->sound));
		CommonTypes::putVector3($out, $packet->position);
		VarInt::writeSignedInt($out, $packet->extraData);
		CommonTypes::putString($out, $packet->entityType);
		CommonTypes::putBool($out, $packet->isBabyMob);
		CommonTypes::putBool($out, $packet->disableRelativeVolume);
		LE::writeSignedLong($out, $packet->actorUniqueId);
		//firePosition is intentionally not written (doesn't exist in 1.26.0-1.26.20)

		return $out->getData();
	}

	public static function decodePayload(ByteBufferReader $in) : LevelSoundEventPacket{
		$packet = new LevelSoundEventPacket();
		$packet->sound = LegacySoundMap::oldIdToNewString(VarInt::readUnsignedInt($in));
		$packet->position = CommonTypes::getVector3($in);
		$packet->extraData = VarInt::readSignedInt($in);
		$packet->entityType = CommonTypes::getString($in);
		$packet->isBabyMob = CommonTypes::getBool($in);
		$packet->disableRelativeVolume = CommonTypes::getBool($in);
		$packet->actorUniqueId = LE::readSignedLong($in);
		$packet->firePosition = null;
		return $packet;
	}
}
