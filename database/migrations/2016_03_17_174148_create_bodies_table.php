<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBodiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oparl_bodies', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            // oparl.id is not in the db layer
            // type is not in the db layer

            $table->unsignedInteger('system_id')->nullable();
            $table->foreign('system_id')->references('id')->on('oparl_systems');

            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('website')->nullable();
            $table->string('license')->nullable();
            $table->dateTime('license_valid_since')->nullable();
            $table->dateTime('oparl_since')->nullable();

            $table->string('ags')->nullable();
            $table->string('rgs')->nullable();

            $table->json('equivalent_body')->nullable();

            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();

            // organization is not in the db layer
            // person
            // meeting
            // paper
            // legislative term is n:1

            $table->string('classification')->nullable();

            $table->unsignedInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('oparl_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oparl_bodies');
    }
}
