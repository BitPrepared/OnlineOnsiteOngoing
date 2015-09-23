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

class Annotation extends Model {

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
    protected $table = 'annotations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    // Assegnamento massivo
    protected $fillable = ['author', 'source', 'text','textHtml','hashtags'];

    public function setHashtagsAttribute(array $value){
        return $this->attributes['hashtags'] = join(',', $value);
    }

    public function getHashtagsAttribute($value){
        return explode(',',$value);
    }

    public function setSourceAttribute($value){
        return $this->attributes['source'] = Source::label($value);
    }

    public function getSourceAttribute($value){
        return Source::fromLabel($value);
    }

    // ???
    protected $hidden   = ['source','author','created_at','updated_at','deleted_at'];

    /**
     * Get the comments for the blog post.
     */
    public function attachments()
    {
        return $this->hasMany('Indaba\Dashboard\Attachment');
    }

    /**
     * The accessors to append to the model's array form. (quando faccio toJson viene aggiunto)
     * @see: http://laravel.com/docs/5.1/eloquent-serialization
     *
     * @var array
     */
    protected $appends = ['sourceLabel', 'deleted', 'created'];

    public function getCreatedAttribute(){
        $carbon = new Carbon($this->attributes['created_at'],'Europe/Rome');
        return $carbon->__toString();
    }

    public function getDeletedAttribute(){
        return $this->attributes['deleted_at'] == null ? false : true;
    }

    public function getSourceLabelAttribute()
    {
        return $this->attributes['source'];
    }

}