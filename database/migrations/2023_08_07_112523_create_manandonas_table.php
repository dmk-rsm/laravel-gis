<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('manandonas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->timestamps();
        });

        DB::statement("SELECT AddGeometryColumn('public', 'manandonas', 'geom', 4326, 'POINT', 2)");
        DB::statement('CREATE INDEX sidx_manandona_geom ON manandonas USING GIST (geom)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manandonas');
    }
};
