@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

            </div>
        </div>
        <div class="row justify-content-center mt-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Videos') }}
                        <button type="button" class="btn btn-primary float-right" data-toggle="modal"
                                data-target="#videoUploadModal">
                            Upload
                        </button>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if($videos)
                            <div class="row">
                                @foreach($videos as $video)
                                    <div class="col-md-4 m-3">
                                        <h5>{{$video->title}}</h5>
                                        <video class="video-js vjs-default-skin vjs-big-play-centered vjs-16-9" controls
                                               preload="none"
                                               poster="{{$video->thumbnail}}"
                                               style="width:100%;height:100%;" width="412" height="231">
                                            <source src="{{asset('storage/uploads/'.$video->user->id.'/'.$video->url)}}"
                                                    type="application/x-mpegURL">
                                        </video>
                                        <p>{{Carbon\Carbon::parse($video->created_at)->diffForHumans()}}</p>
                                    </div>
                                @endforeach
                            </div>

                        @endif

                        {{$videos->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.video_upload_modal')

@endsection

<style>


</style>

@section('scripts')
    <script>


        $(function () {

            $('.video-js').mediaelementplayer();
            $('.overlays').appendTo('.mejs-layers');

            $("#uploadForm").submit(function (e) {
                e.preventDefault();
                $.ajax({
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = ((evt.loaded / evt.total) * 100);
                                $(".progress-bar").width(percentComplete + '%');
                                $(".progress-bar").html(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: '{{route("video.store")}}',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        $(".progress-bar").width('0%');
                        $('#uploadStatus').html('<div class=text-center><img src="images/loader.gif" width=50/></div>');
                    },
                    error: function (jqXHR, error, errorThrown) {
                        $('#uploadStatus').html(JSON.parse(jqXHR.responseText).message);
                    },
                    success: function (res) {
                        $('#uploadStatus').html('');

                        if (res.status) {
                            $('#uploadForm')[0].reset();
                            $('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
                            //location.reload(false);
                            $('#videoUploadModal').modal('hide');


                        } else {
                            $('#uploadStatus').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
                        }
                    }
                });
            });


        })
    </script>
@endsection
