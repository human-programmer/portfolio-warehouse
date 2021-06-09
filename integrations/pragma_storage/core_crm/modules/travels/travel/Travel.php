<?php


namespace PragmaStorage;


use Generals\Functions\Date;

require_once __DIR__ . '/../../../business_rules/travels/ITravel.php';
require_once __DIR__ . '/TravelModel.php';
require_once __DIR__ . '/TravelUpdater.php';

class Travel extends TravelModel implements ITravel {
	use TravelUpdater;

	private int $creation_date;
	private int $travel_status;

	public function __construct(private IStoreApp $app, private ITravels $travels, array $model) {
		parent::__construct($model);
		$this->creation_date = gettype($model['creation_date']) === "integer" ? $model['creation_date'] : Date::getIntTimeStamp($model['creation_date']);
		$this->travel_status = $model['travel_status'];
	}

	protected function getStoreApp(): IStoreApp {
		return $this->app;
	}

	function getCreationDate(): int {
		return $this->creation_date;
	}

	function toArray(): array {
		return array_merge(parent::toArray(), [
				'creation_date' => $this->creation_date,
				'travel_status' => $this->travel_status,
                'links' => $this->getLinksModels(),
			]
		);
	}

	function getTravelStatus(): int {
		return $this->travel_status;
	}

	function setDeliveredStatus(): void {
		$this->travel_status = EXPORT_STATUS_EXPORTED;
	}

    function getSelf(): self {
        return $this;
    }

    function findTravelLink(int $product_id): ITravelLink|null {
        return $this->app->getTravelLinks()->findTravelLink($this->getTravelId(), $product_id);
    }

    private function getLinksModels(): array {
	    $links = $this->getLinks();
	    foreach($links as $link)
	        $result[] = $link->toArray();
	    return $result ?? [];
    }

    function getLinks(): array {
	    return $this->app->getTravelLinks()->getTravelsLinks($this->getTravelId());
    }
}