<?php

declare(strict_types=1);

namespace Acme\Basket;

use Acme\Basket\Contracts\BasketInterface;
use Acme\Basket\Contracts\DeliveryCalculatorInterface;
use Acme\Basket\Contracts\OfferCalculatorInterface;
use Acme\Basket\ValueObjects\Product;
use Money\Currency;
use Money\Money;

final class ShoppingBasket implements BasketInterface
{
     /** @var array<string, int> */
    private array $items = [];

    /** @var array<string, Product> */
    private array $productCatalogue;

    private Currency $currency;

    public function __construct(
        array $productCatalogue,
        private DeliveryCalculatorInterface $deliveryCalculator,
        private OfferCalculatorInterface $offerCalculator,
        string $currency = 'USD'
    ) {
        $this->currency = new Currency($currency);
        $this->initializeProductCatalogue($productCatalogue);
    }

    public function add(string $productCode): void
    {
        if (!isset($this->productCatalogue[$productCode])) {
            throw new \InvalidArgumentException("Product with code {$productCode} not found");
        }

        $this->items[$productCode] = ($this->items[$productCode] ?? 0) + 1;
    }
    private function calculateSubtotal(): Money
    {
        $total = new Money(0, $this->currency);

        foreach ($this->items as $productCode => $quantity) {
            $product = $this->productCatalogue[$productCode];
            $total = $total->add($product->price->multiply($quantity));
        }

        return $total;
    }

    public function total(): float
    {
        if (empty($this->items)) {
            return 0.0;
        }

        $subtotal = $this->calculateSubtotal();
        $discount = $this->offerCalculator->calculateDiscount($this->items, $this->productCatalogue, $this->currency);
        $delivery = $this->deliveryCalculator->calculate($subtotal->subtract($discount));

        return $subtotal->subtract($discount)->add($delivery)->getAmount() / 100;
    }

    private function initializeProductCatalogue(array $products): void
    {
        $this->productCatalogue = [];

        foreach ($products as $product) {
             $this->productCatalogue[$product->code] = $product;
        }
    }
}
