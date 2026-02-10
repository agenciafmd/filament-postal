<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postal', static function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')
                ->default(true)
                ->unsigned()
                ->index();
            $table->string('name');
            $table->string('to');
            $table->string('to_name');
            $table->string('subject');
            $table->text('cc')
                ->nullable();
            $table->text('bcc')
                ->nullable();
            $table->string('slug')
                ->unique()
                ->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
