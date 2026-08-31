<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MALLIESIMERKKI: mahdollistaa käyttäjän kuulumisen useampaan yritykseen
 * käyttäjän oman "kotiyrityksen" (users.company_id) lisäksi. Tämä taulu
 * on pohjan yleinen mekanismi — sopii mille tahansa toimialalle.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};