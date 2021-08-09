<?php
/**
 * MYMO CMS - The Best Laravel CMS
 *
 * @package    juzacmscms/juzacmscms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://github.com/juzacmscms/juzacmscms
 * @license    MIT
 *
 * Created by The Anh.
 * Date: 7/10/2021
 * Time: 3:13 PM
 */

namespace Juzaweb\Cms\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'status'
    ];

    public static function getAllStatus()
    {
        return [
            'active' => trans('juzacms::app.active'),
            'unconfirmed' => trans('juzacms::app.unconfimred'),
            'banned' => trans('juzacms::app.banned'),
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @param Builder $builder
     * @return Builder
     * */
    public function scopeActive($builder)
    {
        return $builder->where('status', '=', 'active');
    }

    public function getAvatar() {
        if ($this->avatar) {
            return image_url($this->avatar);
        }

        return asset('juzacms/styles/images/thumb-default.png');
    }
}