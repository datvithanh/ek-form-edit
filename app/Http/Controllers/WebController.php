<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\PostHistory;
use Carbon\Carbon;

class WebController extends Controller
{
    public function standard($content){
        $content = str_replace("\r", ' ', $content);
        $content = str_replace("\t", ' ', $content);
        $content = str_replace(' ', ' ', $content);
        // $content = str_replace('', ' ', $content);
//        $content = str_replace("\n", ' ', $content);
        $content = str_replace("\xc2\xa0", ' ', $content);
        $content = str_replace("&#13;", ' ', $content);

        //loại text thừa : đề bài, câu hỏi, nhãn, bình luận

        $remove_subject_texts = [
            'loigiaihay.com',
            'loigiaihay..com',
            'loigiaihay',
            'vietjack.com',
            'vietjack',
            'đề bài',
            'Đề bài',
            'câu hỏi',
            'bình luận',
        ];

        $content = str_ireplace($remove_subject_texts, '', $content);


        // loại text thừa lời giải
        $remove_texts = [
            'Giải',
            'Gỉải',
            'Giải',
            'Lời giải chi tiết',
            'Lời giải',
            'Hướng dẫn giải',
            'GỢI Ý LÀM BÀI',
            'Trả lời',
            'Phương pháp giải - Xem chi tiết',
            'Hướng dẫn giải',
            'Hướng dẫn',
            'Đáp án chi tiết',
            'Đáp án',
            'BÀI THAM KHẢO',
            'Bài Tham Khảo',
            'Hướng dẫn trả lời'
        ];

        foreach ($remove_texts as $remove_text){
            $content = preg_replace('/(<strong[^>]*>|^)\s*'.$remove_text.'\s*:?\s*<\/strong>/ui', '', $content);
            $content = preg_replace('/(<b[^>]*>|^)\s*'.$remove_text.'\s*:?\s*<\/b>/ui', '', $content);
        }

        //loại javascript
        $content = preg_replace('/<script[^>]*>.*?<\/script>/', '', $content);

        //chuyển số mũ thành dạng latext

        if(preg_match_all('/(?<=\s)([^\s>]+)\s*<sup\>([^<]+)<\/sup>/', $content, $matches)){
            foreach ($matches[0] as $k => $value){
                $co_so = $matches[1][$k];
                $he_so = $matches[2][$k];

                if($this->isValidSomu($co_so) && $this->isValidSomu($he_so)){
                    $latex = "\($co_so^$he_so\)";
                    $content = str_replace($value, $latex, $content);
                }
            }
        }

        if(preg_match_all('/(?<=\s)([^\s>]+)\s*<sub\>([^<]+)<\/sub>/', $content, $matches)){
            foreach ($matches[0] as $k => $value){
                $co_so = $matches[1][$k];
                $he_so = $matches[2][$k];

                if($this->isValidSomu($co_so) && $this->isValidSomu($he_so)){
                    $latex = "\($co_so"."_$he_so\)";
                    $content = str_replace($value, $latex, $content);
                }
            }
        }

        //loại text thừa đầu câu
        $remove_texts2 = [
            'Lời giải chi tiết',
            'Lời giải',
            'Hướng dẫn giải',
            'GỢI Ý LÀM BÀI',
            'Trả lời',
            'Phương pháp giải - Xem chi tiết',
            'Hướng dẫn giải',
            'Hướng dẫn',
            'Đáp án chi tiết',
            'Đáp án',
            'BÀI THAM KHẢO',
            'Bài Tham Khảo',
            'Hướng dẫn trả lời'
        ];

        foreach ($remove_texts2 as $remove_text){
            $content = preg_replace('/^\s*'.$remove_text.'\s*:?\s*/ui', '', $content);
        }

        $content = preg_replace('/^\s*giải\s*:?\s*\\\n\s*/ui', '', $content);

        $content = htmlspecialchars_decode($content);
        $content = preg_replace('/(\s*\\\n\s*){2,}/', ' \n ', $content);
        $content = preg_replace("/\s{2,}/", ' ', $content);
        $content = str_ireplace("&nbsp;", ' ', $content);

        //loại \n đầu câu
        while (true){
            $content = trim($content);

            if(mb_strpos($content, '\n') === 0) $content = mb_substr($content, 2);
            else break;
        }

        //loại \n cuối câu
        while (true){
            $content = trim($content);

            if(mb_strrpos($content, '\n') === mb_strlen($content) - 2) $content = mb_substr($content, 0, mb_strlen($content) - 2);
            else break;
        }

        $content = trim($content);

        return $content;
    }

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

