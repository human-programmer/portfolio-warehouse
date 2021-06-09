<?php


namespace PragmaStorage\Export;


class ExportsTotalPrice {

    private float $total_not_deficit_purchase_price = 0.0;
    private float $not_deficit_quantity = 0.0;
    private float $deficit_quantity = 0.0;

    function __construct(private array $details) {}

    function getTotalPurchasePrice(): float {
        $this->calcNotDeficitTotalPurchasePrice();
        $this->calcNotDeficitQuantity();
        $this->calcDeficitQuantity();
        return $this->getTotalCalcPurchasePrice();
    }

    private function calcNotDeficitTotalPurchasePrice(): void {
        foreach($this->details as $detail)
            if(!$detail->isDeficit())
                $this->total_not_deficit_purchase_price += $detail->getTotalPurchasePrice();
    }

    private function calcNotDeficitQuantity(): void {
        foreach($this->details as $detail)
            if(!$detail->isDeficit())
                $this->not_deficit_quantity += $detail->getQuantity();
    }

    private function calcDeficitQuantity(): void {
        foreach($this->details as $detail)
            if($detail->isDeficit())
                $this->deficit_quantity += $detail->getQuantity();
    }

    private function getTotalCalcPurchasePrice(): float {
        return $this->total_not_deficit_purchase_price + $this->getDeficitTotalPurchasePrice();
    }

    private function getDeficitTotalPurchasePrice(): float {
        if(!$this->not_deficit_quantity) return 0.0;
        return $this->total_not_deficit_purchase_price / $this->not_deficit_quantity * $this->deficit_quantity;
    }
}