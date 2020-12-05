<?php

namespace App\Tests\Helper;

use App\Domain\Entity\MappedXml;

class HelperMappedXml
{
    /**
     * Mapped data from data/coffee_feed_test
     *
     * @return MappedXml
     */
    public function getTestDataMappedXml(): MappedXml
    {
        return new MappedXml(
            [
                'entity_id',
                'CategoryName',
                'sku',
                'name',
                'description',
                'shortdesc',
                'price',
                'link',
                'image',
                'Brand',
                'Rating',
                'CaffeineType',
                'Count',
                'Flavored',
                'Seasonal',
                'Instock',
                'Facebook',
                'IsKCup',
            ],
            [
                [
                    '340',
                    'Green Mountain Ground Coffee',
                    '20',
                    'Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag',
                    '',
                    'Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag steeps cup after cup of smoky-sweet, complex dark roast coffee from Green Mountain Ground Coffee.',
                    '41.6000',
                    'http://www.coffeeforless.com/green-mountain-coffee-french-roast-ground-coffee-24-2-2oz-bag.html',
                    'http://mcdn.coffeeforless.com/media/catalog/product/images/uploads/intro/frac_box.jpg',
                    'Green Mountain Coffee',
                    '0',
                    'Caffeinated',
                    '24',
                    'No',
                    'No',
                    'Yes',
                    '1',
                    '0',
                ],
                [
                    '342',
                    'Nestle Hot Chocolate',
                    '5000081171',
                    'Nestle\'s Rich Hot Chocolate 50 Packets',
                    '',
                    'Nestle\'s Rich Hot Chocolate 50 Packets bulk quantity prepare 50 individual servings of milk chocolate instant hot cocoa from Nestle Hot Chocolate.',
                    '11.9900',
                    'http://www.coffeeforless.com/nestles-milk-hot-chocolate-50-packets.html',
                    'http://mcdn.coffeeforless.com/media/catalog/product//n/e/nestle-hot-chocolate-mix-50-packets.png',
                    'Nestle',
                    '5',
                    '',
                    '50',
                    '',
                    '',
                    'Yes',
                    '1',
                    '0',
                ],
            ]
        );
    }
}
