<?php

namespace Database\Seeders;

use App\Models\Month;
use Illuminate\Database\Seeder;

class MonthSeeder extends Seeder
{
    public function run(): void
    {
        $months = [
            ['en' => 'Baisakh', 'np' => 'बैशाख'],
            ['en' => 'Jestha', 'np' => 'जेठ'],
            ['en' => 'Ashadh', 'np' => 'असार'],
            ['en' => 'Shrawan', 'np' => 'श्रावण'],
            ['en' => 'Bhadra', 'np' => 'भदौ'],
            ['en' => 'Ashwin', 'np' => 'आश्विन'],
            ['en' => 'Kartik', 'np' => 'कार्तिक'],
            ['en' => 'Mangsir', 'np' => 'मंसिर'],
            ['en' => 'Poush', 'np' => 'पुष'],
            ['en' => 'Magh', 'np' => 'माघ'],
            ['en' => 'Falgun', 'np' => 'फाल्गुण'],
            ['en' => 'Chaitra', 'np' => 'चैत'],
        ];

        foreach ($months as $index => $month) {
            Month::updateOrCreate(
                ['order_no' => $index + 1],
                [
                    'name_en' => $month['en'],
                    'name_np' => $month['np'],
                ]
            );
        }
    }
}
