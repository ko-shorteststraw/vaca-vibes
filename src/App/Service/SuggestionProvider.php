<?php

declare(strict_types=1);

namespace App\Service;

class SuggestionProvider
{
    private const SUGGESTIONS = [
        [
            'id'          => 1,
            'destination' => 'Maui, Hawaii',
            'budget'      => 3500,
            'notes'       => 'Stunning beaches, snorkeling at Molokini Crater, Road to Hana drive, and world-class sunsets.',
            'image_url'   => 'https://images.unsplash.com/photo-1542259009477-d625272157b7?w=600&h=338&fit=crop',
            'start_date'  => '',
            'end_date'    => '',
            'emoji'       => "\u{1F3D6}\u{FE0F}",
            'tagline'     => 'Island paradise awaits',
        ],
        [
            'id'          => 2,
            'destination' => 'Paris, France',
            'budget'      => 4200,
            'notes'       => 'The Eiffel Tower, Louvre Museum, charming cafes, and strolls along the Seine.',
            'image_url'   => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&h=338&fit=crop',
            'start_date'  => '',
            'end_date'    => '',
            'emoji'       => "\u{1F5FC}",
            'tagline'     => 'The City of Light',
        ],
        [
            'id'          => 3,
            'destination' => 'Banff, Canada',
            'budget'      => 2800,
            'notes'       => 'Turquoise lakes, majestic Rocky Mountain scenery, hiking, and wildlife spotting.',
            'image_url'   => 'https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=600&h=338&fit=crop',
            'start_date'  => '',
            'end_date'    => '',
            'emoji'       => "\u{1F3D4}\u{FE0F}",
            'tagline'     => 'Mountain majesty',
        ],
        [
            'id'          => 4,
            'destination' => 'Bali, Indonesia',
            'budget'      => 2200,
            'notes'       => 'Lush rice terraces, ancient temples, vibrant culture, and affordable luxury.',
            'image_url'   => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&h=338&fit=crop',
            'start_date'  => '',
            'end_date'    => '',
            'emoji'       => "\u{1F334}",
            'tagline'     => 'Tropical serenity',
        ],
        [
            'id'          => 5,
            'destination' => 'Tokyo, Japan',
            'budget'      => 3800,
            'notes'       => 'Neon-lit streets, centuries-old shrines, incredible food scene, and cutting-edge culture.',
            'image_url'   => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=338&fit=crop',
            'start_date'  => '',
            'end_date'    => '',
            'emoji'       => "\u{26E9}\u{FE0F}",
            'tagline'     => 'Where tradition meets future',
        ],
        [
            'id'          => 6,
            'destination' => 'Costa Rica',
            'budget'      => 2500,
            'notes'       => 'Rainforests, volcanoes, zip-lining, and the pure vida lifestyle.',
            'image_url'   => 'https://images.unsplash.com/photo-1519999482648-25049ddd37b1?w=600&h=338&fit=crop',
            'start_date'  => '',
            'end_date'    => '',
            'emoji'       => "\u{1F33F}",
            'tagline'     => 'Pura vida adventure',
        ],
    ];

    /** @return array<int, array> */
    public function getAll(): array
    {
        return self::SUGGESTIONS;
    }

    public function findById(int $id): ?array
    {
        foreach (self::SUGGESTIONS as $suggestion) {
            if ($suggestion['id'] === $id) {
                return $suggestion;
            }
        }
        return null;
    }
}
