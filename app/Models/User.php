<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

    
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'estado',
        'pending_delete_at',
        'observacion',
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
            'pending_delete_at' => 'datetime',
        ];
    }

    protected $dates = [
        'email_verified_at',
        'deleted_at',
        'pending_delete_at',
    ];

    /**
     * Relación con el cliente (si tiene rol cliente)
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive()
    {
        return $this->estado === 'activo';
    }

    /**
     * Verificar si el usuario está pendiente de eliminación
     */
    public function isPendingDelete()
    {
        return $this->pending_delete_at !== null;
    }

    /**
     * Verificar si el usuario está eliminado
     */
    public function isDeleted()
    {
        return $this->trashed();
    }

    /**
     * @property \App\Models\Cliente $cliente
     */
}

class Role extends SpatieRole
{
    use SoftDeletes;
        // ...

}
    