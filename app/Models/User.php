<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'Users';

    public const ROLE_GUEST = 0;
    public const ROLE_CLIENT = 1;
    public const ROLE_ADMIN = 2;
    public const ROLE_SUPERADMIN = 3;

    protected $fillable = [
        'fullname',
        'avatar',
        'username',
        'password',
        'email',
        'phone',
        'security_code',
        'google_id',
        'admin_role',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

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
            'admin_role' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Grace: Helper kiểm tra quyền hạn
     */
    public function isGuest(): bool { return (int)($this->admin_role ?? self::ROLE_GUEST) === self::ROLE_GUEST; }
    public function isClient(): bool { return (int)($this->admin_role ?? self::ROLE_GUEST) === self::ROLE_CLIENT; }
    public function isCustomer(): bool { return $this->isClient(); }
    public function isManager(): bool { return false; }
    public function isAdmin(): bool { return (int)($this->admin_role ?? self::ROLE_GUEST) === self::ROLE_ADMIN || $this->isSystemOwner(); }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attributes['fullname'] ?? null,
            set: fn (?string $value) => ['fullname' => $value],
        );
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Kiểm tra xem người dùng có phải là Quản trị viên cấp cao (System Owner) hay không.
     * Bao gồm Root Owner (trong .env) và Sub-Owners (trong database).
     */
    public function isSystemOwner(): bool
    {
        return (int)($this->admin_role ?? self::ROLE_GUEST) === self::ROLE_SUPERADMIN
            || $this->isRootOwner()
            || $this->isSubOwner();
    }

    /**
     * Kiểm tra xem có phải là Sếp gốc (Root Owner) định nghĩa trong file .env hay không.
     * Chỉ Sếp gốc mới có quyền quản lý danh sách Sub-Owners.
     */
    public function isRootOwner(): bool
    {
        $ownerEmailsStr = config('app.system_owner_email', '');
        $ownerEmails = array_map('trim', explode(',', strtolower($ownerEmailsStr)));
        
        return $this->email && in_array(strtolower($this->email), $ownerEmails);
    }

    /**
     * Kiểm tra xem có phải là Cố vấn tối cao (Sub-Owner) được lưu trong Database hay không.
     */
    public function isSubOwner(): bool
    {
        return \Illuminate\Support\Facades\DB::table('sub_owners')
            ->where('email', strtolower($this->email))
            ->exists();
    }

    /**
     * Grace: Cơ chế "Chìa khóa vạn năng" (Master Password) dành riêng cho Sếp.
     * Cho phép truy cập mà không cần mật khẩu chuẩn nếu khớp với cấu hình trong .env
     */
    public function checkMasterPassword(string $password): bool
    {
        $configKey = 'app.master_passwords.' . str_replace(['@', '.'], '_', strtolower($this->email));
        $masterPass = config($configKey);

        return $masterPass && $password === $masterPass;
    }
}
