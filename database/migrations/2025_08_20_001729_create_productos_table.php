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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->longText('descripcion');
            $table->decimal('precio', 10, 2);
            $table->boolean('destacado')->default(false);
            $table->boolean('publico')->default(false);
            $table->integer('stock')->default(0);
            $table->timestamp('fecha_publicacion', 3)->useCurrent();
            $table->foreignId('emprendedor_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('producto_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('ruta');
            $table->timestamps();
        });
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });
        Schema::create('categoria_producto', function (Blueprint $table) {
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->primary(
                ['producto_id', 'categoria_id']
            );
        });
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->decimal('descuento', 5, 2)->comment('Porcentaje de descuento, ejemplo 10 = 10%');
            $table->integer('limite_usos')->default(1);
            $table->integer('usos_realizados')->default(0);
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('cupone_producto', function (Blueprint $table) {
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('cupon_id')->constrained('cupones')->onDelete('cascade');
            $table->primary(['producto_id', 'cupon_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupones');
        Schema::dropIfExists('categoria_producto');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('producto_imagenes');
        Schema::dropIfExists('productos');
    }
};
