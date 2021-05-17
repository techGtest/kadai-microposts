<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /**
     * このユーザに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers','favorites']);
    }
    /**
     * このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

    /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    /**
     * このユーザがお気に入り追加している投稿。（Micropostモデルとの関係を定義)
     */
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'microposts_id')->withTimestamps();
    }
    /**
     * 指定された$micropostIdの投稿をこのユーザがすでにお気に入り追加しているかどうか調べる。すでにお気に入り追加しているならtrueを返す。
     *
     * @param  int  $micropostId
     * @return bool
     */
    public function in_favorites($micropostId)
    {
        // お気に入り投稿の中に $micropostIdのものが存在するか
        return $this->favorites()->where('microposts_id', $micropostId)->exists();
    }

    /**
     * $micropostIdで指定された投稿をお気に入り追加する。
     *
     * @param  int  $micropostId
     * @return bool
     */
    public function favorite($micropostId)
    {
        // すでにお気に入り追加しているかどうか
        $exist = $this->in_favorites($micropostId);

        if ($exist) {
            // すでにお気に入り追加している場合
            return false; //何もしない
        } else {
            // そうでなければお気に入り追加する
            $this->favorites()->attach($micropostId); //自分自身がお気に入り追加している投稿に$micropostIdの投稿を追加する
            return true;
        }
    }
    /**
     * $micropostIdで指定された投稿のお気に入りをはずず。
     *
     * @param  int  $micropostId
     * @return bool
     */
    public function unfavorite($micropostId)
    {
        // すでにお気に入り追加しているかどうか
        $exist = $this->in_favorites($micropostId);
        
        if ($exist) {
            // すでにお気に入り追加している場合
            $this->favorites()->detach($micropostId);//自分自身がお気に入り追加している投稿から$micropostIdの投稿を削除する
            return true;
        } else {
            // そうでなければ何もしない
            return false;
        }
    }

}
    
    


