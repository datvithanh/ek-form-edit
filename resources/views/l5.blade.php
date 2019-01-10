<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{url('assets/css/l5.css')}}" rel="stylesheet" type="text/css">
    <link href="{{url('assets/css/toastr.min.css')}}" rel="stylesheet" type="text/css">
    <style>
        #loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #header{
            position: relative;
            left: 20%;
        }
        #next{
            margin-left: 2rem;
        }
        a.btn{
            color: white!important;
        }
        #body_container{
            position: relative;
            top: 3rem;
        }
        #link{
            padding: 1.25rem;
        }
        table th, table td{
            border: 1px solid black;
        }
        #id{
            text-align: right;
        }
    </style>
</head>
<body>

<div id="body_container">
    <div class="row">
        <div class="col-md-12">
            <form id="header" class="form-inline">
                <div class="form-group mx-sm-3 mb-2">
                    <label for="id" class="sr-only">Id</label>
                    <input type="text" class="form-control" id="id" name="id" placeholder="Id">
                </div>
                <a id="submit" class="btn btn-primary mb-2">Tìm kiếm</a>

                <button id="next" class="btn btn-primary mb-2">Xác nhận chuyển L5-Z</button>
                &nbsp;
                <div id="loader"></div>
            </form>

            <div style="color: black;">
                <input type="hidden" id="cur_id" name="cur_id" value="{{$post_id}}">
                <div>
                    <div class="container">
                        <div id="link">
                            <a target="_blank" href="{{$post['url']}}">{{$post['url']}}</a>
                            <hr/>
                            <label style="font-weight: 700">Tiêu đề:</label>
                            {{$post['tieu_de']}}
                            <br/>
                            <br/>
                            <label style="font-weight: 700">Id trên toppick:</label>
                            {{$post['id']}}
                            <br/>
                            <br/>
                            <label style="font-weight: 700">Đường dẫn câu hỏi:</label>
                            {{$post['duong_dan_hoi']}}
                            <br/>
                            <br/>
                            <label style="font-weight: 700">Đường dẫn câu trả lời:</label>
                            {{$post['duong_dan_tra_loi']}}
                            <br/>
                            <br/>
                            <hr/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div style="color: black;">
                <div class="container">
                    <div class="card-body">
                        <label style="font-weight: 700">Đề bài</label>
                        <br/>
                        {!!$post['de_bai_parsed']!!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @if(count($post['ch_images']) > 0 )
            <label style="color: black; font-weight: 700;">
                Ảnh đề bài 
            </label>
            <br/>
            @foreach($post['ch_images'] as $image) 
            <img style="box-shadow: 1px 1px 1px 1px #dfdfdf" width="95%" src="{{$image}}">
            @endforeach
            @endif
        </div>
        <div class="col-md-6">
            <div style="color: black;">
                <div class="container">
                    <div class="card-body">
                        <label style="font-weight: 700">Đề bài latex</label>
                        <br/>
                        <textarea rows="4" cols="100">{{$post['de_bai']}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <hr width="80%"/>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div style="color: black;">
                <div class="container">
                    <div class="card-body">
                        <label style="font-weight: 700">Đáp án</label>
                        <br/>
                        {!!$post['dap_an_parsed']!!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @if(count($post['da_images']) > 0 )
            <label style="color: black; font-weight: 700;">
                Ảnh đáp án
            </label>
            <br/>
            @foreach($post['da_images'] as $image) 
            <img style="box-shadow: 1px 1px 1px 1px #dfdfdf" width="95%" src="{{$image}}">
            @endforeach
            @endif
        </div>
        <div class="col-md-6">
            <div style="color: black;">
                <div class="container">
                    <div class="card-body">
                        <label style="font-weight: 700">Đáp án latex</label>
                        <br/>
                        <textarea rows="4" cols="100">{{$post['dap_an']}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <hr width="80%"/>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
<script src="{{url('assets/js/jquery-3.2.1.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-MML-AM_CHTML' async></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js'></script>
<script src="{{url('assets/js/toastr.min.js')}}"></script>

<script>
    $(document).ready(function(){
        // $('form').submit(false);
        $('form').bind("keypress", function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                let header_form = $('form#header');

                let id = header_form.find('input[name="id"]').val();

                if(!id.match(/\S/)) {
                    alert ('Đừng để trống id!');
                    return false;
                }

                id = parseInt(id);

                if(id <= 0){
                    alert('Hãy nhập 1 số dương!')
                    return;
                }

                if(Number.isInteger(id)) window.location.href = "{{url('item/l5')}}/"+id;
                else alert('Hãy nhập id là 1 số dương!');
            }
        });

        $("#submit").click(function(){
            let header_form = $('form#header');

            let id = header_form.find('input[name="id"]').val();

            if(!id.match(/\S/)) {
                alert ('Đừng để trống id!');
                return false;
            }

            id = parseInt(id);

            if(id <= 0){
                alert('Hãy nhập 1 số dương!')
                return;
            }

            if(Number.isInteger(id)) window.location.href = "{{url('item/l5')}}/"+id;
            else alert('Hãy nhập id là 1 số dương!');
        });

        $("#next").click(function(){
            // let cur_id = parseInt($('input[name="cur_id"]').val());
            // let next_id = cur_id + 1;
            // window.location.href = "{{url('post/l5')}}/"+next_id;
            $("#next").prop('disabled', true);
            document.getElementById('loader').style.display = "block";

            axios.put("{{url('api/item/l5')}}/{{$post['id']}}").then(function(response) {
                console.log(response.data.success);
                if(response.data.success == true) {
                    toastr.success(response.data.message);
                }
                else {
                    toastr.error(response.data.message);
                }
                document.getElementById('loader').style.display = "none";

            }).catch(function(error){
                console.log(error);
            });
        });
    });
</script>


    </body>
</html>