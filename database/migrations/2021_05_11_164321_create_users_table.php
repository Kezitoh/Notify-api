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
            $table->increments('id');
            $table->unsignedInteger('id_role');
            $table->unsignedInteger('id_group');
            $table->string('user',9)->unique();
            $table->string('name',25);
            $table->string('surname',25);
            $table->string('password');
            $table->string('email', 50)->nullable()->default(null);
            $table->boolean('is_online')->default(false);
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_online')->nullable()->default(null);
            // $table->rememberToken();
            $table->timestamps();
            $table->engine = "InnoDB";
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
