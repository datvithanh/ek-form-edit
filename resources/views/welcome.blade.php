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
                    <label style="width: 15%">ID:</label>
                    <input class="form-control" style="display: inline-block; width:35%" id="post-id" type="text"
                        placeholder="Post's id" value="{{$post->id}}"/>
                    <button class="btn btn-success" style="display: inline-block;" id="btn-change-id">Go</button>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 15%">ItemID:</label>
                    <p style="display: inline-block;">{{$post->hoi_dap_id}}</p>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="vertical-align: top; width: 15%">Đề bài:</label>
                    <div style="display:inline-block; width:80%">

                    <textarea class="form-control" style="width:100%" id="postquestion" rows="7"
                        placeholder="Post's question in HTML">{{$post->de_bai}}</textarea>
                    <!-- <p style="margin-top:20px; width: 100%" id="postquestion-display">
                        {!!$post->de_bai!!}
                    </p> -->
                    </div>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="vertical-align: top; width: 15%">Đáp án:</label>
                    <div style="display:inline-block; width:80%">
                    <textarea class="form-control" style="width:100%" id="postanswer" rows="7"
                        placeholder="Post's answer">{{$post->dap_an}}</textarea>
                    <!-- <p style="margin-top:20px; width: 100%" id="postanswer-display">
                        {!!$post->dap_an!!}
                    </p> -->
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
<script src="{{url('/assets/ckeditor/ckeditor.js')}}" charset="utf-8"></script>
<script src="{{url('/assets/ckeditor/adapters/jquery.js')}}"></script>
<script>
    // console.log(CKEDITOR.config);
    // CKEDITOR.replace( 'editor', {
    //     extraPlugins: 'easyimage',
    //     cloudServices_tokenUrl: 'https://example.com/cs-token-endpoint',
    //     cloudServices_uploadUrl: 'https://your-organization-id.cke-cs.com/easyimage/upload/'
    // });
    let prev_id = "{{$post->id}}";
    // $('#postquestion').ckeditor();
    // $('#postanswer').ckeditor();
    CKEDITOR.replace('postquestion', { extraPlugins: 'mathjax,eqneditor', height: '250px', allowedContent: true});
    CKEDITOR.replace('postanswer', { extraPlugins: 'mathjax,eqneditor', height: '250px', allowedContent: true});
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

    $('#postquestion').bind('input propertychange', function() {
        console.log('asdjkdk');
        $("#postquestion-display")[0].innerHTML = $("#postquestion").val();
        // renderMathJax();
    });

    $('#postanswer').bind('input propertychange', function() {
        $("#postanswer-display")[0].innerHTML = $("#postanswer").val();
        // renderMathJax();
    });

    $("#btn-edit").click(function(){
        let id = $("#post-id").val();
        $("#btn-edit").prop('disabled', true);
        // let de_bai = CKEDITOR.instances.postquestion.document.getBody().getText();
        // let dap_an = CKEDITOR.instances.postanswer.document.getBody().getText();
        let de_bai = CKEDITOR.instances.postquestion.getData();
        let dap_an = CKEDITOR.instances.postanswer.getData();
        de_bai = de_bai.substr(3, de_bai.length-8);
        dap_an = dap_an.substr(3, dap_an.length-8);
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
