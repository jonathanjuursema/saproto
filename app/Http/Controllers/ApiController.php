<?php

namespace App\Http\Controllers;

use App\Models\AchievementOwnership;
use App\Models\ActivityParticipation;
use App\Models\EmailListSubscription;
use App\Models\Feedback;
use App\Models\FeedbackCategory;
use App\Models\FeedbackVote;
use App\Models\OrderLine;
use App\Models\Photo;
use App\Models\PhotoLikes;
use App\Models\PlayedVideo;
use App\Models\RfidCard;
use App\Models\Token;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use stdClass;

class ApiController extends Controller
{
    /**
     * @return string
     */
    public function train(Request $request)
    {
        return stripslashes(file_get_contents('http://@ews-rpx.ns.nl/mobile-api-avt?station='.$_GET['station']));
    }

    /**
     * @param  string  $token
     * @return false|string
     */
    public function protubeAdmin($token)
    {
        $token = Token::where('token', $token)->first();

        $adminInfo = new stdClass();

        if (! $token) {
            $adminInfo->is_admin = false;
        } else {
            $user = $token->user;
            if (! $user) {
                $adminInfo->is_admin = false;
            } else {
                $adminInfo->user_id = $user->id;
                $adminInfo->user_name = $user->name;
                $adminInfo->calling_name = $user->calling_name;
                $adminInfo->is_admin = $user->can('protube') || $user->isTempadmin();
            }
        }

        return json_encode($adminInfo);
    }

