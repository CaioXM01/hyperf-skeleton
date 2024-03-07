<?php

namespace App\Infraestructure\Database\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property float $value
 * @property int $payer_id
 * @property int $payee_id
 * @property string $refound_reason
 * @property \Carbon\Carbon $notified_at
 * @property \Carbon\Carbon $transferred_at
 * @property \Carbon\Carbon $refound_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Transaction extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'id', 'value', 'payer_id', 'payee_id', 'notified_at', 'transferred_at', 'refound_at', 'refound_reason'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public bool $incrementing = false;
}
