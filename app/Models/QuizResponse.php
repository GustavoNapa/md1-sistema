<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResponse extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'quiz_mysql';

    protected $fillable = [
        'email',
        'name',
        'answers',
        'summary',
        'response_time_minutes',
        'report_html',
        'report_filename',
    ];

    protected $casts = [
        'answers' => 'array',
        'summary' => 'array',
        'response_time_minutes' => 'integer',
    ];

    /**
     * Get the dominant profile from summary
     */
    public function getDominantProfileAttribute()
    {
        if (!$this->summary || !isset($this->summary['ordered']) || empty($this->summary['ordered'])) {
            return null;
        }

        return $this->summary['ordered'][0]['profile'] ?? null;
    }

    /**
     * Get formatted percentages
     */
    public function getPercentagesAttribute()
    {
        if (!$this->summary || !isset($this->summary['perc'])) {
            return [];
        }

        return $this->summary['perc'];
    }
    
    /**
     * Get formatted counts
     */
    public function getCountsAttribute()
    {
        if (!$this->summary || !isset($this->summary['counts'])) {
            return [];
        }

        return $this->summary['counts'];
    }
    
    /**
     * Get ordered profiles
     */
    public function getOrderedProfilesAttribute()
    {
        if (!$this->summary || !isset($this->summary['ordered'])) {
            return [];
        }

        return $this->summary['ordered'];
    }
}
