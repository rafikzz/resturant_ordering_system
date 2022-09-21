<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelDaily\Invoices\Facades\Invoice as FacadesInvoice;
use LaravelDaily\Invoices\Invoice;

class Bill extends Invoice
{
     /**
     * @var float
     */
    public $service_charge;


    public function serviceCharge(float $service_charge)
    {
        if($service_charge)
        {
            $this->service_charge = $service_charge;
        }
        return $this;
    }

    public function totalTaxes(float $amount, bool $byPercent = false)
    {
        if ($this->hasTax()) {
            throw new Exception('Invoice: unable to set tax twice.');
        }

        $this->total_taxes             = $amount;
        !$byPercent ?: $this->tax_rate = $amount;

        return $this;
    }

    protected function calculate()
    {
        $total_amount   = null;
        $total_discount = null;
        $total_taxes    = null;

        $this->items->each(
            function ($item) use (&$total_amount, &$total_discount, &$total_taxes) {
                // Gates
                if ($item->hasTax() && $this->hasTax()) {
                    throw new Exception('Invoice: you must have taxes only on items or only on invoice.');
                }

                if ($item->hasDiscount() && $this->hasDiscount()) {
                    throw new Exception('Invoice: you must have discounts only on items or only on invoice.');
                }

                $item->calculate($this->currency_decimals);

                (!$item->hasUnits()) ?: $this->hasItemUnits = true;

                if ($item->hasDiscount()) {
                    $total_discount += $item->discount;
                    $this->hasItemDiscount = true;
                }

                if ($item->hasTax()) {
                    $total_taxes += $item->tax;
                    $this->hasItemTax = true;
                }

                // Totals
                $total_amount += $item->sub_total_price;
            });

        $this->applyColspan();

        /**
         * Apply calculations for provided overrides with:
         * totalAmount(), totalDiscount(), discountByPercent(), totalTaxes(), taxRate()
         * or use values calculated from items.
         */
        $this->hasTotalAmount() ?: $this->total_amount                            = $total_amount;
        $this->total_discount = $total_discount;
        // $this->total_taxes              = $total_taxes;
        // !$this->hasShipping() ?: $this->calculateShipping();

        return $this;
    }

    // public function calculateDiscount(): void
    // {
    //     $totalAmount = $this->total_amount;

    //     if ($this->discount_percentage) {
    //         $newTotalAmount = PricingService::applyDiscount($totalAmount, $this->discount_percentage, $this->currency_decimals, true);
    //     } else {
    //         $newTotalAmount = PricingService::applyDiscount($totalAmount, $this->total_discount, $this->currency_decimals);
    //     }

    //     $this->total_amount   = $newTotalAmount;
    //     $this->total_discount = $totalAmount - $newTotalAmount;
    // }

    // public function calculateTax(): void
    // {
    //     if ($this->taxable_amount) {
    //         return;
    //     }

    //     $this->taxable_amount = $this->total_amount;
    //     $totalAmount          = $this->taxable_amount;

    //     if ($this->tax_rate) {
    //         $newTotalAmount = PricingService::applyTax($totalAmount, $this->tax_rate, $this->currency_decimals, true);
    //     } else {
    //         $newTotalAmount = PricingService::applyTax($totalAmount, $this->total_taxes, $this->currency_decimals);
    //     }

    //     $this->total_amount = $newTotalAmount;
    //     $this->total_taxes  = $newTotalAmount - $totalAmount;
    // }

}
