<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("address");
            $table->float("lat")->nullable();
            $table->string("lon")->nullable();
            $table->string("type_primaty_key")->nullable(); //todo тут либо primate - приматы либо private либо primary
            $table->string("type")->nullable();
            $table->string("wind_direction")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stations');
    }
}
