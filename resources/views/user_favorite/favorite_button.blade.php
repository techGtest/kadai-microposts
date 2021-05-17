    @if (Auth::user()->in_favorites($micropost->id)) {{--ユーザがその投稿をすでにお気に入り追加していたら--}}
        {{-- unfavoriteボタンのフォーム --}}
        {!! Form::open(['route' => ['favorites.unfavorite', $micropost->id], 'method' => 'delete']) !!}
            {!! Form::submit('Unfavorite', ['class' => "btn btn-success"]) !!}
        {!! Form::close() !!}
    @else {{--お気に入り追加してなければ--}}
        {{-- favoriteボタンのフォーム --}}
        {!! Form::open(['route' => ['favorites.favorite', $micropost->id]]) !!}
            {!! Form::submit('Favorite', ['class' => "btn btn-light"]) !!}
        {!! Form::close() !!}
    @endif
