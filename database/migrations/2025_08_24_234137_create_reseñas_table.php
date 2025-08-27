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
        Schema::create('reseñas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('emprendedor_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('calificacion_producto')->unsigned()->comment('1 a 5 estrellas');
            $table->tinyInteger('calificacion_servicio')->unsigned()->nullable()->comment('1 a 5 estrellas, opcional');
            $table->string('reseña', 200)->nullable();
            $table->boolean('aprobada')->default(false)->comment('Si la reseña está aprobada o visible públicamente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseñas');
    }
};
