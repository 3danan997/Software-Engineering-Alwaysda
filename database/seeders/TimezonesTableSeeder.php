<?php

namespace Database\Seeders;

use App\Models\Utils\Timezone;
use Illuminate\Database\Seeder;

class TimezonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = time();

        Timezone::create(["position" => 0, "title" => "Automatic", "offset" => "00", "gtm_diff" => "Automatic"]);

        $i = 1;
        foreach (timezone_identifiers_list() as $zone) {
            date_default_timezone_set($zone);
            $zones['position'] = $i;
            $zones['offset'] = date('P', $timestamp);
            $zones['gtm_diff'] = date('P', $timestamp);

            Timezone::updateOrCreate(['title' => $zone], $zones);
            $i++;
        }
    }
}
