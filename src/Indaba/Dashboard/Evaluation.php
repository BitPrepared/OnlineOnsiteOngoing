<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 05/02/15 - 22:00
 * 
 */

namespace Indaba\Dashboard;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model {
    
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
    protected $table = 'evaluations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    // Assegnamento massivo
    protected $fillable = ['annotation_id','sessione','evento','punteggio'];

    protected $hidden   = ['annotation_id','created_at','updated_at','deleted_at'];

    /**
     * Get the post that owns the comment.
     */
    public function annotation()
    {
        return $this->belongsTo('Indaba\Dashboard\Annotation');
    }

}