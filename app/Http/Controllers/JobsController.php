<?php

namespace App\Http\Controllers;

use App\Notification;
use App\OrderFile;
use Illuminate\Http\Request;
use App\JobCategory;
use App\JobSkill;
use App\Order;
use App\Skill;
use App\Bid;
use App\User;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use File;
use App\OrderSubmission;
use App\Rating;

class JobsController extends Controller
{
    public function post(Request $request)
    {
        $user = auth()->user();
        if($user == null) {
            return response()->json(['Unauthorised'=>'Not logged in yet'], 401);
        }

        if($user->type != 'Client') {
            return response()->json(['Unauthenticated'=>'Not a client'], 403);
        }

        $order = $user->jobs()->create([
            'topic' => $request->input('title'),
            'description' => $request->input('description'),
            'budget'=>$request->input('budget'),
            'bidding_instructions'=>$request->input('biddinginstructions'),
            'rating_required'=>$request->input('rating'),
            'deadline'=>$request->input('deadline'),
            'experience_required'=>$request->input('experiencelevel'),
        ]);









        $files_count =(int) request()->input('filescount');
        if( $files_count > 0) {
            $zip = new ZipArchive();

        $submissions = 0;
        $zip_name = 'Order_'.$order->id.'.zip';
        if (!file_exists(public_path().'/uploads/order-files')) {
            mkdir(public_path().'/uploads/order-files', 0777, true);
        }
        while(file_exists(public_path().'/uploads/order-files/'.$zip_name)) {
            $submissions++;
            $zip_name = 'Order_'.$order->id.'('.$submissions.').zip';
        }
        $zip->open(public_path().'/uploads/order-files/'.$zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if (!file_exists(public_path().'/uploads/order-files/Order_'.$order->id)) {
            mkdir(public_path().'/uploads/order-files/Order_'.$order->id, 0777, true);
        }
        for($i=0; $i<$files_count; $i++) {
            $file = request()->file('files'.$i);
            $file->move(public_path().'/uploads/order-files/Order_'.$order->id, $file->getClientOriginalName());
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(public_path().'/uploads/order-files/Order_'.$order->id),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file)
        {
            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen(public_path().'/uploads/order-files/Order_'.$order->id) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $order->files()->create([
            'path' => $zip_name
        ]);
        $zip->close();
        File::deleteDirectory(public_path().'\uploads\order-files\Order_'.$order->id);
        }













        $skills = $request->input('skills');
        $skills = json_decode($skills);

        foreach($skills as $skill){
            if(!$_skill = Skill::where('name', '=', $skill)->first()){
                $_skill = Skill::create(['name'=> $skill]);
            }
            $order->skills()->save(new JobSkill(['skill_id'=>$_skill->id]) );
        }
        return response()->json(['message'=>'Job posted sucessfully'], 200);

    }

    public function index()
    {
        $user = auth()->user();
        if($user == null) {
            return response()->json(['Unauthorised'=>'Not logged in yet'], 401);
        }

        if($user->type != 'Freelancer') {
            return response()->json(['Unauthenticated'=>'Not a Freelancer'], 403);
        }

        $jobs = Order::where('status', '=', 'Unassigned')->get();
       // $jobs = Order::all();
         foreach($jobs as $job) {
             $job->specialities = $job->specialities;
             $job->files = $job->files;
         }
         return \response()->json($jobs);
    }

    public function message($id)
    {
        $freelancer = \auth()->user();
        $order = Order::findOrFail($id);
        $order->messages()->create([
            'source'=> $freelancer->id,
            'destination'=> $order->client_id,
            'message'=>\request()->input('content')
        ]);

        return \response()->json(['message'=>'Message sent'],200);
    }

    public function bid($id)
    {
        $freelancer = auth()->user();

        $order = Order::findOrFail($id);
        $order->bids()->create([
            'freelancer_id' => $freelancer->id,
            'amount' => \request()->input('amount')
        ]);
        return response()->json(['message' => 'Bid placed successfully!'],200);
    }

    public function client_jobs()
    {
        $client = auth()->user();
        $orders = Order::where('client_id', '=', $client)->get();
        $orders = Order::all();
        return \response()->json($orders);
    }

    public function job($id) {
        if($job = Order::find($id)){
            $skills = [];
            $experience = (int) $job->experience_required;
            if($experience == 1) {
                $experience = 'Beginner';
            }else if($experience == 2) {
                $experience = 'Intermediate';
            }else if($experience == 3) {
                $experience = 'Expert';
            }
            $_skills = $job->skills;
            foreach($_skills as $_skill) {
                if($skill = Skill::find($_skill->skill_id)) {
                    array_push($skills, $skill->name);
                }
            }
            $_files = $job->files;


            $job = [
                'id'=> $job->id,
                'available' => $job->status == 'Unassigned',
                'topic'=> $job->topic,
                'description'=> $job->description,
                'deadline'=> $job->deadline,
                'topic'=> $job->topic,
                'rating'=>$job->rating_required,
                'experience' =>$experience,
                'budget'=>$job->budget,
                'instructions'=>$job->bidding_instructions,
                'skills'=> $skills,
                'files' =>$_files
            ];
        }
        return $job == null ? response()->json(['message'=> 'Job not found'],404) : response()->json($job);
    }

    public function get_freelancer_jobs_inprogress() {
        $user = auth()->user();
        $orders = Order::where('freelancer_id', '=', $user->id)->where('status', '=', 'Incomplete')->get();
        return \response()->json($orders,200);
    }

    public function find_jobs() {
        $user = auth()->user();
        $rating = $user->account->rating;
        $experience = $user->freelancer_information->experience_level;
        $jobs = Order::where('status', '=', 'Unassigned')->where('rating_required', '<=', $rating)->where('experience_required','<=', $experience )->get();
        return response()->json($jobs, 200);
    }

    public function get_freelancer_jobs_completed() {
        $user = auth()->user();
        $orders = Order::where('freelancer_id', '=', $user->id)->where('status', '=', 'Complete')->get();
        return \response()->json($orders,200);
    }

    public function get_freelancer_jobs_cancelled() {
        $user = auth()->user();
        $orders = Order::where('freelancer_id', '=', $user->id)->where('status', '=', 'Cancelled')->get();
        return \response()->json($orders,200);
    }

    public function get_freelancer_jobs_bidded() {
        $user = auth()->user();
        $bids = Bid::where('freelancer_id', '=', $user->id)->get();
        $orders = [];
        foreach($bids as $bid) {
            $order = $bid->order;

            if($order->status == 'Unassigned') {
                $order->bid_amount = $bid->amount;
                array_push($orders, $order);
            }
        }
        return response()->json($orders, 200);
    }

    function get_client_jobs_in_bidding() {
        $user = \auth()->user();
        $jobs = Order::where('client_id', '=', $user->id)->where('status','=','Unassigned')->get();
        foreach($jobs as $job) {
            $job->bids = sizeof($job->bids);
        }
        return response()->json($jobs,200);
    }

    function get_bids($id) {
        $job = Order::find($id);
        if($job == null){
            return response()->json(['message'=>'No such job'],404);
        }
        $bids = $job->bids;
        foreach($bids as $bid) {
            //$bid->freelancer->firstname.' '.$bid->freelancer->lastname
            $bid->freelancer = $bid->get_freelancer_name();
        }
        return response()->json($bids,200);
    }


    public function assign()
    {
        $freelancer = User::find(\request()->input('freelancer'));
        $bid = Bid::where('freelancer_id', '=', $freelancer->id)->first();
        if($freelancer) {
            $job = Order::find(\request()->input('orderid'));
            $job->freelancer_id = $freelancer->id;
            $job->status = 'Incomplete';
            $job->price = $bid->amount;
            $job->save();
            return response()->json(['message'=>'Assigned successfully'],200);
        }
        return response()->json(['error'=>'Could not assign the ojob'],400);
    }

    public function get_client_jobs_in_progress()
    {
        $user = \auth()->user();
        $jobs = Order::where('client_id', '=', $user->id)->where('status', '=', 'Incomplete')->get();
        foreach($jobs as $job) {
            $job->submissions;
            $job->freelancer;
        }
        return \response()->json($jobs,200);
    }

    public function get_client_jobs_cancelled()
    {
        $user = \auth()->user();
        $jobs = Order::where('client_id', '=', $user->id)->where('status', '=', 'Cancelled');
        return \response()->json($jobs);
    }

    public function get_client_jobs_complented()
    {
        $user = \auth()->user();
        $jobs = Order::where('client_id', '=', $user->id)->where('status', '=', 'Completed');
        return \response()->json($jobs);
    }

    public function upload_submission()
    {
        $files_count =(int) request()->input('filescount');
        if( $files_count < 1) {
            return response()->json(['error'=>'Please upload a file'], 400);
        }
        $order = Order::find((int) request()->input('orderid'));
        $zip = new ZipArchive();

        $submissions = 0;
        $zip_name = 'Order_'.$order->id.'.zip';
        if (!file_exists(public_path().'/uploads/submissions')) {
            mkdir(public_path().'/uploads/submissions', 0777, true);
        }
        while(file_exists(public_path().'/uploads/submissions/'.$zip_name)) {
            $submissions++;
            $zip_name = 'Order_'.$order->id.'('.$submissions.').zip';
        }
        $zip->open(public_path().'/uploads/submissions/'.$zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // if ($zip->open(public_path().'/uploads/submissions/'.$zip_name, ZipArchive::CREATE) !== TRUE) {
        //     return response()->json(['error'=>'Upload failed'], 400);
        // }

        if (!file_exists(public_path().'/uploads/submissions/Order_'.$order->id)) {
            mkdir(public_path().'/uploads/submissions/Order_'.$order->id, 0777, true);
        }
        for($i=0; $i<$files_count; $i++) {
            $file = request()->file('files'.$i);
            $file->move(public_path().'/uploads/submissions/Order_'.$order->id, $file->getClientOriginalName());
            //$zip->addFile(request()->file('files'.$i));
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(public_path().'/uploads/submissions/Order_'.$order->id),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

       // $files_uploaded = scandir(public_path().'/uploads/submissions/Order_'.$order->id);
        // foreach($files_uploaded as $file_uploaded) {
        //     $zip->addFile(public_path().'/uploads/submissions/Order_'.$order->id.'/'.$files_count, $file_uploaded);
        // }
        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen(public_path().'/uploads/submissions/Order_'.$order->id) + 1);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        $order->submissions()->create([
            'path' => $zip_name
        ]);
        $zip->close();
        File::deleteDirectory(public_path().'\uploads\submissions\Order_'.$order->id);

        return response()->json(['message'=>'Submission uploaded successfully.'],200);
    }


    public function get_submissions($job_id)
    {
        $job = Order::find($job_id);
        if($job == null) {
            return \response()->json(['error'=>'No such job'], 404);
        }

        return \response()->json($job->submissions, 200);
    }

    public function download_submissions($id)
    {
        $submission = OrderSubmission::find($id);
        if($submission == null) {
            return \response()->json(['error'=>'No such submission'], 404);
        }
        $headers = array(
            'Content-Type: application/octet-stream',
         );
         return response()->download(public_path().'/uploads/submissions/'.$submission->path, $submission->path, $headers);
    }

    public function download_job_file($id) {
        $file = OrderFile::find($id);
        if($file == null) {
            return \response()->json(['error'=>'No such file'], 404);
        }
        $headers = array(
            'Content-Type: application/octet-stream',
        );
        return response()->download(public_path().'/uploads/order-files/'.$file->path, $file->path, $headers);
    }


    public function update_rating() {
        $order_id = \request()->input('order_id');
        $user_id = \request()->input('user_id');
        $rating = \request()->input('rating');
        $comment = \request()->input('comment');

        $order = Order::find($order_id);
        $user = User::find($user_id);
        if($order && $user) {
            $_rating = Rating::where('order_id', '=', $order_id)->where('user_id', '=', $user_id)->first();

            if ($_rating) {
                $_rating->comment = $comment;
                $_rating->rating = $rating;
                $_rating->save();
            }else {
                $order->ratings()->save(
                    new Rating([
                        'user_id'=> $user_id,
                        'order_id'=> $order_id,
                        'rating'=> $rating,
                        'comment'=>$comment
                    ])
                );
            }

            $ratings =$user->ratings;
            $_total = 0;
            $count = 0;
            foreach ($ratings as $r) {
                $_total+=$r->rating;
                $count++;
            }
            $rating = $_total == 0 ? $_total : $_total/$count;
            $user->account->rating = $rating;

            $user->account->available_balance = $user->account->available_balance + $order->price;
            auth()->user()->account->pending_balance = auth()->user()->account->pending_balance - $order->price;

            $user->account->save();
            auth()->user()->account->save();
            $order->status = 'Complete';
            $user->notifications()->save(
                new Notification([
                    'message'=>'Your order id '.$order_id.' has been paid (:!'
                ])
            );
            auth()->user()->notifications()->save(
                new Notification([
                    'message'=>'Your order id '.$order_id.' is now complete. Thank you for using online worksite!'
                ])
            );
            return response()->json($_rating, 200);
        }
    }




}
