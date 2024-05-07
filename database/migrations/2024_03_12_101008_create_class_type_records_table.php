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
        Schema::create('class_type_records', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("class_type_id");
            $table->unsignedBigInteger("record_id");

            $table->unsignedBigInteger("hours_spend");

            $table->index("class_type_id", "class_type_record_class_type_idx");
            $table->index("record_id", "class_type_record_record_idx");

            $table->foreign("class_type_id", "class_type_record_class_type_fk")->on("class_types")->references("id")->onDelete('cascade');;
            $table->foreign("record_id", "class_type_record_record_fk")->on("records")->references("id")->onDelete('cascade');;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_type_records');
    }
};
