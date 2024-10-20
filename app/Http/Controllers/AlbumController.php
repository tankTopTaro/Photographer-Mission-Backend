<?php

namespace App\Http\Controllers;

use App\Mail\AlbumAccessMail;
use App\Mail\AlbumInvitationMail;
use App\Mail\TestEmail;
use App\Models\Album;
use App\Models\Capture;
use App\Models\OneTimeToken;
use App\Models\Remote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlbumController extends Controller
{
    /**
     * Store a new album and user, then send an email 
     * to the user with a link to access the album.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {    
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'remote_id' => 'required|exists:remotes,id',   
        ]);

        // Find the remote using the remote_id from the request
        $remote = $request->remote_id ? Remote::findOrFail($request->remote_id) : null;

        // Get the venue_id from the remote
        $venue_id = $remote->venue_id;

        // Create a new album with the remote_id, venue_id and status = 'live'
        $album = Album::create([
            'remote_id' => $remote ? $remote->id : null,
            'venue_id' => $venue_id,
            'status' => 'live',
        ]);

        // Create a new user with the name, email, and album_id
        $user =User::create([
            'name' => $request->name,
            'email' => $request->email,
            'album_id' => $album->id,   // Link the user to the album
        ]);

        // Generate a token for the user
        $salt = env('SALT');
        $tokenString = $salt . $album->id . $user->id;
        $token = hash('sha256', $tokenString);

        // Generate the album access link
        // $albumUrl = route('show', ['albumId' => $album->id, 'userId' => $user->id, 'token' => $token]);
        $albumUrl = "https://photographer-mission.io/photographer/album/{$album->id}/user/{$user->id}/{$token}";

        // Send an email to the user with the album access link
        Mail::to($user->email)->send(new AlbumAccessMail($albumUrl, $user));


        return response()
            ->json([
                'message' => 'Album and User created successfully!', 
                'token' => $token, 
                'albumId' => $album->id, 
                'userId' => $user->id], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Display the album and user information if the token is valid.
     * 
     * The token is generated by hashing the 
     *      album ID, user ID, and a secret salt.
     * 
     * The token is checked against the expected token 
     *      generated from the album and user IDs.
     * 
     * If the token is valid, the user is redirected 
     *      to the album page with a cookie that contains the user token.
     * 
     * If the token is invalid, the user is redirected 
     *      to the home page with an error message.
     * 
     * @param int $albumId The ID of the album
     * @param int $userId The ID of the user
     * @param string $token The token to check against the expected token
     * 
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function show ($albumId, $userId, $token) {
        Log::info('Show method triggered with params:', [
            'albumId' => $albumId,
            'userId' => $userId,
            'token' => $token   
        ]);

        // Find the album and user by their IDs
        $album = Album::findOrFail($albumId);
        $user = User::findOrFail($userId);

        // Generate a token for the user
        $salt = env('SALT');
        $expectedTokenString = $salt . $album->id . $user->id;
        $expectedToken = hash('sha256', $expectedTokenString);
        $isValid = $expectedToken === $token;

        Log::info('Expected token:', [$expectedToken, 'isValid' => $isValid]);
        Log::info('Received token:', [$token]);

        if ($isValid) {
            // Get all captures associated with the album
            $captures = Capture::where('album_id', $album->id)->get();
            $cookieToken = hash('sha256', $album->id . $user->id);

            return response()->json([
                'album' => $album,
                'user' => $user,
                'captures' => $captures,
                'cookieToken' => $cookieToken
            ], 200)->withCookie(cookie('user_token', $cookieToken, 60 * 24 * 7))
            ->header('Access-Control-Allow-Origin', '*');;
        } else {
            return response()->json(['message' => 'Invalid token.'], 401);
        }
    }

    /**
     * Invite a user to access an album.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteUser (Request $request) {
        $request->validate([
            'album_id' => 'required|exists:albums,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'token' => 'required|string'
        ]);

        // Find the album using the album_id from the request
        $album = Album::findOrFail($request->album_id);

        // Find the token
        $oneTimeToken = OneTimeToken::where('album_id', $request->album_id)
            ->where('token', $request->token)
            ->first();

        // If the token does not exist, create it
        if (!$oneTimeToken) {
            $oneTimeToken = new OneTimeToken();
            $oneTimeToken->album_id = $request->album_id;
            $oneTimeToken->email = $request->email;
            $oneTimeToken->token = $request->token;
            $oneTimeToken->used = false; // Initially, the token is not used
            $oneTimeToken->save(); // Save the new token
        } else {
            // If it exists, you may want to check if it's already used
            if ($oneTimeToken->used) {
                return response()->json(['error' => 'Token has already been used'], 400); // Handle used token case
            }
        }

        $oneTimeToken->used = true;
        $oneTimeToken->save();

        // Create a new user with the name, email, and album_id
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'album_id' => $album->id,   // Link the user to the album
        ]);

        // Generate a token for the user
        $salt = env('SALT');
        $tokenString = $salt . $album->id . $user->id;
        $token = hash('sha256', $tokenString);

        // Generate the album access link
        // $albumUrl = route('show', ['albumId' => $album->id, 'userId' => $user->id, 'token' => $token]);
        $albumUrl = "https://photographer-mission.io/photographer/album/{$album->id}/user/{$user->id}/{$token}";

        // Send an email to the user with the album access link
        Mail::to($user->email)->send(new AlbumAccessMail($albumUrl, $user));

        return response()
            ->json([
                'message' => 'User invited successfully!',
                'token' => $token, 
                'albumId' => $album->id, 
                'userId' => $user->id,
            ], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

/**
 * Invite a friend to access an album by sending an email with an access link.
 * 
 * This function validates the request data, generates a token, creates an 
 * album access link, and sends an invitation email to the specified email address.
 * 
 * @param \Illuminate\Http\Request $request The request object containing email 
 * and album_id.
 * 
 * @return \Illuminate\Http\JsonResponse A JSON response indicating the success 
 * of the invitation.
 */
    public function inviteFriend (Request $request) {

        $request->validate([ 
            'email' => 'required|email|max:255',
            'album_id' => 'required|exists:albums,id'
        ]);

        // Generate a token for the user
        $salt = env('SALT');
        $randomId = rand(1000, 9999);
        $tokenString = $salt . $request->album_id . $randomId;
        $token = hash('sha256', $tokenString);

        // Store the token as unused
        OneTimeToken::create([
            'token' => $token,
            'album_id' => $request->album_id,
            'used' => false,
            'email' => $request->email
        ]);

        // Generate the album access link
        $albumUrl = "https://photographer-mission.io/photographer/album/{$request->album_id}/{$token}";

        // Send an email to the user with the album access link
        Mail::to($request->email)->send(new AlbumInvitationMail($albumUrl));

        return response()
            ->json(['message' => 'Friend successfully invited!'], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Update the email address of a user.
     * 
     * This function validates the request data, finds the user using the user_id,
     * and updates the email address.
     * 
     * @param \Illuminate\Http\Request $request The request object containing user_id
     * and email.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success
     * of the update.
     */
    public function updateEmail (Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'album_id' => 'required|exists:albums,id',
            'email' => 'required|email|max:255',
        ]);

        $user = User::findOrFail($request->user_id);

        $user->email = $request->email;
        $user->save();

        return response()->json(['message' => 'Email updated successfully!'], 200)
            ->header('Access-Control-Allow-Origin', '*');
    }
}
