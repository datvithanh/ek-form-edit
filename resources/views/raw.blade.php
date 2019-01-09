@extends('layouts.master')

@section('content')
<div class="container">
    <div class="table-wrapper">
        <div class="card-body">
                <div class="col-md-8"><h3>Câu hỏi đáp</h3></div>
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
            </div>
        </div>  
    </div>
    <hr>
</div>
@if(sizeof($histories) > 0 )
<div class="container">
    <div class="table-wrapper">
        <div class="card-body">
            <div class="col-md-8"><h3>Lịch sử chỉnh sửa</h3></div>
        </div>
        <div class="card-body" style="padding-bottom: 0px">
            @foreach($histories as $history)
            <div class="row">
                <div class="col-md-4" style="margin-top:25px">
                    <h3>{{$history->created}}</h3>
                </div>
                <div class="col-md-8" style="margin-top:25px">
                    Đề bài
                    <br>
                    <textarea rows="4" cols="80">{{$history->de_bai}}</textarea>
                    <br>
                    Đáp án 
                    <br>
                    <textarea rows="4" cols="80">{{$history->dap_an}}</textarea>
                </div>
            </div>
            <hr width="40%">
            @endforeach

        </div>  
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

    $("#btn-change-id").click(function(){
        let post_id = $("#post-id").val();
        if(prev_id != post_id) {
            if(post_id == "" || isNaN(post_id) || Number(post_id) <= 0 || Number.isInteger(Number(post_id)) == false){
                toastr.error("Tìm kiếm bằng ID: ID không được để trống và phải là số nguyên dương");
                return;
            }
            window.location = "{{url('/post')}}/" + post_id + "/raw";
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
            window.location = "{{url('/post')}}/" + post_id + "/raw";
        }
    });

    
</script>
@endpush
