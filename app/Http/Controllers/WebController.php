<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\PostHistory;

class WebController extends Controller
{
    public function endlToBr($text)
    {
        $text = str_replace('\nolimits', '\zolimits', $text);
        $text = str_replace('\neq', '\zeq', $text);
        $text = str_replace('\ne', '\ze', $text);
        $text = str_replace('\n', '<br/>', $text);
        $text = str_replace('\zolimits', '\nolimits', $text);
        $text = str_replace('\zeq', '\neq', $text);
        $text = str_replace('\ze', '\ne', $text);
        return $text;
    }

    public function addSpan($str)
    {
        $str = str_replace("\(", '<span class="math-tex">\(', $str);
        $str = str_replace("\)", '\)</span>', $str);
        return $str;
    }

    public function stripSpan($str)
    {
        $str = str_replace('<span class="math-tex">\(', "\(", $str);
        $str = str_replace('\)</span>', "\)", $str);
        return $str;
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

        $post->de_bai = $this->addSpan($post->de_bai);
        $post->dap_an = $this->addSpan($post->dap_an);

        $data['post'] = $post;
        $data['histories'] = PostHistory::where('post_id', $postId)->orderBy('created_at', 'desc')->get()->map(function ($history) {
            // dd($history->content);
            // $history->content = $this->endlToBr($history->content);

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
        $request->de_bai = str_replace('<br/>', '', $request->de_bai);
        $request->dap_an = str_replace('<br/>', '', $request->dap_an);

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

        $post->de_bai = $this->stripSpan($post->de_bai);
        $post->dap_an = $this->stripSpan($post->dap_an);

        $post->save();
        return ['message' => 'success'];
    }

    public function test()
    {
        // $s = "\(\eqalign{& \sin B = {b \over a};\,\,\cos B = {c \over a};\,\,tgB = {b \over c};\,\,{\mathop{\rm cotgB}\nolimits} = {c \over b} \cr & \sin C = {c \over a};\,\,\cos C = {b \over a};\,\,tgC = {c \over b};\,\,{\mathop{\rm cotgB}\nolimits} = {b \over c} \cr} \)\n a) \(\eqalign{& b = a.\left( {{b \over a}} \right) = a.\sin B = a.\cos C \cr & c = a.\left( {{c \over a}} \right) = a.\cos B = a.\sin C \cr} \) \n b) \(\eqalign{& b = c.\left( {{b \over c}} \right) = c.tgB = c.{\mathop{\rm cotg}\nolimits} C \cr & c = b.\left( {{c \over b}} \right) = b.{\mathop{\rm cotg}\nolimits} B = b.tgC \cr} \)";
        // $s = "+) Giá trị của hàm số \(f(x)\) tại \(x=a\) là: \(f(a)\).\n Tức là thay \(x=a\) vào biểu thức của hàm số \(f(x)\) ta tính được \(f(a)\).\n +) Giá trị của hàm số \(y=ax+b\) lớn hơn giá trị của hàm số \(y=ax\) là \(b\) đơn vị khi \(x\) lấy cùng một giá trị.\n \n \n \n a) Thay các giá trị vào hàm số \(y = f(x) = \dfrac{2}{3} x\). Ta có \n \(f(-2) = \dfrac{2}{3}.(-2)=\dfrac{2.(-2)}{3}=\dfrac{-4}{3}\).\n \(f(-1) = \dfrac{2}{3}.(-1)= \dfrac{2.(-1)}{3}=\dfrac{-2}{3}\).\n \(f(0) = \dfrac{2}{3}.0=0\).\n \(f\left (\dfrac{1}{2}\right ) =\dfrac{2}{3}.\dfrac{1}{2}=\dfrac{1}{3}\).\n \(f(1) = \dfrac{2}{3}.1=\frac{2}{3}\).\n \(f(2) = \dfrac{2}{3}.2=\frac{4}{3}\).\n \(f(3) = \dfrac{2}{3}.3=2\).\n b) Thay các giá trị vào hàm số \(y = g(x) = \dfrac{2}{3} x + 3\). Ta có \n \(g(-2) = \dfrac{2}{3}.(-2)+3= \dfrac{2.(-2)}{3}+3=\dfrac{-4}{3}+\dfrac{9}{3}\)\n \(=\dfrac{5}{3}\).\n \(g(-1) = \dfrac{2}{3}.(-1)+3 = \dfrac{2.(-1)}{3}+3= \dfrac{-2}{3}+\dfrac{9}{3}\)\n \(=\dfrac{7}{3}\).\n \(g(0) = \dfrac{2}{3}.0+3= \dfrac{2.0}{3}+3=0+3=3.\)\n \(g\left ( \dfrac{1}{2} \right ) = \dfrac{2}{3}. \dfrac{1}{2} +3=\dfrac{1}{3}+3=\dfrac{1}{3}+\dfrac{9}{3}=\dfrac{10}{3}\).\n \(g(1) = \dfrac{2}{3}.1+3=\dfrac{2}{3}+3=\dfrac{2}{3}+\dfrac{9}{3}=\dfrac{11}{3}\).\n \(g(2) = \dfrac{2}{3}.2+3=\dfrac{2.2}{3}+3=\dfrac{4}{3}+3=\dfrac{4}{3}+\dfrac{9}{3}\)\n \(=\dfrac{13}{3}\).\n \(g(3) = \dfrac{2}{3}.3+3=\dfrac{2.3}{3}+3=\dfrac{6}{3}+\dfrac{9}{3}=\dfrac{15}{3}=5\).\n c) \n Khi \(x\) lấy cùng một giá trị thì giá trị của \(g(x)\) lớn hơn giá trị của \(f(x)\) là \(3\) đơn vị.";
        // $s = "Cho hai đường tròn \((O;\ R)\) và \((O';\ r)\). Khi đó:\n a) \((O;\ R)\) và \((O';\ r)\) tiếp xúc ngoài nếu \(OO'=R+r\);\n b) \((O;\ R)\) và \((O';\ r)\) tiếp xúc trong nếu \(OO'=R-r > 0\).\n \n \n \n a)\n \(9\)\n Hai đường tròn tiếp xúc ngoài nên \(OO'=R+r=OA+O'A=3+1=4 (cm).\)\n Vậy \(O'\) luôn cách \(O\) một khoảng không đổi là \(4cm\). Do đó \(O'\) nằm trên đường tròn tâm \(O\) bán kính \(4cm\).\n Trả lời: Tâm của các đường tròn có bán kính 1cm tiếp xúc ngoài với đường tròn \((O;\ 3cm)\) nằm trên đường tròn \((O; 4cm)\).\n b)\n \(e - 2\)\n Hai đường tròn tiếp xúc trong nên \(OO'=R-r=AO-AO'=3-1=2 (cm).\)\n Vậy \(O'\) luôn cách \(O\) một khoảng không đổi là \(2cm\). Do đó \(O'\) nằm trên đường tròn tâm \(O\) bán kính \(2cm\).\n Trả lời: Tâm của các đường tròn có bán kính \(1cm\) tiếp xúc trong với đường tròn \((O;3cm)\) nằm trên đường tròn \((O;\ 2cm)\).";
        // $s = "Các bước giải bài toán bằng cách lập phương trình\n Bước 1:\n 1. Lập phương trình, chọn ẩn và tìm điều kiện của ẩn\n 2. Biểu thị các đại lượng chưa biết theo ẩn và các đại lượng đã biết.\n 3. Lập phương trình biểu thị mối quan hệ giữa các đại lượng.\n Bước 2: giải phương trình\n Bước 3: Đối chiếu với điều kiện và kết luận bài toán.\n \n \n \n Gọi số áo phải may trong 1 ngày theo kế hoạch là x (áo) (\(x \in {N^*}\) )\n Thời gian quy định may xong 3000 áo là: \(\dfrac{{3000}}{x}\) (ngày)\n Số áo thực tế may được trong 1 ngày là: x + 6 (áo)\n Thời gian thực tế may xong 2650 cái áo là: \(\dfrac{{2650}}{{x + 6}}\) (ngày)\n Vì xưởng may xong 2650 áo trước khi hết hạn 5 ngày nên ta có phương trình:\n \(\begin{array}{l}\dfrac{{3000}}{x} - \dfrac{{2650}}{{x + 6}} = 5\\ \Leftrightarrow 600\left( {x + 6} \right) - 530x = x\left( {x + 6} \right)\\ \Leftrightarrow 600x + 3600 - 530x - {x^2} - 6x = 0\\ \Leftrightarrow - {x^2} + 64x + 3600 = 0\\ \Leftrightarrow {x^2} - 64x - 3600 = 0\\a = 1;b' = - 32;c = - 3600\\\Delta ' = {\left( { - 32} \right)^2} + 3600 = 4624 > 0;\\ \sqrt {\Delta '} = 68\end{array}\)\n Khi đó phương trình có 2 nghiệm phân biệt là:\n \({x_1} = 32 + 68 = 100\left( {tm} \right);\\{x_2} = 32 - 68 = - 36\left( {ktm} \right)\)\n Vậy số áo phải may trong 1 ngày theo kế hoạch là 100 áo.";
        // dd(str_split($s));
        // $this->process($s);
    }
}
