<?php

namespace App\Console\Commands;

use App\Models\Agriculture;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Reloadagricultures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-gis:reload-agricultures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate the agricultures table and reload the geojson';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $geojson = file_get_contents(resource_path('/geojson/agriculture.geojson'));

        // Delete all existing records from the agricultures table
        Agriculture::truncate();

        // Decode the geojson file content (it's json after all) into an array, create a Laravel collection
        // from the features element, loop through each feature and create a monument in the
        // database. For the geom field, we use a raw expression to use the ST_GeomFromGeoJSON
        // function, passing it the feature's geometry fragment re-encoded to json.
        $features = collect(json_decode($geojson, true)['features'])->each(function ($feature) {
            Agriculture::create([
                'name' => $feature['properties']['name'],
                'image' => $feature['properties']['image'],
                'geom' => DB::raw("ST_GeomFromGeoJSON('" . json_encode($feature['geometry']) . "')"),
            ]);
        });

        $this->info($features->count() . ' agricultures loaded successfully.');

        return 0;
    }
}
