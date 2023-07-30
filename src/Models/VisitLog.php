<?php

namespace SrDev93\VisitLog\Models;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    protected $table = 'visitlogs';

    protected $fillable = [
        'ip',
        'browser',
        'os',
        'user_id',
        'category_id',
        'product_id',
        'country',
        'countryCode',
        'region',
        'regionName',
        'city',
        'zip',
        'timezone',
        'lat',
        'lon',
        'is_banned'
    ];

    /**
     * Mutator that appends in query resultsets as though it is part of db table
     *
     * @var array
     */
    protected $appends = ['last_visit'];

    /**
     * Last Visit Accessor.
     *
     * @return string
     */
    function getLastVisitAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    # global scope that will be applied to all queries
    public function newQuery()
    {
        return parent::newQuery()->orderBy('updated_at', 'DESC');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
