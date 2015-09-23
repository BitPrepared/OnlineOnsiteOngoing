<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 05/02/15 - 22:00
 * 
 */

namespace Indaba\Dashboard;

use \Indaba\Dashboard\Source;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Attachment extends Model {
    
	use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachments';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    // Assegnamento massivo
    protected $fillable = ['annotation_id','source','fileName','filePath'];

    protected $hidden   = ['annotation_id','source','filePath','created_at','updated_at','deleted_at'];

    public function setSourceAttribute($value){
        return $this->attributes['source'] = Source::label($value);
    }

    public function getSourceAttribute($value){
        return Source::fromLabel($value);
    }

    /**
     * Get the post that owns the comment.
     */
    public function annotation()
    {
        return $this->belongsTo('Indaba\Dashboard\Annotation');
    }

}