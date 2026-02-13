<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = [
        'zone_name',
        'areas',
        'cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'areas' => 'array',
            'cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if a given area is covered by this zone
     */
    public function coversArea(string $area): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        $normalizedArea = strtolower(trim($area));
        $zones = array_map('strtolower', array_map('trim', $this->areas ?? []));
        
        foreach ($zones as $zone) {
            if (str_contains($normalizedArea, $zone) || str_contains($zone, $normalizedArea)) {
                return true;
            }
        }
        
        return false;
    }
}
