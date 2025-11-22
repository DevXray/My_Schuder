<?php
// ============================================
// app/Models/Role.php
// ============================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Helper methods
    public static function admin()
    {
        return static::where('name', 'admin')->first();
    }

    public static function dosen()
    {
        return static::where('name', 'dosen')->first();
    }

    public static function mahasiswa()
    {
        return static::where('name', 'mahasiswa')->first();
    }
}
