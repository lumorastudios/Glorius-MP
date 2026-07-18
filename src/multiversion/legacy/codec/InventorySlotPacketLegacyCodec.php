<?php

declare(strict_types=1);

/*
 * MultiVersion support for PocketMine-MP 5.44.3
 *
 * Port persis dari InventorySlotPacket versi bedrock-protocol 55.0.0 (1.26.0).
 * Clientbound-only. Di 1.26.30, $containerName dan $storage jadi nullable
 * (dibungkus optional-bool di wire format) - client lama TIDAK mengenal
 * representasi "kosong" itu sama sekali, jadi kalau nilainya null di sini kita
 * isi default yang aman (containerId 0, item kosong) supaya tetap valid dibaca.
 */

namespace pocketmine\multiversion\legacy\codec;

use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\VarInt;
use pocketmine\multiversion\legacy\LegacyItemStackWrapper;
use pocketmine\multiversion\legacy\LegacyPacketHeader;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\types\inventory\FullContainerName;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

final class InventorySlotPacketLegacyCodec{

	private function __construct(){
		//NOOP
	}

	public static function encode(InventorySlotPacket $packet) : string{
		$out = new ByteBufferWriter();
		LegacyPacketHeader::write($out, $packet);

		VarInt::writeUnsignedInt($out, $packet->windowId);
		VarInt::writeUnsignedInt($out, $packet->inventorySlot);

		($packet->containerName ?? new FullContainerName(0))->write($out);
		LegacyItemStackWrapper::write($out, $packet->storage ?? new ItemStackWrapper(0, ItemStack::null()));
		LegacyItemStackWrapper::write($out, $packet->item);

		return $out->getData();
	}
}
