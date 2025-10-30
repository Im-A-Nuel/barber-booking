<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarbersTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('specialty')->nullable();           // contoh: Fade, Classic Cut, Beard Trim
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('phone', 20)->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0.00); // 0.00 - 5.00
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barbers_tabel');
    }
}
