<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use App\Models\LeadSource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => "admin@admin.com"
        ]);

        Customer::factory()
            ->count(10)
            ->create();

        $leadSources = [
            'Website',
            'Online Ad',
            'Twitter',
            'Webinar',
            'Trade Show',
            'Referral'
        ];

        foreach ($leadSources as $leadSource){
            LeadSource::create(['name' => $leadSource]);
        }

        $tags =[
            'Priority',
            'VIP'
        ];
        foreach($tags as $tag) {
            Tag::create(['name' => $tag]);
        }
    }
}
