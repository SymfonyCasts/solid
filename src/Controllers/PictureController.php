<?php

namespace sasquatch\Controllers;

use sasquatch\Models\Picture;
use sasquatch\Repositories\PictureRepository;
use sasquatch\Services\PictureRenderer;
use sasquatch\Services\Renderer;

class PictureController
{
    private $renderer;

    public function __construct( Renderer $renderer )
    {
        $this->renderer = $renderer;
    }

    public function index() : string
    {
        $contents = '
        <p>All reported Sasquatch sightings, we will find the truth, upload your image to help us find Bigfoot!</p>
        <a class="btn btn-info btn-lg" href="upload">Upload your Bigfoot Image</a>
        <div class="my-5 row">';

        $pictureRenderer = new PictureRenderer();
        foreach ( Picture::findAll() as $picture ) {
            $contents .= $pictureRenderer->render($picture);
        }

        $contents .= '</div>';
        
        return $this->renderer->renderPage( 'All Reported Sightings', $contents );
    }

    public function upload() : string
    {
        if ( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ) {
            // Process form
            if ( !array_key_exists('newPicture', $_FILES) || !array_key_exists('author', $_POST) ) {

                http_send_status( 400 );

                return $this->renderer->renderPage( 'New sighting',"<p>Sorry... I didn't understand :(...</p><p>Wanna <a href='upload'>try again?</a></p>");
            } else {
                try {
                    $newPicture = Picture::createFromUpload(
                        $_FILES['newPicture'],
                        new \DateTimeImmutable(),
                        $_POST['author'],
                        $_POST['location'],
                    );
                    $repo = new PictureRepository();
                    $repo->save( $newPicture );

                    return $this->renderer->renderPage('New sighting', '
                <h2>Sasquatch Spotted! </h2>
                <p>Click <u><a class="text-white" href="/">here</a></u> to see your image and the rest of the sightings!</p>'
                    );
                } catch ( \Exception $e ) {
                    error_log( __FILE__.': '.$e->getMessage() );

                    return $this->renderer->renderPage('New sighting!', '
            <h2 class="mb-4 text-center">Sneaky like a Sasquatch, that upload didn\'t work!</h2>
            <p class="text-center"><a class="btn btn-light btn-lg" href="upload">Try Again</a></p>
            '
                    );
                }
            }
        } else {

            return $this->renderer->renderUploadForm();
        }
    }

    public function show( string $fileName ) : string
    {
        return file_get_contents( __DIR__.'/../../uploads/'.$fileName );
    }
}