<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 *
 * Port persis dari UpdateClientOptionsPacket versi bedrock-protocol 55.0.0
 * (1.26.0). Serverbound-only. PM sendiri TIDAK memproses isi packet ini sama
 * sekali, tapi kalau tidak ditangani, client lama yang mengirim packet ini
 * (mis. saat ganti graphics mode di menu options ketika sedang di server)
 * akan menyebabkan buffer-underrun saat decode field baru `filterProfanityChange`
 * di akhir - yang berpotensi membuat sesi ter-disconnect. Jadi tetap perlu
 * ditangani demi keamanan walau datanya tidak dipakai.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\GraphicsMode;
use pocketmine\network\mcpe\protocol\UpdateClientOptionsPacket;

final class UpdateClientOptionsPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function decodePayload(ByteBufferReader $in) : UpdateClientOptionsPacket{
		$graphicsMode = CommonTypes::readOptional($in, fn() => GraphicsMode::fromPacket(Byte::readUnsigned($in)));
		return UpdateClientOptionsPacket::create($graphicsMode, null);
	}
}
