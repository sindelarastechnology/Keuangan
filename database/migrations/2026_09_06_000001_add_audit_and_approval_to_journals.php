<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom audit trail ke jurnal_umum
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('change_reason')->nullable();
        });

        // Tambah kolom audit trail ke pembelians
        Schema::table('pembelians', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
        });

        // Tambah kolom audit trail ke penjualans
        Schema::table('penjualans', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
        });

        // Tambah kolom audit trail ke kas_masuk
        Schema::table('kas_masuk', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
        });

        // Tambah kolom audit trail ke kas_keluar
        Schema::table('kas_keluar', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['draft', 'pending_review', 'approved', 'rejected'])->default('approved');
            $table->text('approval_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kas_keluar', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['created_by', 'updated_by', 'approved_by', 'approval_status', 'approval_reason', 'approved_at']);
        });

        Schema::table('kas_masuk', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['created_by', 'updated_by', 'approved_by', 'approval_status', 'approval_reason', 'approved_at']);
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['created_by', 'updated_by', 'approved_by', 'approval_status', 'approval_reason', 'approved_at']);
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['created_by', 'updated_by', 'approved_by', 'approval_status', 'approval_reason', 'approved_at']);
        });

        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['created_by', 'updated_by', 'approved_by', 'approval_status', 'approval_reason', 'approved_at', 'change_reason']);
        });
    }
};
