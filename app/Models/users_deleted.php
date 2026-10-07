<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class users_deleted extends Model
{
    use HasFactory;

    protected $table = 'users_deleted';


    protected $fillable = [
        'id', 'pid', 'name', 'mobile_no', 'email', 'email_verified', 'otp_token', 'role_id', 'password', 'passwd', 'imei', 'imei_id', 'fcm_reg_id', 'user_type', 'date_time', 'updated_at', 'created_at', 'profile_image', 'server_key', 'google_id', 'email_verified_at', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'remember_token', 'current_team_id', 'profile_photo_path'
    ];
}
