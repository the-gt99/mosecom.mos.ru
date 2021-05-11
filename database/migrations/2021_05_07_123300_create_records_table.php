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
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('indication_type_id');
            $table->double("proportion");
            $table->double("unit");
            $table->timestamps();

            $table->foreign('station_id')
                ->references('id')
                ->on('stations');
            $table->foreign('indication_type_id')
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
