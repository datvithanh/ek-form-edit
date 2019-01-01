@extends('layouts.master')

@section('content')
<div class="container">
    <div class="table-wrapper">
        <div class="card-body">
                <div class="col-md-8"><h2>Edit câu hỏi đáp</h2></div>
        </div>
        <div class="card-body" style="padding-bottom: 0px">
            <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 10px 20px">
                <div class="form-group" style="width: 100%;">
                    <label style="width: 20%">ItemID:</label>
                    <input class="form-control" style="display: inline-block; width:35%" id="post-id" type="text"
                        placeholder="Post's id" value="{{$post->id}}"/>
                    <button class="btn btn-success" style="display: inline-block;" id="btn-change-id">Go</button>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 20%">Id hỏi đáp của tài liệu:</label>
                    <p style="display: inline-block;">{{$post->hoi_dap_id}}</p>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 20%">Câu hỏi:</label>
                    <div style="display:inline-block; width:75%">

                    <textarea class="form-control" style="width:100%" id="post-question" rows="7"
                        placeholder="Post's question in HTML">{{$post->de_bai}}</textarea>
                    <p style="width: 100%" id="post-question-display">
                        {!!$post->de_bai!!}
                    </p>
                    </div>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 20%">Đáp án:</label>
                    <div style="display:inline-block; width:75%">
                    <textarea class="form-control" style="width:100%" id="post-answer" rows="7"
                        placeholder="Post's answer">{{$post->dap_an}}</textarea>
                    <p style="width: 100%" id="post-answer-display">
                        {!!$post->dap_an!!}
                    </p>
                    </div>
                </div>
                <button class="btn btn-success" style="width: 30%; margin: 10px; padding: 15px;" id="btn-edit">Lưu
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    let prev_id = "{{$post->id}}";

    function renderMathJax()
    {
        window.MathJax = {};
        var head= document.getElementsByTagName('head')[0];
        var script= document.createElement('script');
        script.src= 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-MML-AM_CHTML&cachebuster='+ new Date().getTime();
        head.appendChild(script);
    }

    $("#btn-change-id").click(function(){
        let post_id = $("#post-id").val();
        if(prev_id != post_id)
            window.location = "{{url('/post')}}/" + post_id + "/edit";
    });

    $('#post-question').bind('input propertychange', function() {
        $("#post-question-display")[0].innerHTML = $("#post-question").val();
        renderMathJax();
    });

    $('#post-answer').bind('input propertychange', function() {
        $("#post-answer-display")[0].innerHTML = $("#post-answer").val();
        renderMathJax();
    });

    $("#btn-edit").click(function(){
        let id = $("#post-id").val();
        let de_bai = $("#post-question").val();
        let dap_an = $("#post-answer").val();
        if(id == "" || de_bai == "" || dap_an == "" || de_bai.trim() == "" || dap_an.trim() == "" || id.trim() == "")
        {
            toastr.error("Thiếu thông tin");
            return;
        }
        let data = {
            de_bai: de_bai,
            dap_an: dap_an
        }
        axios.put("{{url('/api/post/')}}" + "/" + id, data)
            .then(function(response){
                toastr.success("Edit Thành công");
                window.location.reload();
            }).catch(function(error){

            })
    });
</script>
@endpush
