<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ball and Bucket</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" >
    <style>
        .all_buckets, .all_balls{
            display: flex;
        }
        .single_bucket>div{
            position: relative;
        }
        .single_bucket>div span{
            position: absolute;
            left: 0;
            bottom: 50px;
            right: 0;
            text-align: center;
            font-size: 23px;
        }
        .single_bucket h5{
            text-align: center;
        }
        .all_balls .ball {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: auto;
        }
        .all_balls .single_ball{
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
            // print_r($buckets);
        ?>  
        <div class="all_buckets">
            @foreach($buckets as $bucket)
                <div class="single_bucket">
                    <div>
                        <img width="200px" src="/assets/bucket.png">
                        <span>{{$bucket->name}}</span>
                    </div>
                    <h5>{{$bucket->capacity}} cubic inches</h5>
                </div>
            @endforeach
        </div>

        <div class="all_balls">
            @foreach($balls as $ball)
                <div class="single_ball">
                    <div>
                        <div style="background-color: {{$ball->color}}" class="ball"></div>
                    </div>
                    <span>{{$ball->color}}</span>
                    <h5></h5>
                    <h5>{{$ball->size}} cubic inches</h5>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-6">
                <h3 class="">Bucket Form</h3>
                <form action="{{ route('buckets.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="name">Bucket name:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Bucket name">
                    </div>
                    <div class="form-group">
                        <label for="capacity">Volume (in Inches):</label>
                        <input type="number" class="form-control" id="capacity" name="capacity">
                    </div>
                    <button type="submit" class="btn btn-primary">Create bucket</button>
                </form>
            </div>
            <div class="col-md-6">
                <h3 class="">Ball Form</h3>
                <form action="{{ route('balls.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="color">Ball Name:</label>
                        <input type="text" class="form-control" id="color" name="color" placeholder="Ball Name">
                    </div>
                    <div class="form-group">
                        <label for="size">Volume (in Inches):</label>
                        <input type="text" class="form-control" id="size" name="size">
                    </div>
                    <button type="submit" class="btn btn-primary">Create ball</button>
                </form>
            </div>
            <div class="col-md-6 ">
                <h3 class="">Bucket Suggestion</h3>
                <form action="{{ route('suggest') }}" class="" method="post">
                    @csrf
                    @foreach($balls as $ball)
                        <div class="form-group row">
                            <label for="input{{$ball->color}}" class="col-sm-2 col-form-label">{{$ball->color}}</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="color[{{$ball->color}}]" id="input{{$ball->color}}">
                            </div>
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary">Create ball</button>
                </form>
            </div>

            {{-- @if(session('data')) --}}
                <div class="col-md-6 ">
                    <h3 class="">Result</h3>
                    @if(session('error'))
                        <p>{{session('error')}}</p>
                    @endif
                    @if(session('data'))
                        <ul>
                            @foreach(session('data') as $key => $data)
                                <li>Bucket {{$key}}: Place 
                                    @php $comma = false; @endphp
                                    @foreach($data as $ball)
                                        {{($comma) ? "," : ""}} {{$ball['volume']}} {{$ball['color']}} balls
                                        @php $comma = true; @endphp
                                    @endforeach
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            {{-- @endif --}}

        </div>
    </div>
</body>
</html>