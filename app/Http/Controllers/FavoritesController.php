<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * 投稿をお気に入り追加するアクション。
     *
     * @param  $id  投稿のid
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
        // 認証済みユーザ（閲覧者）が、 idの投稿をお気に入り追加する
        \Auth::user()->favorite($id); //User.phpで定義したfavoriteメソッドを召喚 id=$micropostIdになる
        // 前のURLへリダイレクトさせる
        return back();
    }
    /**
     * 投稿のお気に入りを削除するアクション。
     *
     * @param  $id  投稿のid
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 認証済みユーザ（閲覧者）が、 idの投稿のお気に入りを外す
        \Auth::user()->unfavorite($id); //User.phpで定義したunfavoriteメソッドを召喚 id=$micropostIdになる
        // 前のURLへリダイレクトさせる
        return back();
    }
}
