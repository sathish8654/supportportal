<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id', 'name', 'mobile_no', 'email', 'email_verified', 'otp_token', 'role_id', 'only_selfview', 'password', 'passwd', 'imei', 'imei_id', 'fcm_reg_id', 'user_type', 'date_time', 'updated_at', 'created_at', 'profile_image', 'server_key', 'google_id', 'email_verified_at', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'remember_token', 'current_team_id', 'profile_photo_path', 'created_by', 'updated_by','status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class, 'user_id', 'id');
    }

    public function hasMenuPermission($menu, $submenu = null, $action = null)
    {
        $permission = $this->permissions->first();
        if (!$permission) {
            return false;
        }

        // If "Allow All Menu" is enabled, access is allowed
        if ($permission->all_select == 1) {
            return true;
        }

        // If "Allow All Menu" is disabled, read from the JSON column allowed_menus
        $allowedMenus = $permission->allowed_menus;
        if (is_string($allowedMenus)) {
            $allowedMenus = json_decode($allowedMenus, true);
        }

        if (empty($allowedMenus) || !isset($allowedMenus['menus'])) {
            return false;
        }

        $menus = $allowedMenus['menus'];

        // Check main menu
        if (!isset($menus[$menu]) || !$menus[$menu]['enabled']) {
            return false;
        }

        // If only checking main menu
        if (is_null($submenu)) {
            return true;
        }

        // Check submenu
        $submenus = $menus[$menu]['submenus'] ?? [];
        if (!isset($submenus[$submenu]) || !$submenus[$submenu]['enabled']) {
            return false;
        }

        // If only checking submenu
        if (is_null($action)) {
            return true;
        }

        // Check action permission (e.g. view, create, edit, delete)
        $actions = $submenus[$submenu]['actions'] ?? [];
        return !empty($actions[$action]);
    }

    public function hasRole($role)
    {
        // Return true to allow seeing all role-specific UI features in this UI prototype phase
        return true;
    }
}

