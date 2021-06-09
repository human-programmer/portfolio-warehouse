<?php


namespace PragmaStorage;


trait TravelLinkUpdater {
    private function checkProductImportIsLinked(): void {
        $this->getReceiveProductImportId() || $this->linkProductImport();
    }

    private function linkProductImport(): void {
        $import = $this->createReceiveProductImport();
        $this->setReceiveProductImportId($import->getProductImportId());
    }

    private function createReceiveProductImport(): iProductImport {
        $travel = $this->getTravel();
        return $this->getApp()->getProductImports()->createTravelsImport($travel, $this->getProductId());
    }

    private function checkExportIsLinked(): void {
        $this->findStartExport() || $this->linkExport();
    }

    private function linkExport(): void {
        $export = $this->createExport();
        $this->setStartExportId($export->getExportId());
        $export->setStatus(EXPORT_STATUS_EXPORTED);
    }

    private function createExport(): iExport {
        $exportModel = $this->getApp()->getExports()->createLinkedTravelsExportModel($this->getProductId());
        return $this->getApp()->getExports()->createExportFromStruct($exportModel);
    }

    private function updateLinksQuantity(): void {
        $this->updateQuantityOfProductImport();
        $this->updateQuantityOfExport();
        $this->updatePurchasePrice();
        $this->saveSelf();
    }

    private function updateQuantityOfProductImport(): void {
        $import = $this->findReceiveProductImport();
        if($this->getQuantity() !== $import->getImportQuantity())
            $import->setQuantity($this->getQuantity());
    }

    private function updateQuantityOfExport(): void {
        $export = $this->findStartExport();
        if($this->getQuantity() == $export->getQuantity()) return;
        $export->setQuantity($this->getQuantity());
    }

    private function updatePurchasePrice(): void {
        if(!$this->findStartExport()) return;
        $avg = $this->getAvgPurchasePrice();
        $this->findReceiveProductImport()?->setPurchasePrice($avg);
    }

    private function getAvgPurchasePrice(): float {
        if(!$this->getExportQuantity()) return 0.0;
        return $this->getTotalPurchasePrice() / $this->getExportQuantity();
    }

    private function getExportQuantity(): float {
        return $this->findStartExport()?->getQuantity() ?? 0.0;
    }
}