<?php

declare(strict_types=1);

namespace Acme\Basket\Offers;

use Acme\Basket\Contracts\OfferCalculatorInterface;
use Acme\Basket\ValueObjects\Product;
use Money\Money;

class RedWidgetSecondHalfPriceOffer implements OfferCalculatorInterface
{
    private const RED_WIDGET_CODE = 'R01';
    
    public function calculateDiscount(array $items, array $productCatalogue): Money
    {
        $redWidgetCount = $items[self::RED_WIDGET_CODE] ?? 0;
        
        if ($redWidgetCount < 2) {
            return Money::USD(0);
        }
        
        /** @var Product $redWidget */
        $redWidget = $productCatalogue[self::RED_WIDGET_CODE];
        $discountQuantity = (int) floor($redWidgetCount / 2);
        
        return $redWidget->price->divide(2)->multiply($discountQuantity);
    }
}
