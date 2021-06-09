<?php


namespace PragmaStorage;

require_once __DIR__ . '/link/TravelLink.php';
require_once __DIR__ . '/TravelLinksSchema.php';
require_once __DIR__ . '/TravelsLinksBuffer.php';

trait TravelsLinksLoader {
    use TravelsLinksBuffer;

    private function createTravelsLink(int $travel_id, int $product_id): ITravelLink {
        $link = $this->createStructFromModel(['travel_id' => $travel_id, 'product_id' => $product_id, 'quantity' => 0]);
        $link->saveSelf();
        $link = $this->loadTargetLink($travel_id, $product_id);
        $link->updateLinks();
        return $link;
    }

    private function loadTargetLink(int $travel_id, int $product_id): ITravelLink {
        $model = TravelLinksSchema::getProductsTravelLinkRow($travel_id, $product_id);
        return $this->createStructFromModel($model);
    }

    private function getFromDb(int $travel_id): array {
        $models = TravelLinksSchema::getTravelsLinksRows($travel_id);
        $this->loadTravelsModels($travel_id, $models);
        return $this->findAllOfTravelInBuffer($travel_id);
    }

    private function getFromDbByExport(int $export_id): ITravelLink {
        $models = TravelLinksSchema::getExportsLinksRows($export_id);
        $this->validExportsLoadedModels($models);
        $this->loadTravelsModels($models[0]['travel_id'], $models);
        return $this->findExportsTravelLinkInBuffer($export_id);
    }

    private function validExportsLoadedModels(array $models): void {
        if(!count($models)) throw new \Exception("Request returned an empty result");
    }

    private function loadTravelsModels(int $travel_id, array $rows): void {
        foreach ($rows as $row)
            $this->createStructFromModel($row);
        $this->setTravelLoadedInBuffer($travel_id);
    }

    private function createStructFromModel(array $model): ITravelLink {
        $struct = new TravelLink($this->getApp(), $model);
        $this->addLinkInBuffer($struct);
        return $struct;
    }


}