<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipa_members', function (Blueprint $table) {
            $table->id();
            $table->string('member_number')->unique();
            $table->string('salutation')->nullable();
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('mobile_phone')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('membership_status')->nullable();
            $table->string('member_level')->nullable();
            $table->string('member_level_short')->nullable();
            $table->text('member_tags')->nullable();
            $table->date('joined_at')->nullable();
            $table->date('level_valid_until')->nullable();
            $table->string('membership_years')->nullable();
            $table->string('current_level_years')->nullable();
            $table->string('job_title_zh')->nullable();
            $table->string('job_title_en')->nullable();
            $table->string('company_name_zh')->nullable();
            $table->string('company_name_en')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('wechat')->nullable();
            $table->string('other_social_platform')->nullable();
            $table->string('other_social_account')->nullable();
            $table->string('alternate_email')->nullable();
            $table->string('partner_level_1')->nullable();
            $table->string('partner_level_2')->nullable();
            $table->string('region')->nullable();
            $table->string('review_discount')->nullable();
            $table->string('special_approval')->nullable();
            $table->string('assistant_contact')->nullable();
            $table->string('assistant_phone')->nullable();
            $table->string('referrer_mobile')->nullable();
            $table->string('referrer_name')->nullable();
            $table->string('referrer_member_number')->nullable();
            $table->string('certificate_name')->nullable();
            $table->string('certificate_printed')->nullable();
            $table->date('certificate_issued_at')->nullable();
            $table->string('exam_status')->nullable();
            $table->string('ifrs')->nullable();
            $table->string('ethics')->nullable();
            $table->string('bda')->nullable();
            $table->string('cpd_credits')->nullable();
            $table->date('membership_restored_at')->nullable();
            $table->date('membership_upgraded_at')->nullable();
            $table->date('membership_transferred_at')->nullable();
            $table->text('leave_reason')->nullable();
            $table->date('leave_expires_at')->nullable();
            $table->text('termination_or_leave_reason')->nullable();
            $table->date('termination_or_leave_at')->nullable();
            $table->date('membership_application_at')->nullable();
            $table->string('membership_application_type')->nullable();
            $table->string('membership_application_status')->nullable();
            $table->date('membership_application_status_at')->nullable();
            $table->date('membership_application_review_at')->nullable();
            $table->string('membership_application_reviewer')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipa_members');
    }
};
