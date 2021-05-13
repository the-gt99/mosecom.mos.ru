<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indication_id');
            $table->unsignedBigInteger('station_id');
            $table->double("proportion")->nullable();
            $table->double("unit")->nullable();
            $table->timestamps();
            $table->timestamp("measurement_at");


            $table->foreign('station_id')
                ->references('id')
                ->on('stations');
            $table->foreign('indication_id')
                ->references('id')
                ->on('type_of_indication');
        });
    }

    /**php artisan make:model Flight
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records');
    }
}
