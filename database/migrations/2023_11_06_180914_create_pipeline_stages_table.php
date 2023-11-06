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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('position');
            $table->boolean('is_default');
            $table->timestamps();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\PipelineStage::class)->after('last_name')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\PipelineStage::class);
        });
    }
};
