<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 *
 * Port persis dari PlaySoundPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Clientbound-only. Posisi suara di-encode lewat BlockPosition varian LAMA
 * (unsigned-Y, lihat LegacyBlockPosition) dikali 8 (fixed point), dan field
 * baru `serverSoundHandle` (optional) di versi 1.26.30 di-skip sepenuhnya.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pocketmine\multiversion\legacy\LegacyBlockPosition;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\BlockPosition;

final class PlaySoundPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(PlaySoundPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		CommonTypes::putString($out, $packet->soundName);
		LegacyBlockPosition::write($out, new BlockPosition(
			(int) ($packet->x * 8),
			(int) ($packet->y * 8),
			(int) ($packet->z * 8)
		));
		LE::writeFloat($out, $packet->volume);
		LE::writeFloat($out, $packet->pitch);

		return $out->getData();
	}
}
