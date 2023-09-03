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
        echo "<pre>";
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
        foreach($balls as $ball){
            $totalBallVolume[$ball->color] = $colors[$ball->color] * $ball->size;
            $totalBallSize[$ball->color] = $ball->size;
            $totalVolume += $colors[$ball->color] * $ball->size;

        }
        // print_r($totalBallVolume)
        $buckets = Bucket::orderBy('empty_volume', 'desc')->get();
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
