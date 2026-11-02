<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('type', 20);
            $table->string('regency', 20);
            $table->boolean('is_demo')->default(false);
            $table->timestamp('demo_expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('role', 10)->default('pemilik')->after('password');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('category', 20)->index();
            $table->string('base_unit', 10);
            $table->decimal('kg_per_base', 10, 4)->default(1);
            $table->json('conversions')->nullable();
            $table->timestamps();
        });

        Schema::create('product_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('alias', 100);
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('source', 10);
            $table->timestamps();
            $table->unique(['alias', 'business_id']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_business_id')->nullable()->constrained('businesses')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('regency', 20)->nullable();
            $table->string('phone', 40)->nullable()->unique();
            $table->string('verification', 15)->default('pernyataan');
            $table->boolean('is_demo')->default(false);
            $table->boolean('active')->default(true);
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->index(['active', 'regency']);
        });

        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('origin', 10);
            $table->decimal('capacity_per_week', 12, 2)->nullable();
            $table->string('capacity_unit', 20)->nullable();
            $table->unsignedInteger('price_per_unit')->nullable();
            $table->string('price_unit', 20)->nullable();
            $table->timestamps();
            $table->unique(['supplier_id', 'product_id']);
            $table->index(['product_id', 'origin']);
        });

        Schema::create('supplier_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('alias', 120);
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['business_id', 'alias']);
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('supplier_name_raw', 120)->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('status', 12)->default('diproses');
            $table->string('image_path', 200)->nullable();
            $table->decimal('ai_confidence', 4, 3)->nullable();
            $table->boolean('ai_used')->default(false);
            $table->string('note', 255)->nullable();
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status', 'receipt_date']);
        });

        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name_raw', 100);
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 20)->nullable();
            $table->decimal('quantity', 12, 3);
            $table->string('unit', 20);
            $table->decimal('quantity_kg', 14, 3)->nullable();
            $table->boolean('kg_estimated')->default(false);
            $table->decimal('ai_kg_per_unit', 10, 4)->nullable();
            $table->unsignedBigInteger('total_price')->default(0);
            $table->string('origin', 16)->default('tidak_diketahui');
            $table->string('evidence', 16)->default('tidak_diketahui');
            $table->decimal('confidence', 4, 3)->nullable();
            $table->boolean('needs_review')->default(false);
            $table->timestamps();
            $table->index(['business_id', 'category']);
        });

        Schema::create('quota_rules', function (Blueprint $table) {
            $table->id();
            $table->string('group', 20)->unique();
            $table->unsignedTinyInteger('min_percent');
            $table->string('source', 255);
            $table->timestamps();
        });

        Schema::create('supply_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_per_week', 12, 2);
            $table->string('unit', 20);
            $table->unsignedInteger('offered_price')->nullable();
            $table->string('status', 12)->default('menunggu');
            $table->text('message');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->index(['supplier_id', 'status']);
        });

        Schema::create('wa_messages', function (Blueprint $table) {
            $table->id();
            $table->string('direction', 3);
            $table->string('phone', 40)->index();
            $table->text('body');
            $table->string('channel', 10);
            $table->string('status', 10)->default('logged');
            $table->string('external_id', 100)->nullable()->unique();
            $table->string('error', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('wa_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 40)->unique();
            $table->string('state', 20);
            $table->json('payload')->nullable();
            $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('ai_calls', function (Blueprint $table) {
            $table->id();
            $table->string('purpose', 30);
            $table->string('driver', 20);
            $table->string('model', 60);
            $table->string('status', 12);
            $table->unsignedInteger('latency_ms')->default(0);
            $table->string('error', 255)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->index(['purpose', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_calls');
        Schema::dropIfExists('wa_conversations');
        Schema::dropIfExists('wa_messages');
        Schema::dropIfExists('supply_requests');
        Schema::dropIfExists('quota_rules');
        Schema::dropIfExists('receipt_items');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('supplier_aliases');
        Schema::dropIfExists('supplier_products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('product_aliases');
        Schema::dropIfExists('products');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_id');
            $table->dropColumn('role');
        });
        Schema::dropIfExists('businesses');
    }
};
