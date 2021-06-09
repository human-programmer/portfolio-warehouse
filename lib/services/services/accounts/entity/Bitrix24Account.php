<?php


namespace Services\General;


trait Bitrix24Account {
	private string $bitrix24_lang;
	private string $bitrix24_member_id;
	private string $bitrix24_referer;

	private function bitrix24Init(array $model): void {
		$this->bitrix24_lang = $model['bitrix24_lang'] ?? '';
		$this->bitrix24_member_id = $model['bitrix24_member_id'] ?? '';
		$this->bitrix24_referer = $model['bitrix24_referer'] ?? '';
	}

	function getBitrix24Lang(): string {
		return $this->bitrix24_lang;
	}

	function getBitrix24MemberId(): string {
		return $this->bitrix24_member_id;
	}

	function getBitrix24Referer(): string {
		return $this->bitrix24_referer;
	}
}