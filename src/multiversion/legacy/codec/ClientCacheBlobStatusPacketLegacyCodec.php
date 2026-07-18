<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 * Port persis dari ClientCacheBlobStatusPacket versi bedrock-protocol 55.0.0
 * (1.26.0). Serverbound-only. Urutan baca berbeda: versi lama baca KEDUA
 * jumlah (miss lalu hit) DULU baru semua hash-nya; versi baru baca per-bagian
 * (miss count + miss hashes, baru hit count + hit hashes).
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\ClientCacheBlobStatusPacket;

final class ClientCacheBlobStatusPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function decodePayload(ByteBufferReader $in) : ClientCacheBlobStatusPacket{
		$missCount = VarInt::readUnsignedInt($in);
		$hitCount = VarInt::readUnsignedInt($in);

		$missHashes = [];
		for($i = 0; $i < $missCount; ++$i){
			$missHashes[] = LE::readUnsignedLong($in);
		}

		$hitHashes = [];
		for($i = 0; $i < $hitCount; ++$i){
			$hitHashes[] = LE::readUnsignedLong($in);
		}

		return ClientCacheBlobStatusPacket::create($hitHashes, $missHashes);
	}
}
