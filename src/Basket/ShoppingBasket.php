<?php

declare(strict_types=1);

namespace Acme\Basket;

use Acme\Basket\Contracts\BasketInterface;
use Acme\Basket\Contracts\DeliveryCalculatorInterface;
use Acme\Basket\Contracts\OfferCalculatorInterface;
use Acme\Basket\ValueObjects\Product;
use Money\Money;
use Money\Currency;

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
        $total = Money::USD(0);

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
        $discount = $this->offerCalculator->calculateDiscount($this->items, $this->productCatalogue);
        $delivery = $this->deliveryCalculator->calculate($subtotal->subtract($discount));

        return $subtotal->subtract($discount)->add($delivery)->getAmount() / 100;
    }

    /** @param array<array{code: string, name: string, price: float}> $products */

    private function initializeProductCatalogue(array $products): void
    {
        $this->productCatalogue = [];

        foreach ($products as $product) {
            if (is_array($product)) {
                // Convert float price to Money object (assuming price is in dollars)
                $price = is_float($product['price'])
                    ? Money::USD((int) round($product['price'] * 100))
                    : $product['price'];

                $this->productCatalogue[$product['code']] = new Product(
                    $product['code'],
                    $product['name'],
                    $price
                );
            } elseif ($product instanceof Product) {
                $this->productCatalogue[$product->code] = $product;
            }
        }
    }
}
