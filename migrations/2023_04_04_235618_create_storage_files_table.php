<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('storage_files', function (Blueprint $table) {
            $table->uuid('file_id');
            $table->string('path')->unique();
            $table->string('hash');
            $table->json('info');
            $table->bigInteger('size');
            $table->timestamp('deletion_date')->nullable();
            $table->timestamps();
        });


        DB::statement("ALTER TABLE storage_files ADD data LONGBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_files');
    }
};
