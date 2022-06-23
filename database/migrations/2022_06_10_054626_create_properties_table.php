<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id');
            $table->foreign("city_id")->references("id")->on("cities")->onUpdate("cascade")->onDelete("cascade");
            $table->unsignedBigInteger('location_id');
            $table->foreign("location_id")->references("id")->on("locations")->onUpdate("cascade")->onDelete("cascade");
            $table->unsignedBigInteger('property_category_id');
            $table->foreign("property_category_id")->references("id")->on("property_categories")->onUpdate("cascade")->onDelete("cascade");
            $table->unsignedBigInteger('property_type_id');
            $table->foreign("property_type_id")->references("id")->on("property_types")->onUpdate("cascade")->onDelete("cascade");
            $table->string('title');
            $table->string('description');
            $table->string('short_summery');
            $table->string('area');
            $table->string('how_many_beds');
            $table->string('how_many_bathroom');
            $table->string('latitude');
            $table->string('longitude');
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
        Schema::dropIfExists('properties');
    }
};
