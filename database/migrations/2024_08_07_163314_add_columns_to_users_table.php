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
        Schema::table('users', function (Blueprint $table) {
            $table->string("last_name");
            $table->date("birth")->nullable();
            $table->enum('gender',['male','female']);
            $table->text("profile_pic")->default('https://static.vecteezy.com/system/resources/thumbnails/009/292/244/small/default-avatar-icon-of-social-media-user-vector.jpg');
            $table->string("country")->nullable();
            $table->string("tel")->nullable();
            $table->string("address")->nullable();
            $table->string("CodePostal")->nullable();
            $table->boolean("status")->default(true);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
