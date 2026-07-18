<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 *
 * Port persis dari Enchant::write() dan EnchantOption::write() versi
 * bedrock-protocol 55.0.0 (1.26.0). Dibanding 1.26.30:
 * - Enchant.id: dulu Byte (1 byte), sekarang VarInt
 * - EnchantOption.cost: dulu VarInt, sekarang Byte (tertukar posisinya)
 * - Jumlah item tiap list enchant (equip/held/self activated) selalu VarInt
 *   di kedua versi - itu TIDAK berubah, cuma isi Enchant per-item yang beda.
 */

namespace pocketmine\multiversion\legacy;

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pmmp\encoding\VarInt;
use pocketmine\network\mcpe\protocol\serializer\CommonTypes;
use pocketmine\network\mcpe\protocol\types\Enchant;
use pocketmine\network\mcpe\protocol\types\EnchantOption;
use function count;

final class LegacyEnchantOption{

	private function __construct(){
		//NOOP
	}

	private static function writeEnchant(ByteBufferWriter $out, Enchant $enchant) : void{
		Byte::writeUnsigned($out, $enchant->getId());
		Byte::writeUnsigned($out, $enchant->getLevel());
	}

	/**
	 * @param Enchant[] $list
	 */
	private static function writeEnchantList(ByteBufferWriter $out, array $list) : void{
		VarInt::writeUnsignedInt($out, count($list));
		foreach($list as $item){
			self::writeEnchant($out, $item);
		}
	}

	public static function write(ByteBufferWriter $out, EnchantOption $option) : void{
		Byte::writeUnsigned($out, $option->getCost());

		LE::writeUnsignedInt($out, $option->getSlotFlags());
		self::writeEnchantList($out, $option->getEquipActivatedEnchantments());
		self::writeEnchantList($out, $option->getHeldActivatedEnchantments());
		self::writeEnchantList($out, $option->getSelfActivatedEnchantments());

		CommonTypes::putString($out, $option->getName());
		CommonTypes::writeRecipeNetId($out, $option->getOptionId());
	}
}
