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
function endlToBr(&$text) {
    $text = str_replace('\nolimits','\zolimits',$text);
    $text = str_replace('\neq','\zeq',$text);
    $text = str_replace('\ne','\ze',$text);
    $text = str_replace('\n','<br/>',$text);
    $text = str_replace('\zolimits','\nolimits',$text);
    $text = str_replace('\zeq','\neq',$text);
    $text = str_replace('\ze','\ne',$text);
}

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
    
    endlToBr($post->de_bai);
    endlToBr($post->dap_an);

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