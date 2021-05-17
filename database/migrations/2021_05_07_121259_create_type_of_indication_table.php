<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTypeOfIndicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_of_indication', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("code_name");
        });


        DB::table('type_of_indication')->insert([
            ['name' => 'PM2.5', 'code_name' => 'sds_p1'],
            ['name' => 'PM10', 'code_name' => 'sds_p2']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_of_indication');
    }
}
