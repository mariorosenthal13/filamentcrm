<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use App\Models\LeadSource;
use App\Models\PipelineStage;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\Types\True_;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => "admin@admin.com"
        ]);

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

        $pipelineStages = [
            [
                'name' => 'Lead',
                'position' => 1,
                'is_default' => true
            ],
            [
                'name' => 'Contact Made',
                'position' => 2,
                'is_default' => false
            ],
            [
                'name' => 'Proposal Made',
                'position' => 3,
                'is_default' => false
            ],
            [
                'name' => 'Proposal Rejected',
                'position' => 4,
                'is_default' => false
            ],
            [
                'name' => 'Customer',
                'position' => 5,
                'is_default' => false
            ]
        ];

        foreach($pipelineStages as $pipelineStage){
            PipelineStage::create($pipelineStage);
        }
        $defaultPipelineStage = PipelineStage::where('is_default', true)->first()->id;
        Customer::factory()->count('10')->create([
            'pipeline_stage_id' => $defaultPipelineStage,
        ]);
    }
}