        //strip span tag
        // $text = str_replace('< class="math-tex">\(', "\(", $text);
        // $text = str_replace('\)span</span>', "\)", $text);

        //parse html table back to markdown
        // if (preg_match_all('/<table>(.|\||\s)*?<\/table>/', $text, $matches)) {
        //     foreach ($matches[0] as $table_html) {
        //         $table_markdown = $this->htmlTableToMarkdown($table_html);
        //         $text = str_replace($table_html, $table_markdown, $text);
        //     }
        // }

        $text = str_replace('&nbsp;\n', '\n', $text);

        $text = str_replace('http://dev.data.giaingay.io/TestProject/public/media/', 'media/', $text);
        return $text;
    }

    public function brToEndlLatex($text)
    {
        $ok = 0;
        $ntext = '';
        for ($i = 0; $i < strlen($text); $i++) {
            if ($ok == 1 && $text[$i] == '<' && $text[$i + 1] == 'b' && $text[$i + 2] == 'r' && $text[$i + 3] == '/' && $text[$i + 4] == '>') {
                $ntext = $ntext . '\\\\';
                $i += 4;
                continue;
            }
            if ($text[$i] == '\\' && $text[$i + 1] == '(')
                $ok = 1;
            if ($text[$i] == '\\' && $text[$i + 1] == ')')
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

        // parse markdown table to html
        // $parser = new \cebe\markdown\MarkdownExtra();
        // if (preg_match_all('/<table>(.|\||\s)*?<\/table>/', $text, $matches)) {
        //     foreach ($matches[0] as $table_html) {
        //         $html = $table_html;
        //         $html = str_replace(['<table>', '</table>'], '', $html);
        //         // preserve latex form after parse
        //         $html = $this->escapeSlash($html);
        //         $html = $parser->parse($html);

        //         if (preg_match_all('/(\[\d+\]):\s*([^\[\<]+)/', $html, $matches)) {
        //             foreach ($matches[0] as $j => $markdown_link) {
        //                 $number = '![]' . $matches[1][$j];
        //                 $image_html = '<img src="' . $matches[2][$j] . '"/>';

        //                 $html = str_replace($markdown_link, '', $html);
        //                 $html = str_replace($number, $image_html, $html);
        //             }
        //         }

        //         $html = str_replace("&lt;br/&gt;", "<br/>", $html);

        //         $text = str_replace($table_html, $html, $text);
        //     }
        // }

        //add span tag to display on mathjax
        // $text = str_replace("\(", '<span class="math-tex">\(', $text);
        // $text = str_replace("\)", '\)</span>', $text);

        $text = $this->brToEndlLatex($text);
        if (preg_match_all('/\s{2,}/', $text, $matches)) {
            foreach ($matches[0] as $space_text) {
                $replace = str_repeat('&nbsp;', strlen($space_text));

                $text = str_ireplace($space_text, $replace, $text);
            }
        }
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
        return view('welcome', ['post' => $post, 'histories' => $data['histories']]);
    }

    public function rawHistory($postId, Request $request)
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

        // $request->de_bai = $this->standard($this->reverse($request->de_bai));
        // $request->dap_an = $this->standard($this->reverse($request->dap_an));
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

    function parseHtml($text)
    {
        $parser = new \cebe\markdown\MarkdownExtra();

        $text = str_replace('\nolimits', '\zolimits', $text);
        $text = str_replace('\neq', '\zeq', $text);
        $text = str_replace('\ne', '\ze', $text);
        $text = str_replace('\n', '<br/>', $text);
        $text = str_replace('\zolimits', '\nolimits', $text);
        $text = str_replace('\zeq', '\neq', $text);
        $text = str_replace('\ze', '\ne', $text);

        if (preg_match_all('/\s{2,}/', $text, $matches)) {
            foreach ($matches[0] as $space_text) {
                $replace = str_repeat('&nbsp;', strlen($space_text));

                $text = str_ireplace($space_text, $replace, $text);
            }
        }

        $text = str_replace('media/', 'http://dev.data.giaingay.io/TestProject/public/media/', $text);

        if (preg_match_all('/<table>(.|\||
)*?<\/table>/', $text, $matches)) {

            foreach ($matches[0] as $table_html) {

                $html = $table_html;
                $html = str_replace(['<table>', '</table>'], '', $html);
                $html = $parser->parse($html);

                if (preg_match_all('/(\[\d+\]):\s*([^\[\<]+)/', $html, $matches)) {
                    foreach ($matches[0] as $j => $markdown_link) {
                        $number = '![]' . $matches[1][$j];
                        $image_html = '<img src="' . $matches[2][$j] . '"/>';

                        $html = str_replace($markdown_link, '', $html);
                        $html = str_replace($number, $image_html, $html);
                    }
                }

                $html = str_replace("&lt;br/&gt;", "<br/>", $html);

                $text = str_replace($table_html, $html, $text);
            }
        }

        return $text;
    }

    public function l5($id, Request $request)
    {
        $post = Post::find($id);

        $images = scandir(config('app.image_dir'));
        $ch_images = array_filter(
            $images,
            function ($key) use ($post) {
                if (strpos($key, $post->hoi_dap_id . '-CH') !== false)
                    return true;
                return false;
            }
        );
        $da_images = array_filter(
            $images,
            function ($key) use ($post) {
                if (strpos($key, $post->hoi_dap_id . '-DA') !== false)
                    return true;
                return false;
            }
        );

        $ch_images = array_map(function ($val) {
            return config('app.image_url') . '/' . $val;
        }, $ch_images);

        $da_images = array_map(function ($val) {
            return config('app.image_url') . '/' . $val;
        }, $da_images);

        $data = [];
        // dd($ch_images);
        $data['post_id'] = $id;
        $data['post'] = [
            'tieu_de' => $post->tieu_de,
            'url' => $post->url,
            'id' => $post->id,
            'duong_dan_hoi' => $post->duong_dan_hoi,
            'duong_dan_tra_loi' => $post->duong_dan_tra_loi,
            'de_bai_parsed' => $this->parseHtml($post->de_bai),
            'dap_an_parsed' => $this->parseHtml($post->dap_an),
            'dap_an' => $post->dap_an,
            'de_bai' => $post->de_bai,
            'da_images' => $da_images,
            'ch_images' => $ch_images,
        ];
        return view('l5', $data);
    }

    public function itemL5($id, Request $request)
    {
        $item = Post::find($id);
        if ($item->level != 'L5')
            return [
            'success' => false,
            'message' => 'Không được chuyển sang L5-Z nếu item không ở L5'
        ];
        $item->level = 'L5-Z';
        $item->save();
        return [
            'success' => true,
            'message' => 'Chuyển level thành công'
        ];
    }

    public function escapeSlash($text)
    {
        $next = '';
        for ($i = 0; $i < strlen($text); ++$i) {
            if ($text[$i] == '\\' && ($text[$i + 1] == '(' || $text[$i + 1] == ')')) {
                $next .= '\\\\' . $text[$i + 1];
                $i += 1;
            } else $next .= $text[$i];
        }
        return $next;
    }

    public function htmlTableToMarkdown($text) {
        if (preg_match_all('/<tr>(.|\||\s)*?<\/tr>/', $text, $matches)) {
            $array = [];
            foreach ($matches[0] as $trtag) {
                if (preg_match_all('/(<th>|<td>)(.|\||\s)*?(<\/th>|<\/td>)/', $trtag, $item_matches)) {
                    $item_matches[0] = array_map(function ($item) {
                        $item = str_replace('<th>', '', $item);
                        $item = str_replace('</th>', '', $item);
                        $item = str_replace('<td>', '', $item);
                        $item = str_replace('</td>', '', $item);
                        $item .= '\n';
                        return $item;
                    }, $item_matches[0]);
                    array_push($array, $item_matches[0]);
                }
            }

            $th = true;
            $col_width = [];
            for ($i = 0; $i < count($array[0]); ++$i) {
                $max = 1;
                for ($j = 0; $j < count($array); ++$j)
                    $max = max($max, strlen($array[$j][$i]));
                array_push($col_width, $max);
            }
            $table = '<table>';

            for ($i = 0; $i < count($array); ++$i) {
                $table .= '| ';
                $separator = '| ';
                for ($j = 0; $j < count($array[0]); ++$j) {
                    $table .= $array[$i][$j];
                    $separator .= str_repeat('-', $col_width[$j]);
                    if ($j == count($array[0]) - 1) {
                        $table .= ' |';
                        $separator .= ' |';
                    } else {
                        $table .= ' | ';
                        $separator .= ' | ';
                    }
                }
                if ($i != count($array) - 1) {
                    $table .= PHP_EOL;
                    $table .= $separator . PHP_EOL;
                }
            }
            $table .= '</table>';
            return $table;
        }
        return '';
    }

    public function test()
    {
        $text = $this->reverse('a) Tìm tọa độ điểm A thông qua hoành độ của điểm A, và thuộc đường thẳng (d)<br />
        c) Dựa vào đồ thị ta xác định tọa độ giao điểm thứ hai của (P) và (d)<br />
        Đồ thị hàm số (P) cắt đường thẳng (d): y = 3x – 4 tại điểm A có hoành độ bằng -2 nên ta có : \(y = 3.\left( { - 2} \right) - 4 = - 10 \Rightarrow A\left( { - 2; - 10} \right)\)<br />
        Điểm A thuộc đồ thị hàm số (P) \(y = a{x^2}\,\,\left( {a \ne 0} \right) \Rightarrow - 10 = a.{\left( { - 2} \right)^2}\)\(\, \Rightarrow a = \dfrac{{ - 5}}{2}\)<br />
        Vậy hàm số cần tìm có dạng: \(y = - \dfrac{5}{2}{x^2}\)<br />
        b) Bảng giá trị
        <table>
            <thead>
                <tr>
                    <th>\(x\)</th>
                    <th>\( - 2\)</th>
                    <th>\( - 1\)</th>
                    <th>0</th>
                    <th>1</th>
                    <th>2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>\(y = - \dfrac{5}{2}{x^2}\)</td>
                    <td>\( - 10\)</td>
                    <td>\( - \dfrac{5}{2}\)</td>
                    <td>0</td>
                    <td>\( - \dfrac{5}{2}\)</td>
                    <td>\( - 10\)</td>
                </tr>
                <tr>
                    <td>\(y = 3x - 4\)</td>
                    <td> </td>
                    <td> </td>
                    <td>\( - 4\)</td>
                    <td>\( - 1\)</td>
                    <td> </td>
                </tr>
            </tbody>
        </table>
          <br />
        Vậy đồ thị hàm số \(y = - \dfrac{5}{2}{x^2}\)là 1 Parabol đi qua các điểm có tọa độ là \(\left( { - 2; - 10} \right);\left( { - 1; - \dfrac{5}{2}} \right);\left( {0;0} \right);\)\(\,\left( {1; - \dfrac{5}{2}} \right);\left( {2; - 10} \right)\)<br />
        Đồ thị hàm số \(y = 3x - 4\) là 1 đường thẳng đi qua các điểm có tọa độ là \(\left( {0; - 4} \right);\left( {1; - 1} \right)\)<br />
        <img src="http://dev.data.giaingay.io/TestProject/public/media/Solutions/9lX2dyqPw5c1b5cb6e983e9.97490653/TopkidMP4rcf.jpg" /><br />
        c) Bằng đồ thị, hãy xác định tọa độ giao điểm thứ hai của (P) và (d) vừa vẽ ở câu b.<br />
        Tọa độ giao điểm thứ hai của (P) và (d) là: \(\left( {\dfrac{4}{5};\dfrac{{ - 8}}{5}} \right)\)');
        dd($text);
        $text = '<table>
        <thead>
            <tr>
                <th>\(x\)</th>
                <th>\( - 4\)</th>
                <th>\( - 2\)</th>
                <th>0</th>
                <th>2</th>
                <th>4</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>\(y = - \dfrac{1}{4}{x^2}\)</td>
                <td>\( - 4\)</td>
                <td>\( - 1\)</td>
                <td>0</td>
                <td>\( - 1\)</td>
                <td>\( - 4\)</td>
            </tr>
        </tbody>
    </table>';
        dd($this->htmlTableToMarkdown($text));
    }
    // | \(x\)\n | \( - 4\)\n | \( - 2\)\n | 0\n | 2\n | 4\n |
    // | ----------------------------- | ---------- | ---------- | --- | ---------- | ---------- |
    // | \(y = - \dfrac{1}{4}{x^2}\)\n | \( - 4\)\n | \( - 1\)\n | 0\n | \( - 1\)\n | \( - 4\)\n |
}
