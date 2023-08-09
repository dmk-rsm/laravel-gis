<?php

namespace App\Console\Commands;

use App\Models\Manandona;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReloadAgriculture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-gis:reload-agriculture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate the agricultures table and reload the geojson';

    /**
     * Execute the console command.
     * 
     * @var int
     */
    public function handle()
    {
        $geojson = file_get_contents(resource_path('/geojson/agriculture.geojson'));
        //
        Manandona::truncate();
        $features = collect(json_decode($geojson, true)['features'])->each(function ($feature) {
            Manandona::create([
                'name' => $feature['properties']['name'],
                'image' => $feature['properties']['image'],
                'geom' => DB::raw("ST_GeomFromGeoJSON('" . json_encode($feature['geometry']) . "')")
            ]);
        });
        $this->info($features->count() . ' Manandona loaded successfully.');
        return 0;
    }
}
