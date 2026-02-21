<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'audit_date' => 'date',
    ];

    /**
     * Relasi ke AuditResponse
     */
    public function responses()
    {
        return $this->hasMany(AuditResponse::class);
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        $totalQuestions = Question::count();
        $answeredQuestions = $this->responses()->whereNotNull('response')->count();
        
        if ($totalQuestions == 0) {
            return 0;
        }
        
        return round(($answeredQuestions / $totalQuestions) * 100, 2);
    }

    /**
     * Get summary statistics
     */
    public function getSummaryAttribute()
    {
        return [
            'total' => $this->responses()->count(),
            'yes' => $this->responses()->where('response', 'yes')->count(),
            'no' => $this->responses()->where('response', 'no')->count(),
            'na' => $this->responses()->where('response', 'na')->count(),
            'partial' => $this->responses()->where('response', 'partial')->count(),
            'unanswered' => $this->responses()->whereNull('response')->count(),
        ];
    }
}