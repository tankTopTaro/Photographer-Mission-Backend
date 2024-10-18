<?php

namespace App\Http\Controllers;

use App\Mail\AlbumAccessMail;
use App\Mail\TestEmail;
use App\Models\Album;
use App\Models\Capture;
use App\Models\OneTimeToken;
use App\Models\Photobooth;
use App\Models\Remote;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function index()
    {
        $venues = Venue::all();
        $photobooths = Photobooth::all();
        $albums = Album::all();
        $remotes = Remote::all();
        $users = User::all();
        $captures = Capture::all();

        // Get all album IDs with 'longterm' status
        $longtermAlbumIds = $albums->where('status', 'longterm')->pluck('id')->toArray();

        // Filter out users who are not linked to albums with 'longterm' status
        $liveUsers = User::whereNotIn('album_id', $longtermAlbumIds)->get();

        // Get the live albums
        $liveAlbums = Album::where('status', 'live')->pluck('id');

        // Fetch the original users (users with the smallest id for each album)
        $albumOwners = User::whereIn('album_id', $liveAlbums)
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MIN(id)'))
                    ->from('users')
                    ->groupBy('album_id');
            })
            ->get();

        // Get all remote IDs that are already linked to an album
        $linkedRemoteIds = Album::where('status', 'live')->pluck('remote_id')->toArray();

        // Filter out remotes that are already linked to an album
        $availableRemotes = Remote::whereNotIn('id', $linkedRemoteIds)->get();

        return response()->json([
            'venues' => $venues,
            'remotes' => $remotes,
            'availableRemotes' => $availableRemotes,
            'liveUsers' => $liveUsers,
            'albumOwners' => $albumOwners,
            'photobooths' => $photobooths,
            'albums' => $albums,
            'users' => $users,
            'captures' => $captures,
        ]);
    }

    /**
     * Update the status of an album.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'album_id' => 'required|exists:albums,id',
            'status' => 'required|in:live,longterm',
        ]);

        // Find the album using the album_id from the request
        $album = Album::find($request->album_id);

        // Check if status is changing from "live" to "longterm"
        if($album->status === 'live' && $request->status === 'longterm' && $album->remote_id && $album->venue_id) {
            $album->date_over = now();
            
            // Detach all remotes associated with the album
            $album->remotes()->detach();
        }

        // Update the album status
        $album->status = $request->status;

        // Save the album
        $album->save();

        // Send email to all users connected to this album
        if($request->status === 'longterm') {   // Only send email if status is longterm
            $users = User::where('album_id', $album->id)->get();    // Get all users connected to this album

            // Loop through each user and send an email
            foreach ($users as $user) {
                // Generate a token for the user
                $salt = env('SALT');
                $tokenString = $salt . $album->id . $user->id;
                $token = hash('sha256', $tokenString);

                // Generate the album access link
                // $albumUrl = route('show', ['albumId' => $album->id, 'userId' => $user->id, 'token' => $token]);
                $albumUrl = "https://photographer-mission.io/photographer/album/{$album->id}/user/{$user->id}/{$token}";

                // Send an email to the user with the album access link
                Mail::to($user->email)->send(new AlbumAccessMail($albumUrl, $user));
            }
        }

        return response()->json(['success' => true, 'message' => 'Album status updated successfully!']);
    }

    public function checkTokenStatus ($albumId, $token) { 
        $onetimeToken = OneTimeToken::where('token', $token)
            ->where('album_id', $albumId)
            ->first();

        if(!$onetimeToken) {
            return response()->json([
                'error' => 'Token not found'
            ], 404);
        }

        return response()->json([
            'used' => $onetimeToken->used
        ], 200);
    }
}
