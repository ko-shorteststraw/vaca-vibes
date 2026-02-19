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

    private const ACTIVITIES = [
        'maui' => [
            ['title' => 'Snorkeling at Molokini Crater', 'description' => 'Crystal-clear waters with 150+ ft visibility and vibrant coral reefs.', 'estimated_cost' => 65, 'category' => 'Adventure'],
            ['title' => 'Road to Hana Drive', 'description' => 'Scenic 64-mile drive with waterfalls, rainforest, and coastal views.', 'estimated_cost' => 30, 'category' => 'Nature'],
            ['title' => 'Luau at Old Lahaina', 'description' => 'Traditional Hawaiian feast with hula dancing and fire performers.', 'estimated_cost' => 130, 'category' => 'Culture'],
            ['title' => 'Haleakala Sunrise', 'description' => 'Watch the sunrise from the summit of a 10,000-ft dormant volcano.', 'estimated_cost' => 15, 'category' => 'Nature'],
            ['title' => 'Shave Ice at Ululani\'s', 'description' => 'Iconic Hawaiian treat with tropical syrups and fresh fruit.', 'estimated_cost' => 8, 'category' => 'Food'],
        ],
        'paris' => [
            ['title' => 'Eiffel Tower Summit Visit', 'description' => 'Take the elevator to the top for panoramic views of the city.', 'estimated_cost' => 28, 'category' => 'Culture'],
            ['title' => 'Louvre Museum Tour', 'description' => 'Home to the Mona Lisa and 35,000+ works of art.', 'estimated_cost' => 22, 'category' => 'Culture'],
            ['title' => 'Seine River Cruise', 'description' => 'Evening boat cruise past Notre-Dame, Louvre, and more.', 'estimated_cost' => 18, 'category' => 'Adventure'],
            ['title' => 'Pastry Class in Le Marais', 'description' => 'Learn to make croissants and macarons from a French chef.', 'estimated_cost' => 95, 'category' => 'Food'],
            ['title' => 'Montmartre Walking Tour', 'description' => 'Explore the artistic hilltop neighborhood and Sacre-Coeur.', 'estimated_cost' => 25, 'category' => 'Culture'],
        ],
        'banff' => [
            ['title' => 'Canoe on Lake Louise', 'description' => 'Paddle across turquoise glacial waters surrounded by peaks.', 'estimated_cost' => 50, 'category' => 'Adventure'],
            ['title' => 'Johnston Canyon Hike', 'description' => 'Walk along catwalks through a limestone canyon to waterfalls.', 'estimated_cost' => 0, 'category' => 'Nature'],
            ['title' => 'Banff Gondola Ride', 'description' => 'Ride to the summit of Sulphur Mountain for 360-degree views.', 'estimated_cost' => 70, 'category' => 'Adventure'],
            ['title' => 'Elk Burger at Park Distillery', 'description' => 'Local Rocky Mountain cuisine in the heart of Banff town.', 'estimated_cost' => 35, 'category' => 'Food'],
            ['title' => 'Icefields Parkway Drive', 'description' => 'One of the most scenic drives in the world along the Rockies.', 'estimated_cost' => 20, 'category' => 'Nature'],
        ],
        'bali' => [
            ['title' => 'Tegallalang Rice Terraces', 'description' => 'Walk through stunning UNESCO-recognized terraced rice paddies.', 'estimated_cost' => 5, 'category' => 'Nature'],
            ['title' => 'Uluwatu Temple at Sunset', 'description' => 'Clifftop temple with traditional Kecak fire dance at dusk.', 'estimated_cost' => 10, 'category' => 'Culture'],
            ['title' => 'White Water Rafting', 'description' => 'Raft the Ayung River through tropical gorges and waterfalls.', 'estimated_cost' => 40, 'category' => 'Adventure'],
            ['title' => 'Balinese Cooking Class', 'description' => 'Visit a local market and cook traditional dishes with a family.', 'estimated_cost' => 30, 'category' => 'Food'],
            ['title' => 'Spa & Flower Bath', 'description' => 'Traditional Balinese massage and petal-filled bath experience.', 'estimated_cost' => 25, 'category' => 'Adventure'],
        ],
        'tokyo' => [
            ['title' => 'Tsukiji Outer Market Tour', 'description' => 'Sample fresh sushi, tamagoyaki, and street food at the famous market.', 'estimated_cost' => 35, 'category' => 'Food'],
            ['title' => 'Senso-ji Temple Visit', 'description' => 'Tokyo\'s oldest temple in Asakusa with traditional shops along the approach.', 'estimated_cost' => 0, 'category' => 'Culture'],
            ['title' => 'Shibuya Crossing & Harajuku', 'description' => 'Experience the world\'s busiest intersection and quirky fashion district.', 'estimated_cost' => 15, 'category' => 'Culture'],
            ['title' => 'Ramen Tasting in Shinjuku', 'description' => 'Try multiple styles of ramen at the best shops in the city.', 'estimated_cost' => 20, 'category' => 'Food'],
            ['title' => 'Day Trip to Mount Fuji', 'description' => 'Bus to the 5th Station of Japan\'s iconic peak with stunning views.', 'estimated_cost' => 55, 'category' => 'Nature'],
        ],
        'costa rica' => [
            ['title' => 'Arenal Volcano Hike', 'description' => 'Trek through lava fields and rainforest at the base of the volcano.', 'estimated_cost' => 45, 'category' => 'Adventure'],
            ['title' => 'Zip-Lining in Monteverde', 'description' => 'Soar above the cloud forest canopy on high-speed cables.', 'estimated_cost' => 60, 'category' => 'Adventure'],
            ['title' => 'Manuel Antonio Beach', 'description' => 'Relax on white sand beaches inside a national park with monkeys.', 'estimated_cost' => 18, 'category' => 'Nature'],
            ['title' => 'Coffee Plantation Tour', 'description' => 'Learn how Costa Rican coffee is grown, harvested, and roasted.', 'estimated_cost' => 35, 'category' => 'Food'],
            ['title' => 'Hot Springs at Tabacon', 'description' => 'Soak in naturally heated volcanic springs in the rainforest.', 'estimated_cost' => 90, 'category' => 'Nature'],
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

    /** @return array<int, array> */
    public function findActivitiesByDestination(string $destination): array
    {
        $destination = strtolower($destination);
        foreach (self::ACTIVITIES as $key => $activities) {
            if (str_contains($destination, $key)) {
                return $activities;
            }
        }
        return [];
    }
}
