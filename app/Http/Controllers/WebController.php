<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\PostHistory;
use Carbon\Carbon;

class WebController extends Controller
{
    public function reverse($text)
    {
        $text = str_replace('<br />', '\n', $text);
        $text = str_replace('<br />', '\n', $text);

        // $end_block_tags = [
        //     '</p>',
        // ];

        // if(preg_match_all('/<[^>]*>/', $text, $matches)){
        //     foreach ($matches[0] as $tag_html){
        //         if(!preg_match('/<\s*img/', $tag_html)
        //             && !preg_match('/<\s*table/', $tag_html)
        //             && !preg_match('/<\/\s*table/', $tag_html)){

        //             if(in_array($tag_html, $end_block_tags)) $text = str_replace($tag_html, '\n', $text);
        //             else $text = str_replace($tag_html, '', $text);
        //         }
        //     }
        // }

        $text = str_replace('< class="math-tex">\(', "\(", $text);
        $text = str_replace('\)span</span>', "\)", $text);

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

    public function brToEndlLatex($text) {
        $ok = 0;
        $ntext = '';
        for($i=0; $i<strlen($text); $i++){
            if($ok == 1 && $text[$i] == '<' && $text[$i+1] == 'b' && $text[$i+2] == 'r' && $text[$i+3] == '/' && $text[$i+4] == '>'){
                $ntext = $ntext . '\\\\';
                $i+=4;
                continue;
            }
            if($text[$i] == '\\' && $text[$i+1] == '(')
                $ok = 1;
            if($text[$i] == '\\' && $text[$i+1] == ')')
                $ok = 0;
            $ntext .= $text[$i];
        }
        return $ntext;
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

        

        $text = str_replace('media/', 'http://dev.data.giaingay.io/TestProject/public/media/', $text);

        $text = str_replace("\(", '<span class="math-tex">\(', $text);
        $text = str_replace("\)", '\)</span>', $text);
        $text = $this->brToEndlLatex($text);
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

    public function rawHistory($postId, Request $request) {
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
            $history->de_bai = json_decode($history->content)->de_bai;
            $history->dap_an = json_decode($history->content)->dap_an;
            $history->created = date('H:i d-m-Y', strtotime($history->created_at . ' + 10 minutes'));
            return $history;
        });
        $data['histories_json'] = json_encode($data['histories']);
        return view('raw', $data);
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
        // $history->de_bai = str_replace('\r', '', $post->de_bai);
        // $history->dap_an = str_replace('\r', '', $post->dap_an);
        $history->de_bai = $post->de_bai;
        $history->dap_an = $post->dap_an;
        $history->content = json_encode($post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $history->save();

        $post->de_bai = $request->de_bai;
        $post->dap_an = $request->dap_an;
        $post->updated_at = date('Y-m-d H:i:s', strtotime(Carbon::now() . '+ 10 minutes'));

        $post->save();
        return ['message' => 'success'];
    }

    public function test()
    {
        $post = DB::table('all_posts')->where('id', 96)->first();
        $text = $this->endlToBr($post->dap_an);
        $ok = 0;
        $ntext = '';
        for($i=0; $i<strlen($text); $i++){
            if($ok == 1 && $text[$i] == '<' && $text[$i+1] == 'b' && $text[$i+2] == 'r' && $text[$i+3] == '/' && $text[$i+4] == '>'){
                $ntext = $ntext . '\\\\';
                $i+=4;
                continue;
            }
            if($text[$i] == '\\' && $text[$i+1] == '(')
                $ok = 1;
            if($text[$i] == '\\' && $text[$i+1] == ')')
                $ok = 0;
            $ntext .= $text[$i];
        }
        
                // echo $i . ' ' . $text[$i+1] . '<br/>';
        
        
            // echo $text[$i];
        dd($ntext);

    }
}
