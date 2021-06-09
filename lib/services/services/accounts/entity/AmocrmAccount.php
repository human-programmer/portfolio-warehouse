<?php


namespace Services\General;


trait AmocrmAccount {
	private int $amocrm_account_id;
	private string $amocrm_referer;
	private string $amocrm_subdomain;
	private string $amocrm_country;
	private int $amocrm_created_at;
	private int $amocrm_created_by;
	private bool $amocrm_is_technical;
	private string $amocrm_name;

	private function amocrmInit(array $model): void {
		$this->amocrm_account_id = $model['amocrm_account_id'] ?? 0;
		$this->amocrm_referer = $model['amocrm_referer'] ?? '';
		$this->amocrm_subdomain = $model['amocrm_subdomain'] ?? '';
		$this->amocrm_country = $model['amocrm_country'] ?? '';
		$this->amocrm_created_at = $model['amocrm_created_at'] ?? 0;
		$this->amocrm_created_by = $model['amocrm_created_by'] ?? 0;
		$this->amocrm_is_technical = !!$model['amocrm_is_technical'];
		$this->amocrm_name = $model['amocrm_name'] ?? '';
	}

	function getAmocrmAccountId(): int {
		return $this->amocrm_account_id;
	}

	function getAmocrmReferer(): string {
		return $this->amocrm_referer;
	}

	function getAmocrmSubdomain(): string {
		return $this->amocrm_subdomain;
	}

	function getAmocrmCountry(): string {
		return $this->amocrm_country;
	}
	function getAmocrmName(): string {
		return $this->amocrm_name;
	}

	function getAmocrmCreateTime(): int {
		return $this->amocrm_created_at;
	}

	function getAmocrmCreatedByUserId(): int {
		return $this->amocrm_created_by;
	}

	function isAmocrmTechnicalAccount(): bool {
		return $this->amocrm_is_technical;
	}

	private function getAmocrmModel(): array {
		return [
			'amocrm_account_id' => $this->getAmocrmAccountId(),
			'amocrm_referer' => $this->getAmocrmReferer(),
			'amocrm_subdomain' => $this->getAmocrmSubdomain(),
			'amocrm_country' => $this->getAmocrmCountry(),
			'amocrm_created_at' => $this->getAmocrmCreateTime(),
			'amocrm_created_by' => $this->getAmocrmCreatedByUserId(),
			'amocrm_name' => $this->getAmocrmName(),
			'amocrm_is_technical' => $this->isAmocrmTechnicalAccount(),
		];
	}
}