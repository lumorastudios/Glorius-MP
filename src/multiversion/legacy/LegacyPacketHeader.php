<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 *
 * Replikasi persis dari DataPacket::encodeHeader() (vendor bedrock-protocol),
 * dipakai supaya legacy codec bisa menulis header packet yang identik tanpa
 * perlu mengakses method protected milik vendor.
 *
 * Format header ini SUDAH DIKONFIRMASI SAMA PERSIS antara bedrock-protocol
 * 55.0.0 (1.26.0) dan 58.0.0 (1.26.30) - tidak butuh versi berbeda.
 */

namespace pocketmine\multiversion\legacy;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\DataPacket;

final class LegacyPacketHeader{

	private function __construct(){
		//NOOP
	}

	public static function write(ByteBufferWriter $out, DataPacket $packet) : void{
		VarInt::writeUnsignedInt(
			$out,
			$packet::NETWORK_ID |
			($packet->senderSubId << 10) |
			($packet->recipientSubId << 12)
		);
	}
}
