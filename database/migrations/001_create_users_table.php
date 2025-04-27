<?php


// database/migrations/001_create_users_table.php
namespace Database\Migrations;

use App\Core\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->createTable('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['customer', 'admin'])->default('customer');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->dropTable('users');
    }
}