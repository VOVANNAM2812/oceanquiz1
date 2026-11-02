<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 10)->default('user');
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->string('image_url', 2048)->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->unsignedBigInteger('wiki_page_id')->nullable()->unique();
            $table->string('license')->nullable();
            $table->timestamps();
        });
        Schema::create('question_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 15)->default('draft')->index();
            $table->string('source', 15)->default('manual');
            $table->timestamps();
        });
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_set_id')->constrained()->restrictOnDelete();
            $table->text('content');
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('position');
            $table->timestamps();
            $table->unique(['question_set_id', 'position']);
        });
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->text('content');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('position');
            $table->timestamps();
            $table->unique(['question_id', 'position']);
        });
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('question_set_id')->constrained()->restrictOnDelete();
            $table->string('status', 15)->default('in_progress');
            $table->timestamp('started_at', 6);
            $table->timestamp('finished_at', 6)->nullable();
            $table->unsignedSmallInteger('total_score')->default(0);
            $table->unsignedTinyInteger('correct_count')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'status', 'finished_at']);
            $table->index(['question_set_id', 'status', 'total_score', 'finished_at'], 'attempts_ranking_index');
        });
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->restrictOnDelete();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->foreignId('option_id')->nullable()->constrained('question_options')->restrictOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('score')->default(0);
            $table->timestamps();
            $table->unique(['attempt_id', 'question_id']);
        });
        Schema::create('ai_generation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('question_set_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 15)->default('pending');
            $table->string('model');
            $table->text('error_message')->nullable();
            $table->longText('response_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generation_logs');
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('attempts');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_sets');
        Schema::dropIfExists('articles');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
    }
};
