<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintAnalysis extends Model
{
    use HasFactory;

    protected $table = 'complaint_analysis';
    protected $primaryKey = 'AnalysisID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $guarded = [];
}
