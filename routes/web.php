<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\PostHistory;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function (Request $request) {
    $post = DB::table('all_posts')->first();
    if($post == null)
        return view('404');
    return redirect('/' . 'post' . '/' . $post->id . '/edit');
});

Route::get('/post/{postId}/edit', function ($postId, Request $request) {
    $post = DB::table('all_posts')->where('id', $postId)->first();
    if($post == null)
        return view('404');
    $data['post'] = $post;

    return view('welcome', $data);
});

Route::put('/api/post/{postId}', function ($postId, Request $request) {
    $post = Post::find($postId);
    $post->de_bai = $request->de_bai;
    $post->dap_an = $request->dap_an;
    $post->save();
    $history = new PostHistory();
    $history->post_id = $post->id;
    $history->content = json_encode($post);
    $history->save();
    return ['message' => 'success'];
});