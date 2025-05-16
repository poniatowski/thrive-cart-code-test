<?php

declare(strict_types=1);

namespace Acme\Basket\Contracts;

interface BasketInterface
{
    /**
     * Add a product to the basket
     *
     * @param string $productCode Product code to add
     * @throws \InvalidArgumentException If product code doesn't exist
     */
    public function add(string $productCode): void;

    /**
     * Calculate the total cost of the basket
     *
     * @return float Total amount including discounts and delivery
     */
    public function total(): float;
}
