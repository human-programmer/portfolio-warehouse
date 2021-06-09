<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../../business_rules/travel_export_link/ITravelLink.php';
require_once __DIR__ . '/TravelLinkStruct.php';
require_once __DIR__ . '/TravelLinkUpdater.php';


class TravelLink extends TravelLinkStruct implements ITravelLink {
    use TravelLinkUpdater;

    function __construct(private IStoreApp $app, array $model) {
        parent::__construct($model);
    }

    function findReceiveProductImport(): iProductImport|null {
        $id = $this->getReceiveProductImportId();
        return $id ? $this->app->getProductImports()->getProductImport($id) : null;
    }

    function findStartExport(): iExport|null {
        $id = $this->getStartExportId();
        return $id ? $this->app->getExports()->getExport($id) : null;
    }

    function updateLinks(): void {
        $this->checkProductImportIsLinked();
        $this->checkExportIsLinked();
        $this->saveSelf();
        $this->updateLinksQuantity();
    }

    function setQuantity(float $quantity): void {
        if($this->getQuantity() === $quantity) return;
        parent::setQuantity($quantity);
        $this->updateLinksQuantity();
    }

    function getApp(): IStoreApp {
        return $this->app;
    }

    function getTravel(): ITravelModel {
        return $this->app->getTravels()->getTravel($this->getTravelId());
    }

    function saveSelf(): void {
        TravelLinksSchema::save($this->getSelf());
    }

    private function getSelf(): self {
        return $this;
    }

    function getTotalPurchasePrice(): float {
        return $this->findStartExport()?->getTotalPurchasePrice() ?? 0.0;
    }
}