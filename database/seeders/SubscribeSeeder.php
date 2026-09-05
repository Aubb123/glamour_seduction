<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Subscribe;
use App\Models\SubscribeHistory;
use Illuminate\Database\Seeder;

class SubscribeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscribes = [
            [
                'email' => 'jean.dupont@example.fr',
                'status' => 'active',
                'verified_at' => getDateTime(),
                'user_id' => null,
                'history' => [
                    ['action' => 'subscribed',   'acted_at' => getDateTime()->subDays(30), 'ip_address' => '192.168.1.10'],
                ],
            ],
            [
                'email' => 'marie.martin@example.fr',
                'status' => 'active',
                'verified_at' => getDateTime()->subDays(15),
                'user_id' => null,
                'history' => [
                    ['action' => 'subscribed',   'acted_at' => getDateTime()->subDays(20), 'ip_address' => '192.168.1.11'],
                    ['action' => 'unsubscribed', 'acted_at' => getDateTime()->subDays(18), 'ip_address' => '192.168.1.11'],
                    ['action' => 'resubscribed', 'acted_at' => getDateTime()->subDays(15), 'ip_address' => '192.168.1.11'],
                ],
            ],
            [
                'email' => 'john.doe@example.com',
                'status' => 'inactive',
                'verified_at' => getDateTime()->subDays(60),
                'user_id' => null,
                'history' => [
                    ['action' => 'subscribed',   'acted_at' => getDateTime()->subDays(60), 'ip_address' => '10.0.0.5'],
                    ['action' => 'unsubscribed', 'acted_at' => getDateTime()->subDays(10), 'ip_address' => '10.0.0.5'],
                ],
            ],
            [
                'email' => 'amina.diallo@example.bj',
                'status' => 'active',
                'verified_at' => null, // email non encore vérifié
                'user_id' => null,
                'history' => [
                    ['action' => 'subscribed',   'acted_at' => getDateTime()->subDays(2),  'ip_address' => '41.242.116.5'],
                ],
            ],
            [
                'email' => 'carlos.silva@example.com',
                'status' => 'active',
                'verified_at' => getDateTime()->subDays(45),
                'user_id' => null,
                'history' => [
                    ['action' => 'subscribed',   'acted_at' => getDateTime()->subDays(50), 'ip_address' => '200.178.32.1'],
                    ['action' => 'unsubscribed', 'acted_at' => getDateTime()->subDays(48), 'ip_address' => '200.178.32.1'],
                    ['action' => 'resubscribed', 'acted_at' => getDateTime()->subDays(45), 'ip_address' => '200.178.32.1'],
                ],
            ],
        ];

        foreach ($subscribes as $data) {

            $history = $data['history'];
            unset($data['history']);

            // Créer l'abonné
            $subscribe = Subscribe::create([
                'user_id' => $data['user_id'],
                'email' => $data['email'],
                'status' => $data['status'],
                'verified_at' => $data['verified_at'],
            ]);

            // Créer l'historique
            foreach ($history as $entry) {
                SubscribeHistory::create([
                    'subscribe_id' => $subscribe->id,
                    'action' => $entry['action'],
                    'acted_at' => $entry['acted_at'],
                    'ip_address' => $entry['ip_address'],
                ]);
            }
        }
    }
}
