# Photographer-Backend
    - Made using Laravel 11 Breeze API
    - It provides the API routes needed to run the app

  ### __Routes__
  __AdminController__

    /api/admin
      - collects and returns various data from the database, primarily for administrative purposes

    /api/check-token/{albumId}/{token}
      - checks whether a token associated with a particular album has been used
      - its primarily used to check if the invite from the email has been used

    /admin-update
      - allows for updating the status of an album

  __CaptureController__

    /photographer-capture
      - responsible for handling the uploading of multiple image files to an album and saving the file paths in the database

  __AlbumController__

    /photographer-store
      - creates a new album and user, then sends an email to the user with a link to access the album
    
    /photographer-invite
      - invites a user to access an album by creating a user and generating a one-time token

    /photographer/friend-invite
      - invites a friend to access an album by generating a one-time token and sending an email with the album link

    /photographer/email-update
      - updates the email address of a user associated with an album

    /photographer/album/{albumId}/user/{userId}/{token}
      - displays the album and user information if the provided token is valid

  __DownloadController__

    /photographer/album-download
      - handles zipping and preparing all the photos from a specific album for download

    /photographer/download/album/{albumId}/user/{userId}/file/{fileName}
      - allows downloading an individual zip file from the album


### Emails

  You can customise the email template if you edit the  album_invitation.blade.php and album_access.blade.php

  Just be sure to also maintain the AlbumInvitationMail.php and AlbumAccessMail.php in the Mail directory.


### Adding Pictures to users

  Right now you can only add pictures to each user manually.
  It's possible to automatically seed the database with some dummy pictures, but I didn't do it.
  Since I think that this app would definitely be empty at start, and it will be filled up when a user visit a venue and have pictures of them taken.