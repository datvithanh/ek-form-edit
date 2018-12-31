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
                    <label style="width: 25%">Id của tài liệu:</label>
                    <input class="form-control" style="display: inline-block; width:45%" id="post-id" type="text"
                        placeholder="Post's id" value="{{$post->id}}"/>
                    <button class="btn btn-success" style="display: inline-block;" id="btn-change-id">Go</button>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 25%">Id hỏi đáp của tài liệu:</label>
                    <!-- <input class="form-control" style="display: inline-block; width:45%" id="post-hoi-dap-id" type="text"
                        placeholder="Post's hoi dap id" value="{{$post->hoi_dap_id}}"/> -->
                    <p style="display: inline-block;">{{$post->hoi_dap_id}}</p>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 25%">Câu hỏi:</label>
                    <textarea class="form-control" style="display: inline-block; width:45%" id="post-question" rows="7"
                        placeholder="Post's question in HTML">{{$post->de_bai}}</textarea>
                </div>
                <div class="form-group" style="width: 100%;">
                    <label style="width: 25%">Đáp án:</label>
                    <textarea class="form-control" style="display: inline-block; width:45%" id="post-answer" rows="7"
                        placeholder="Post's answer">{{$post->dap_an}}</textarea>
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
    $("#btn-change-id").click(function(){
        let post_id = $("#post-id").val();
        if(prev_id != post_id)
            window.location = "/post/" + post_id + "/edit";
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
        axios.put("/api/post/" + id, data)
            .then(function(response){
                toastr.success("Edit Thành công");
                window.location.reload();
            }).catch(function(error){

            })
    });
</script>
@endpush