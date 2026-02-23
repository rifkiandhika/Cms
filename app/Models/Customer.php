<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public static function generateKode()
    {
        $lastCustomer = static::orderBy('kode_customer', 'desc')->first();
        
        if ($lastCustomer) {
            $lastNumber = (int) substr($lastCustomer->kode_customer, 4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return 'CUST' . $newNumber;
    }

    public function detailCustomers()
    {
        return $this->hasMany(DetailCustomer::class, 'customer_id');
    }

    // Relasi ke Purchase Orders (Penjualan)
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_supplier', 'id')
            ->where('tipe_po', 'penjualan');
    }

    // Relasi ke Tagihan (sebagai customer)
    public function tagihans()
    {
        return $this->hasMany(TagihanPo::class, 'id_relasi', 'id')
            ->where('tipe_relasi', 'customer');
    }

    /**
     * Get the tipe customer label.
     *
     * @return string
     */
    public function getTipeCustomerLabelAttribute()
    {
        $labels = [
            'rumah_sakit' => 'Rumah Sakit',
            'klinik' => 'Klinik',
            'laboratorium' => 'Laboratorium',
            'apotek' => 'Apotek',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->tipe_customer] ?? $this->tipe_customer;
    }

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope a query to only include inactive customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'nonaktif');
    }

    /**
     * Scope a query to filter by tipe customer.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $tipe
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe_customer', $tipe);
    }
}