<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Db;

use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Modules\Database\Migration;

class Schema extends Migration {

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function create(): void {
        if (!$this->schema()->hasTable('user')) {
            $this->schema()->create('user', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('firstname');
                $table->string('lastname');
                $table->string('email');
                $table->string('password');
                $table->string('phone')->nullable();
                $table->string('mobile')->nullable();
                $table->integer("role_id")->default(1);
                $table->integer('valid')->default(0);
                $table->integer('ban')->default(0);
                $table->integer('newsletter')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->foreign('role_id')->references('id')->on('role');
            });
        }

        if (!$this->schema()->hasTable('address_type')) {
            $this->schema()->create('address_type', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('type')->index('address_type');
                $table->string('title');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });
        }

        if (!$this->schema()->hasTable('address')) {
            $this->schema()->create('address', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->string('firstname');
                $table->string('lastname');
                $table->string('company');
                $table->string('street');
                $table->string('number');
                $table->string('zip');
                $table->string('city');
                $table->string('country');
                $table->string('address_type');
                $table->integer('active')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->foreign('user_id')->references('id')->on('user');
                $table->foreign('address_type')->references('type')->on('address_type');
            });
        }

        if (!$this->schema()->hasTable('role')) {
            $this->schema()->create('role', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('name');
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('active')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });
        }

        if (!$this->schema()->hasTable('permission_group')) {
            $this->schema()->create('permission_group', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('name');
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('active')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });
        }

        if (!$this->schema()->hasTable('permission')) {
            $this->schema()->create('permission', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('permission_group_id')->unsigned();
                $table->string('name');
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('active')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->foreign('permission_group_id')->references('id')->on('permission_group');
            });
        }

        if (!$this->schema()->hasTable('role_permission')) {
            $this->schema()->create('role_permission', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('role_id')->unsigned();
                $table->json('permissions');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->foreign('role_id')->references('id')->on('role');
            });
        }

        if (!$this->schema()->hasTable('user_role')) {
            $this->schema()->create('user_role', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('role_id')->unsigned();
                $table->integer('active')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->foreign('user_id')->references('id')->on('user');
                $table->foreign('role_id')->references('id')->on('role');
            });
        }

        if (!$this->schema()->hasTable('user_permission')) {
            $this->schema()->create('user_permission', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->json('permissions');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->foreign('user_id')->references('id')->on('user');
            });
        }
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function delete(): void {
        if ($this->schema()->hasTable('user')) {
            $this->schema()->drop('user');
        }
    }
}
