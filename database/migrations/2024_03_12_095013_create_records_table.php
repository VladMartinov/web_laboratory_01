<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->dateTime("date");
            $table->unsignedInteger("course");
            $table->unsignedBigInteger("group_id");
            $table->unsignedBigInteger("discipline_id");

            $table->index("group_id", "records_group_id_idx");
            $table->index("discipline_id", "records_discipline_id_idx");

            $table->foreign("group_id", "records_group_id_fk")->on("groups")->references("id")->onDelete('cascade');
            $table->foreign("discipline_id", "records_discipline_id_fk")->on("disciplines")->references("id")->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
