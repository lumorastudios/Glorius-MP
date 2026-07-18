<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 *
 * Port persis dari SubChunkRequestPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Serverbound-only (client meminta subchunk tertentu ke server - PENTING untuk
 * loading dunia). Beda dari 1.26.30:
 * - urutan field: dulu basePosition DULU baru daftar entries, sekarang entries
 *   dulu baru basePosition
 * - jumlah entries dulu LE::readUnsignedInt (4 byte tetap), sekarang VarInt
 * - basePosition dulu pakai VarInt (SubChunkPosition::read()), sekarang pakai
 *   LE int tetap (SubChunkPosition::readFixedInts())
 *
 * Catatan: SubChunkPacket (kebalikannya, clientbound, berisi data chunk asli)
 * TERNYATA TIDAK BERUBAH FORMATNYA sama sekali walau nama fungsinya beda
 * (SubChunkPosition::readVarInts() persis sama dengan read() versi lama) -
 * jadi packet itu TIDAK butuh legacy codec.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\SubChunkRequestPacket;
use pocketmine\network\mcpe\protocol\types\SubChunkPosition;
use pocketmine\network\mcpe\protocol\types\SubChunkPositionOffset;
use function count;

final class SubChunkRequestPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function decodePayload(ByteBufferReader $in) : SubChunkRequestPacket{
		$dimension = VarInt::readSignedInt($in);

		$x = VarInt::readSignedInt($in);
		$y = VarInt::readSignedInt($in);
		$z = VarInt::readSignedInt($in);
		$basePosition = new SubChunkPosition($x, $y, $z);

		$entries = [];
		for($i = 0, $count = LE::readUnsignedInt($in); $i < $count; $i++){
			$entries[] = SubChunkPositionOffset::read($in);
		}

		return SubChunkRequestPacket::create($dimension, $basePosition, $entries);
	}
}
