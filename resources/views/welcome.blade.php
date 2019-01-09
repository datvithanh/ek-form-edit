@extends('layouts.master')

@section('content')
<div class="container">
    <div class="table-wrapper">
        <div class="card-body">
                <div class="col-md-8"><h3>Sửa câu hỏi đáp</h3></div>
        </div>
        <div class="card-body" style="padding-bottom: 0px">
            <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 10px 20px">
                <div class="form-group" style="width: 100%;">
                    <label style="width: 15%"><b>ID:</b></label>
                    <input class="form-control" style="display: inline-block; width:35%" id="post-id" min="0" type="html"
                        placeholder="Post's id" value="{{$post->id}}" maxlength="8">
                    &nbsp;
                    <button class="btn btn-success" style="display: inline-block;" id="btn-change-id">Tìm kiếm</button>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 15%"><b>ItemID:</b></label>
                    <input class="form-control" style="display: inline-block; width:35%" id="post-itemid" type="text"
                        placeholder="Post's itemID" value="{{$post->hoi_dap_id}}" maxlength="50"/>
                    &nbsp;
                    <button class="btn btn-success" style="display: inline-block;" id="btn-change-itemid">Tìm kiếm</button>
                </div>
                <hr width="100%">
                <div class="form-group" style="width: 100%;">
                    <label style="vertical-align: top; width: 15%"><b>Đề bài:</b></label>
                    <div style="display:inline-block; width:80%">

                    <textarea class="form-control" style="width:100%" id="postquestion" rows="7"
                        placeholder="Post's question in HTML">{{$post->de_bai}}</textarea>
                    <!-- <p style="margin-top:20px; width: 100%" id="postquestion-display">
                        {!!$post->de_bai!!}
                    </p> -->
                    </div>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="vertical-align: top; width: 15%"><b>Đáp án:</b></label>
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
@if(sizeof($histories) > 0 )
<div class="container">
    <hr>
    <h3>
        Lịch sử chỉnh sửa 
    </h3>
    <div class="row">
        @foreach($histories as $history)
            <div class="col-md-4" style="margin-top:25px">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title" style="text-align: center">
                            {{$history->created}}
                        </h4>
                        <!-- <p class="card-text">Some example text. Some example text.</p> -->
                        <div class="d-flex justify-content-center">
                        <button class="btn btn-outline-info" onclick="rollback({{$history->id}})">Sử dụng lịch sử</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
@push('scripts')
<script src="{{url('/assets/ckeditor/ckeditor.js')}}" charset="utf-8"></script>
<script src="{{url('/assets/ckeditor/adapters/jquery.js')}}"></script>

<script>
    let histories = {!!$histories!!};
    let prev_id = "{{$post->id}}";
    let prev_itemid = "{{$post->hoi_dap_id}}";

    // CKEDITOR.replace('postquestion', { extraPlugins: 'mathjax,eqneditor', height: '250px', allowedContent: true});
    // CKEDITOR.replace('postanswer', { extraPlugins: 'mathjax,eqneditor', height: '250px', allowedContent: true});
    CKEDITOR.replace('postquestion', { extraPlugins: 'eqneditor', height: '250px', allowedContent: true});
    CKEDITOR.replace('postanswer', { extraPlugins: 'eqneditor', height: '250px', allowedContent: true});

    let qeditor = CKEDITOR.instances.postquestion;
    let aeditor = CKEDITOR.instances.postanswer;

    qeditor.on('change', function(){
        console.log(qeditor.getData());
    });

    function renderMathJax()
    {
        window.MathJax = {};
        var head= document.getElementsByTagName('head')[0];
        var script= document.createElement('script');
        script.src= 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-MML-AM_CHTML&cachebuster='+ new Date().getTime();
        head.appendChild(script);
    }

    (function($) {
    $.fn.inputFilter = function(inputFilter) {
            return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
            });
        };
    }(jQuery));

    $("#btn-change-id").click(function(){
        let post_id = $("#post-id").val();
        if(prev_id != post_id) {
            if(post_id == "" || isNaN(post_id) || Number(post_id) <= 0 || Number.isInteger(Number(post_id)) == false){
                toastr.error("Tìm kiếm bằng ID: ID không được để trống và phải là số nguyên dương");
                return;
            }
            window.location = "{{url('/post')}}/" + post_id + "/edit";
        }
    });

    $("#post-id").inputFilter(function(value) {
        return /^\d*$/.test(value); });
    
    $("#post-itemid").inputFilter(function(value) {
        return /^[0-9a-zA-Z.]*$/i.test(value); });
    
    $("#btn-change-itemid").click(function(){
        let post_id = $("#post-itemid").val();
        if(prev_itemid != post_id){
            if(post_id == ""){
                toastr.error("Tìm kiếm bằng ItemId: ItemID không được để trống");
                return;
            }
            window.location = "{{url('/post')}}/" + post_id + "/edit";
        }
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
        let de_bai = qeditor.getData();
        let dap_an = aeditor.getData();
        if($("#post-id").val() != prev_id || $("#post-itemid").val() != prev_itemid) 
        {
            toastr.error("Giữ nguyên ID và ItemID để thay đổi");
            return;
        }

        if(de_bai == "" || dap_an == "" || de_bai.trim() == "" || dap_an.trim() == "")
        {
            toastr.error("Thiếu thông tin");
            return;
        }
        $("#btn-edit").prop('disabled', true);
        let data = {
            de_bai: de_bai,
            dap_an: dap_an
        }
        axios.put("{{url('/api/post/')}}/{{$post->id}}", data)
            .then(function(response){
                toastr.success("Sửa Thành công");
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            }).catch(function(error){
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau");
            })
    });

    function rependl(str){
        str = str.replace('\nolimits', '\zolimits');
        str = str.replace('\neq', '\zeq');
        str = str.replace('\ne', '\ze');
        str = str.replace('\n', '<br/>');
        str = str.replace('\zolimits', '\nolimits');
        str = str.replace('\zeq', '\neq');
        str = str.replace('\ze', '\ne');
        return str;
    }

    function addSpan(str)
    {
        let out = '';
        
        for(i=0;i<str.length;++i){
            if(str[i] == '\\' && str[i+1] == '(') {
                out = out + '<span class="math-tex">\\(';
                i+=1;
            }
            else {
                if(str[i] == '\\' && str[i+1] == ')'){
                    out = out + '\\)</span>';
                    i+=1;
                }
                else
                    out+=str[i];
            }
        }
        console.log(out);
        return out;
    }

    var x;
    var y;
    function rollback(historyId){
        let history = histories.find(x => x.id == historyId);
        de_bai = history.de_bai;
        dap_an = history.dap_an;
        // de_bai = rependl(de_bai);
        de_bai = addSpan(de_bai);
        // dap_an = rependl(dap_an);
        dap_an = addSpan(dap_an);
        qeditor.setData(de_bai);
        aeditor.setData(dap_an);
    }
</script>
@endpush
