<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 *
 * Port persis dari StartGamePacket::encodePayload() versi bedrock-protocol
 * 55.0.0 (1.26.0). Satu-satunya beda dibanding versi 58.0.0 (1.26.30):
 * - field baru `isLoggingChat` di paling akhir (di-skip)
 * - LevelSettings yang di-embed di tengah packet ini butuh LegacyLevelSettings
 *   (karena field spawnPosition di dalamnya beda semantik encoding-nya)
 *
 * Semua field/tipe LAIN di packet ini (playerMovementSettings,
 * networkPermissions, serverJoinInformation, serverTelemetryData, dst)
 * SUDAH DIKONFIRMASI IDENTIK antara 1.26.0 dan 1.26.30 (lihat diff source),
 * jadi tetap aman dipanggil apa adanya dari objek StartGamePacket.
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
		//serverJoinInformation (fitur Xbox Live "Gathering") formatnya berubah total di
		//1.26.30 (GatheringJoinInfo pakai UUID biner + field baru, bukan string lagi).
		//PM sendiri tidak pernah mengisi field ini (selalu null) untuk server biasa, dan
		//client lama tidak akan mengerti format barunya kalaupun ada - jadi untuk sesi
		//legacy kita PAKSA tulis "tidak ada" (false) apa pun isi objek modernnya, demi
		//keamanan (skip 2 field baru serverJoinInformation & ikutan cegah crash decode).
		CommonTypes::putBool($out, false);
		$packet->serverTelemetryData->write($out);

		//sengaja TIDAK menulis isLoggingChat (field baru di 1.26.30)

		return $out->getData();
	}
}