    /** @return JsonResponse */
    public function protubeUserDetails()
    {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'authenticated' => true,
                'name' => $user->calling_name,
                'admin' => $user->hasPermissionTo('protube', 'web') || $user->isTempadmin(),
                'id' => $user->id,
            ]);
        }

        return response()->json(['authenticated' => false]);
    }

    public function protubePlayed(Request $request)
    {
        if ($request->secret != config('herbert.secret')) {
            abort(403);
        }

        $playedVideo = new PlayedVideo();
        $user = User::findOrFail($request->user_id);

        if ($user->keep_protube_history) {
            $playedVideo->user()->associate($user);
        }

        $playedVideo->video_id = $request->video_id;
        $playedVideo->video_title = urldecode($request->video_title);

        $playedVideo->save();

        PlayedVideo::where('video_id', $playedVideo->video_id)->update(['video_title' => $playedVideo->video_title]);
    }

    /**
     * @return JsonResponse
     */
    public function getToken(Request $request)
    {
        $response = new stdClass();

        if (Auth::check()) {
            $response->name = Auth::user()->name;
            $response->photo = Auth::user()->generateMediumPhotoPath();
            $response->token = Auth::user()->getToken()->token;
        } else {
            $response->token = 0;
        }

        if ($request->has('callback')) {
            return response()->json($response)->setCallback($request->input('callback'));
        } else {
            return response()->json($response);
        }
    }

    /**
     * @throws Exception
     */
    public function randomPhoto(): JsonResponse
    {
        $privateQuery = Photo::query()->where('private', false)->whereHas('album', function ($query) {
            $query->where('published', true)->where('private', false);
        });

        if (! $privateQuery->count()) {
            return response()->json(['error' => 'No public photos found!.'], 404);
        }

        $random = random_int(1, 100);
        if ($random > 0 && $random <= 30) { //30% chance the photo is from within the last year
            $query = (clone $privateQuery)->whereBetween('date_taken', [Carbon::now()->subYear()->timestamp, Carbon::now()->timestamp]);
        } elseif ($random > 30 && $random <= 55) { //25% chance the photo is from one year ago
            $query = (clone $privateQuery)->whereBetween('date_taken', [Carbon::now()->subYears(2)->timestamp, Carbon::now()->subYear()->timestamp]);
        } elseif ($random > 55 && $random <= 70) {//15% chance the photo is from two years ago
            $query = (clone $privateQuery)->whereBetween('date_taken', [Carbon::now()->subYears(3)->timestamp, Carbon::now()->subYears(2)->timestamp]);
        } elseif ($random > 70 && $random <= 80) {//10% chance the photo is from three years ago
            $query = (clone $privateQuery)->whereBetween('date_taken', [Carbon::now()->subYears(4)->timestamp, Carbon::now()->subYears(3)->timestamp]);
        } else {//20% chance the photo is older than 4 years
            $query = (clone $privateQuery)->where('date_taken', '<=', Carbon::now()->subYears(4)->timestamp);
        }
        $photo = $query->inRandomOrder()->with('album')->first();

        //        if we picked a year and therefore a query where no photos exist, pick a random public photo as fallback
        if (! $photo) {
            $photo = $privateQuery->inRandomOrder()->with('album')->first();
        }

        return response()->JSON([
            'url' => $photo->url,
            'album_name' => $photo->album->name,
            'date_taken' => Carbon::createFromTimestamp($photo->date_taken)->format('d-m-Y'),
        ]);
    }

    public function randomOldPhoto(): JsonResponse
    {
        $privateQuery = Photo::query()->where('private', false)->whereHas('album', function ($query) {
            $query->where('published', true)->where('private', false);
        })->where('date_taken', '<=', Carbon::now()->subYears(4)->timestamp);

        if (! $privateQuery->count()) {
            return response()->json(['error' => 'No public photos older than 4 years found!.'], 404);
        }

        $photo = $privateQuery->inRandomOrder()->with('album')->first();

        return response()->JSON([
            'url' => $photo->url,
            'album_name' => $photo->album->name,
            'date_taken' => Carbon::createFromTimestamp($photo->date_taken)->format('d-m-Y'),
        ]);
    }

    /** @return array */
    public function gdprExport()
    {
        $user = Auth::user();
        $data = [];

        $data['user'] = $user->makeHidden(['id', 'member', 'photo', 'address', 'bank']);

        $data['member'] = $user->is_member ? $user->member->makeHidden(['id', 'user_id']) : null;

        $data['address'] = $user->address ? $user->address->makeHidden(['user_id']) : null;

        $data['bank_account'] = $user->bank ? $user->bank->makeHidden(['id', 'user_id']) : null;

        foreach (RfidCard::where('user_id', $user->id)->get() as $rfid_card) {
            $data['rfid_cards'][] = [
                'card_id' => $rfid_card->card_id,
                'name' => $rfid_card->name,
                'added_at' => $rfid_card->created_at,
            ];
        }

        foreach (ActivityParticipation::where('user_id', $user->id)->get() as $activity_participation) {
            $data['activities'][] = [
                'name' => $activity_participation->activity?->event?->title,
                'date' => $activity_participation->activity?->event ? date('Y-m-d', $activity_participation->activity->event->start) : null,
                'was_present' => $activity_participation->is_present,
                'helped_as' => $activity_participation->help ? $activity_participation->help->committee->name : null,
                'backup' => $activity_participation->backup,
                'created_at' => $activity_participation->created_at,
                'updated_at' => $activity_participation->updated_at,
                'deleted_at' => $activity_participation->deleted_at,

            ];
        }

        foreach (OrderLine::where('user_id', $user->id)->get() as $orderline) {
            $payment_method = null;
            if ($orderline->payed_with_cash) {
                $payment_method = 'cash_cashier';
            } elseif ($orderline->payed_with_bank_card) {
                $payment_method = 'bank_card_cashier';
            } elseif ($orderline->molliePayment) {
                $payment_method = sprintf('mollie_%s', $orderline->molliePayment->mollie_id);
            } elseif ($orderline->withdrawal) {
                $payment_method = sprintf('withdrawal_%s', $orderline->withdrawal->id);
            }
            $data['orders'][] = [
                'product' => $orderline->product->name,
                'units' => $orderline->units,
                'total_price' => $orderline->total_price,
                'payed_with' => $payment_method,
                'order_date' => $orderline->created_at,
            ];
        }

        foreach (PlayedVideo::where('user_id', $user->id)->get() as $playedvideo) {
            $data['played_videos'][] = [
                'youtube_id' => $playedvideo->video_id,
                'youtube_name' => $playedvideo->video_title,
                'spotify_id' => $playedvideo->spotify_id != '' ? $playedvideo->spotify_id : null,
                'spotify_name' => $playedvideo->spotify_id != '' ? $playedvideo->spotify_name : null,
                'played_at' => $playedvideo->created_at,
            ];
        }

        foreach (EmailListSubscription::where('user_id', $user->id)->get() as $list_subscription) {
            $data['list_subscription'][] = $list_subscription->emaillist ? $list_subscription->emaillist->name : null;
        }

        foreach (AchievementOwnership::where('user_id', $user->id)->get() as $achievement_granted) {
            $data['achievements'][] = [
                'name' => $achievement_granted->achievement->name,
                'description' => $achievement_granted->achievement->desc,
                'granted_on' => $achievement_granted->created_at,
            ];
        }

        foreach (PhotoLikes::where('user_id', $user->id)->get() as $photo_like) {
            $data['liked_photos'][] = $photo_like->photo->url;
        }

        foreach (FeedbackCategory::all() as $category) {
            foreach (Feedback::where('user_id', $user->id)->where('feedback_category_id', $category->id)->get() as $feedback) {
                $data["placed_$category->url"][] = [
                    'feedback' => $feedback->feedback,
                    'created_at' => $feedback->created_at,
                    'accepted' => $feedback->accepted,
                    'reply' => $feedback->reply,
                ];
            }

            foreach (FeedbackVote::where('user_id', $user->id)->whereHas('feedback', function ($q) use ($category) {
                $q->where('feedback_category_id', $category->id);
            })->get() as $feedbackVote) {
                $data["liked_$category->url"][] = [
                    'feedback' => $feedbackVote->feedback->feedback,
                    'liked_at' => $feedbackVote->created_at,
                ];
            }
        }

        return $data;
    }
}
