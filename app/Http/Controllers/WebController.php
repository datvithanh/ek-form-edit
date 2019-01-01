<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\PostHistory;
class WebController extends Controller
{
    public function endlToBr(&$text)
    {
        $text = str_replace('\nolimits', '\zolimits', $text);
        $text = str_replace('\neq', '\zeq', $text);
        $text = str_replace('\ne', '\ze', $text);
        $text = str_replace('\n', '<br/>', $text);
        $text = str_replace('\zolimits', '\nolimits', $text);
        $text = str_replace('\zeq', '\neq', $text);
        $text = str_replace('\ze', '\ne', $text);
    }

    public function index(Request $request)
    {
        $post = DB::table('all_posts')->first();
        if ($post == null)
            return view('404');
        return redirect('/' . 'post' . '/' . $post->id . '/edit');
    }

    public function editPost($postId, Request $request)
    {
        $post = DB::table('all_posts')->where('id', $postId)->first();
        if ($post == null)
            return view('404');

        $this->endlToBr($post->de_bai);
        $this->endlToBr($post->dap_an);

        $data['post'] = $post;

        return view('welcome', $data);
    }

    public function editPostApi ($postId, Request $request) {
        $post = Post::find($postId);
        $post->de_bai = $request->de_bai;
        $post->dap_an = $request->dap_an;
        $post->save();
        $history = new PostHistory();
        $history->post_id = $post->id;
        $history->content = json_encode($post);
        $history->save();
        return ['message' => 'success'];
    }
}
