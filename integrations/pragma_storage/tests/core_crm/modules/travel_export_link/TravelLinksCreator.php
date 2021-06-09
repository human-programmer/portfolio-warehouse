<?php


namespace PragmaStorage;


trait TravelLinksCreator {
    static function travelLinkForBuffer(ITravel $travel, iProduct|int $product, iExport|null $export, iProductImport|null $productImport): ITravelLinkStruct {
        return new TravelLinkStruct([
            'travel_id' => $travel->getTravelId(),
            'product_id' => $product instanceof iProduct ? $product->getProductId() : $product,
            'send_export_id' => $export?->getExportId(),
            'receive_product_import_id' =>$productImport?->getProductImportId()
        ]);
    }

    static function createTravelLink(array $model): ITravelLink {

    }
}