<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->index();
            $table->string('name')->nullable($value=true);
            $table->string('surname')->nullable($value=true);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('description')->nullable($value=true);
            $table->string('password')->nullable($value=true);
            $table->string('role')->nullable($value=true);
            $table->rememberToken()->nullable($value=true);
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
        Schema::dropIfExists('users');
    }
}
