<?php

declare(strict_types=1);

/*
 * Only real differences from 1.26.30: a trailing `isLoggingChat` field
 * (skipped), and LevelSettings needs LegacyLevelSettings for its
 * spawnPosition encoding. Everything else in this packet is unchanged.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyLevelSettings;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use function count;

final class StartGamePacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(StartGamePacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		CommonTypes::putActorUniqueId($out, $packet->actorUniqueId);
		CommonTypes::putActorRuntimeId($out, $packet->actorRuntimeId);
		VarInt::writeSignedInt($out, $packet->playerGamemode);

		CommonTypes::putVector3($out, $packet->playerPosition);

		LE::writeFloat($out, $packet->pitch);
		LE::writeFloat($out, $packet->yaw);

		LegacyLevelSettings::write($out, $packet->levelSettings);

		CommonTypes::putString($out, $packet->levelId);
		CommonTypes::putString($out, $packet->worldName);
		CommonTypes::putString($out, $packet->premiumWorldTemplateId);
		CommonTypes::putBool($out, $packet->isTrial);
		$packet->playerMovementSettings->write($out);
		LE::writeUnsignedLong($out, $packet->currentTick);

		VarInt::writeSignedInt($out, $packet->enchantmentSeed);

		VarInt::writeUnsignedInt($out, count($packet->blockPalette));
		foreach($packet->blockPalette as $entry){
			CommonTypes::putString($out, $entry->getName());
			$out->writeByteArray($entry->getStates()->getEncodedNbt());
		}

		CommonTypes::putString($out, $packet->multiplayerCorrelationId);
		CommonTypes::putBool($out, $packet->enableNewInventorySystem);
		CommonTypes::putString($out, $packet->serverSoftwareVersion);
		$out->writeByteArray($packet->playerActorProperties->getEncodedNbt());
		LE::writeUnsignedLong($out, $packet->blockPaletteChecksum);
		CommonTypes::putUUID($out, $packet->worldTemplateId);
		CommonTypes::putBool($out, $packet->enableClientSideChunkGeneration);
		CommonTypes::putBool($out, $packet->blockNetworkIdsAreHashes);
		$packet->networkPermissions->encode($out);
		//serverJoinInformation's format changed completely in 1.26.30 and PM never
		//populates it anyway, so just force "absent" for legacy sessions.
		CommonTypes::putBool($out, false);
		$packet->serverTelemetryData->write($out);

		//intentionally NOT writing isLoggingChat (a new field in 1.26.30)

		return $out->getData();
	}
}
