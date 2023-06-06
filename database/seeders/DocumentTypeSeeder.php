<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentType::truncate();
        $data = [
            'NDA #1',
            'NDA #2',
            'NDA #3',
        ];
        collect($data)->each(function($type) {
            DocumentType::create([
                'name' => $type
            ]);
        });
    }
}
