<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bucket;
use App\Models\Ball;

class BucketController extends Controller
{
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string',
            'capacity' => 'required|integer',
        ]);

        Bucket::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'empty_volume' => $request->capacity,
        ]);

        return redirect()->route('home')->with('success', 'Bucket created successfully.');
    }

    public function home(){
        $data = [];
        $data['buckets'] = Bucket::all();
        $data['balls'] = Ball::all();
        return view('welcome', $data);
    }

    public function suggest(Request $request)
    {
        // echo "<pre>";
        $colors = $request->color;
        $where = [];
        foreach ($colors as $key => $color) {
            if (empty($color) && $color !== 0) {
                unset($colors[$key]);
            }else{
                $where[] = $key;
            }
        }

        $balls = Ball::whereIn('color', $where)->get();

        $totalVolume = 0;
        $totalBallVolume = [];
        $totalBallSize = [];
        $totalBalls = [];
        foreach($balls as $ball){
            $totalBalls[$ball->color] = [
                'totalSize' => $colors[$ball->color] * $ball->size,
                'bollSize' => $ball->size,
                'totalBalls' => $colors[$ball->color]
            ];
            
            // $totalBallSize[$ball->color] = $ball->size;
            $totalVolume += $colors[$ball->color] * $ball->size;

        }
        // print_r($totalBallVolume)
        $buckets = Bucket::orderBy('empty_volume', 'desc')->get();

        $ballPlacements = [];
        // echo "<pre>";
        foreach($buckets as $bucketKey => $bucket){
            if ($totalVolume <= 0) {
                break; 
            }

            $empty_volume =  $bucket->empty_volume;
            foreach ($totalBalls as $ballKey => $ball) {
                // echo $ball['totalSize']. "balls <br>";
                if(!$empty_volume){
                    break;
                }
                if(!$ball['totalSize']){
                    continue;
                }

                if($ball['totalSize'] <= $empty_volume){
                    $ballPlacements[$bucket->name][] = [
                        'bucket' => $bucket->name,
                        'volume' => $ball['totalBalls'],
                        'color' => $ballKey
                    ];
                    $totalBalls[$ballKey]['totalBalls'] = 0;
                    $totalBalls[$ballKey]['totalSize'] = 0;
                    $empty_volume = $empty_volume - $ball['totalSize'];
                    $totalVolume = $totalVolume - $ball['totalSize'];
                }else{
                    // echo $ball['totalSize'] - $empty_volume . " dd <br>";
                    $newBalls = explode('.', $empty_volume/$ball['bollSize']);
                    if(!$newBalls[0]){
                        continue;
                    }

                    $ballPlacements[$bucket->name][] = [
                        'bucket' => $bucket->name,
                        'volume' => $newBalls[0],
                        'color' => $ballKey
                    ];

                    $totalBalls[$ballKey]['totalBalls'] = $totalBalls[$ballKey]['totalBalls']-$newBalls[0];
                    $totalBalls[$ballKey]['totalSize'] = $totalBalls[$ballKey]['totalSize']-($newBalls[0]*$ball['bollSize']);
                    $empty_volume = $empty_volume - ($newBalls[0]*$ball['bollSize']);
                    $totalVolume = $totalVolume - ($newBalls[0]*$ball['bollSize']);

                    // $newBalls = explode('.', '7');
                    // echo $empty_volume/$ball['bollSize'] . " dd <br>";
                    // print_r($newBalls[0]*);
                }
            }
            
            // print_r($totalBalls);
            // echo "Bucket ".$bucket->name." empty_volume ".$empty_volume." Color $key Ball $ball totalvol $totalVolume ballSize $totalBalls<br>";
            
        }
        // echo $empty_volume ." empty_volume <br>";
        // echo $totalVolume." totalVolume <br>";
        // print_r($ballPlacements);
        if($totalVolume > 0){
            return redirect()->back()->with('error', 'you need to add more buckets');
        }
        $data['ballPlacements'] = $ballPlacements;
        $data['colors'] = $colors;
        return redirect()->back()->with('data', $data);


        die();
        
        $ballPlacements = [];
        foreach($buckets as $bucketKey => $bucket){

            if ($totalVolume <= 0) {
                break; 
            }

            $empty_volume =  $bucket->empty_volume;
            foreach($totalBallVolume as $key => $ball){
                echo "Bucket ".$bucket->name." empty_volume ".$empty_volume." Color $key Ball $ball totalvol $totalVolume ballSize $totalBallSize[$key]<br>";

                // echo $key .'  Space ' .$empty_volume ." ".$bucket->name. " ball :". $ball." Total : ".$totalVolume."<br>";
                if(!$empty_volume){
                    break;
                }
                if(!$ball){
                    continue;
                }
                $empty_volume = $empty_volume - $ball;
                // echo $empty_volume/$totalBallSize[$key] ."new <br>" ;
                if ($empty_volume >= 0) {
                    if(is_float($ball/$totalBallSize[$key])){
                        echo $ball ." ball<br>";
                        echo $totalBallSize[$key] ." totalBallSize<br>";
                        echo $ball/$totalBallSize[$key] ." total <br>";

                        // echo $ball ." ball<br>";
                        echo $totalBallSize[$key] -$ball ." totalBallSize<br>";
                        // echo $ball/$totalBallSize[$key] ." total <br>";
                        // $ball - $totalBallSize[$key];
                    }else{
                        $totalVolume = $totalVolume - $ball;
                        $ballPlacements[$bucket->name][] = [
                            'bucket' => $bucket->name,
                            'volume' => $ball/$totalBallSize[$key],
                            'color' => $key
                        ];
                        $totalBallVolume[$key] = 0;
                        $buckets[$bucketKey]->empty_volume =  $buckets[$bucketKey]->empty_volume - $ball;
                    }


                }else{
                    

                    // var_dump($totalBallSize[$key]);
                    // echo $totalBallSize[$key]/$empty_volume." dd";
                    $negetive = abs($empty_volume);
                    if(($empty_volume) < 0 ){

                        // if(is_float(($ball - $negetive)/$totalBallSize[$key])){
                        //     $pendingBalls = ($ball - $negetive)-$totalBallSize[$key];
                        //     $ballPlacements[$bucket->name][] = [
                        //         'bucket' => $bucket->name,
                        //         'volume' => $pendingBalls,
                        //         'color' => $key . " else float"
                        //     ];
                        //     // echo ($ball - $negetive)-$totalBallSize[$key];
                        //     // echo ($ball - $negetive)/$totalBallSize[$key]. "float";
                        //     // echo $empty_volume ." empty";
                        //     // echo $ball ." ball";
                        //     // $empty_volume = $empty_volume + $ball;
                        //     echo $negetive. " negetive <br>";
                        //     echo $pendingBalls. " pendingBalls <br>";
                        //     echo $totalBallSize[$key]. " totalBallSize <br>";
                        //     $totalBallVolume[$key] = $negetive - ($pendingBalls * $totalBallSize[$key]);
                        //     $empty_volume = ($pendingBalls * $totalBallSize[$key]);
                        //     $buckets[$bucketKey]->empty_volume =  $buckets[$bucketKey]->empty_volume - ($pendingBalls * $totalBallSize[$key]);

                        //     echo $buckets[$bucketKey]->empty_volume ." buckets empty_volume<br>"  ;
                        //     echo $totalBallVolume[$key] ." totalBallVolume<br>"  ;
                        //     echo $empty_volume ." empty_volume<br>"  ;

                        //     continue;
                        // }
                        $totalVolume = $totalVolume - ($ball - $negetive);
                        $ballPlacements[$bucket->name][] = [
                            'bucket' => $bucket->name,
                            'volume' => ($ball - $negetive)/$totalBallSize[$key],
                            'color' => $key . " else"
                        ];
                        $totalBallVolume[$key] = $negetive;
                        $empty_volume = $negetive;
                        $buckets[$bucketKey]->empty_volume =  $buckets[$bucketKey]->empty_volume - $negetive;
                    }
                    break;
                }
            }

        }
        echo "<pre>";
        echo $totalVolume."<br>";

        print_r($ballPlacements);
        die();
        if($totalVolume > 0){
            return redirect()->back()->with('error', 'you need to add more buckets');
        }
        return redirect()->back()->with('data', $ballPlacements);
        // return view('welcome', ['data' => $ballPlacements]);


    }

}
