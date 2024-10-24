<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HmoSeeder extends Seeder
{
    private $hmos = [
        [
            'name' => 'HMO A',
            'code' => 'HMOA123',
            'batching_criteria' => 'submission_date',
            'email' => 'contact@hmoa.com',
        ],
        [
            'name' => 'HMO B',
            'code' => 'HMOB456',
            'batching_criteria' => 'encounter_date',
            'email' => 'support@hmob.com',
        ],
        [
            'name' => 'HMO C',
            'code' => 'HMOC789',
            'batching_criteria' => 'submission_date',
            'email' => 'info@hmoc.com',
        ],
        [
            'name' => 'HMO D',
            'code' => 'HMOD012',
            'batching_criteria' => 'encounter_date',
            'email' => 'helpdesk@hmod.com',
        ],
    ];    

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hmos')->insert($this->hmos);
    }
}