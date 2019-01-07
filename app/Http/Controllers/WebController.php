<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\PostHistory;

class WebController extends Controller
{
    public function reverse($text)
    {
        $text = str_replace('<br />', '\n', $text);
        $text = str_replace('<br />', '\n', $text);

        $end_block_tags = [
            '</p>',
        ];

        if(preg_match_all('/<[^>]*>/', $text, $matches)){
            foreach ($matches[0] as $tag_html){
                if(!preg_match('/<\s*img/', $tag_html)
                    && !preg_match('/<\s*table/', $tag_html)
                    && !preg_match('/<\/\s*table/', $tag_html)){

                    if(in_array($tag_html, $end_block_tags)) $text = str_replace($tag_html, '\n', $text);
                    else $text = str_replace($tag_html, '', $text);
                }
            }
        }

        $text = str_replace('&nbsp;\n', '\n', $text);

        $text = str_replace('http://dev.data.giaingay.io/TestProject/public/media/', 'media/', $text);
        return $text;
    }
    
    public function removeEndl($text){
        if (preg_match_all('/<p>(&nbsp;)*<\/p>\n( )*(\n)*( )*/', $text, $matches)) {
            foreach ($matches[0] as $space_text) {
                $text = str_ireplace($space_text, '', $text);
            }
        }
        return $text;
    }

    public function endlToBr($text)
    {
        $text = str_replace('\nolimits', '\zolimits', $text);
        $text = str_replace('\neq', '\zeq', $text);
        $text = str_replace('\ne', '\ze', $text);
        $text = str_replace('\n', '<br/>', $text);
        $text = str_replace('\zolimits', '\nolimits', $text);
        $text = str_replace('\zeq', '\neq', $text);
        $text = str_replace('\ze', '\ne', $text);

        $text = str_replace('<span class="math-tex">\(', "\(", $text);
        $text = str_replace('\)</span>', "\)", $text);

        $text = str_replace('media/', 'http://dev.data.giaingay.io/TestProject/public/media/', $text);

        $text = str_replace("\(", '<span class="math-tex">\(', $text);
        $text = str_replace("\)", '\)</span>', $text);
        return $text;
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
        if ($post == null) {
            $post = DB::table('all_posts')->where('hoi_dap_id', $postId)->first();
            if ($post == null)
                return view('404');
        }
        $post->de_bai = $this->endlToBr($post->de_bai);
        $post->dap_an = $this->endlToBr($post->dap_an);

        $data['post'] = $post;
        $data['histories'] = PostHistory::where('post_id', $postId)->orderBy('created_at', 'desc')->get()->map(function ($history) {
            $history->de_bai = $this->endlToBr(json_decode($history->content)->de_bai);
            $history->dap_an = $this->endlToBr(json_decode($history->content)->dap_an);
            $history->created = date('H:i d-m-Y', strtotime($history->created_at . ' + 10 minutes'));
            return $history;
        });
        $data['histories_json'] = json_encode($data['histories']);
        return view('welcome', $data);
    }

    public function editPostApi($postId, Request $request)
    {
        $post = Post::find($postId);

        $request->de_bai = $this->reverse($request->de_bai);
        $request->dap_an = $this->reverse($request->dap_an);

        $count = PostHistory::where('post_id', $postId)->count();
        if ($count == 6) {
            $h = PostHistory::where('post_id', $postId)->orderBy('created_at', 'asc')->first();
            $h->delete();
        }
        $history = new PostHistory();
        $history->post_id = $post->id;
        $history->content = json_encode($post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $history->save();

        $post->de_bai = $request->de_bai;
        $post->dap_an = $request->dap_an;

        $post->save();
        return ['message' => 'success'];
    }

    public function test()
    {
        $text = '<p>Tìm hai số u và v trong mỗi trường hợp sau:<br />
        a) <span class="math-tex">\(u + v = 42\)</span>, <span class="math-tex">\(uv = 441\)</span>;<br />
        b) <span class="math-tex">\(u + v = -42\)</span>, <span class="math-tex">\(uv = -400\)</span>;<br />
        c) <span class="math-tex">\(u – v = 5\)</span>, <span class="math-tex">\(uv = 24\)</span>.</p>
        
        <p>&nbsp;</p>
        
        <p>kaskd</p>';
        $post = DB::table('all_posts')->where('id', 3249)->first();
        $text = $post->de_bai;
        $text = str_replace('\n', '<br/>', $text);

        // dd($this->reverse($text));
        dd($this->endlToBr($text));
    }
}
