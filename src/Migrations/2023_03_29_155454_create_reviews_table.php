<?php

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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->efficientUuid('uuid')->unique();
            $table->integer('number');
            $table->string('approvable_type');
            $table->unsignedBigInteger('approvable_id')->index();
            $table->string('reviewed_by');
            $table->dateTime('reviewed_at');
            $table->boolean('accepted');
            $table->timestampsTz();
            $table->softDeletesTz('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review');
    }
};
