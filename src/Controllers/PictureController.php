<?php

namespace sasquatch\Controllers;

use sasquatch\Models\Picture;

class PictureController
{
    public function index() : string
    {
        $contents = '
        
        <h1>All Reported Sightings</h1>
        <p>All reported Sasquatch sightings, we will find the truth, upload your image to help us find Bigfoot!</p>
        <a class="btn btn-info btn-lg" href="upload">Upload your Bigfoot Image</a>
        <div class="my-5 row">
        
';
        foreach ( Picture::findAll() as $picture ) {
            $contents .= "
            <div class='col-sm-4 mb-5'>
                <img src='show?file={$picture->getFileName()}' class='big-foot-img'/>
                <p class='mt-3 mb-0'>Image taken by: <strong>{$picture->getAuthor()}</strong></p> 
                <p class='mb-0'>Coordinates: <strong>{$picture->getLocation()}</strong></p>
                <p class='mb-0'><strong>{$picture->getDate()->format('d/m/Y')}</strong></p>
            </div>
        ";
        }

        $contents .= '
        </div>
       ';

        return $this->render( $contents );
    }

    private function render( string $contents ) : string
    {
        return preg_replace( '/{contents}/', $contents, $this->getLayout() );
    }

    private function getLayout() : string
    {
        return '
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/bigfoot.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 mb-5 top-nav">
                    <h1 class="mt-5"><img class="mr-3" src="img/bigfoot.png" alt="bigfoot icon" style="width:50px;height:65px;">Sasquatch Sightings</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    {contents}
                </div>
            </div>
            <div class="row">
                <div class="col-12 footer-nav">
                    <p class="pt-4">Made with <span class="text-danger"><3</span> by the guys and gals at <u><a class="text-white" href="https://symfonycasts.com">SymfonyCasts</a></u></p>
                </div>
            </div>
        </div>
    </body>
</html>
        ';
    }

    public function upload() : string
    {
        if ( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ) {
            // Process form
            if ( !array_key_exists('newPicture', $_FILES) || !array_key_exists('author', $_POST) ) {

                return $this->error400();
            } else {

                return $this->render(
                    $this->storeUploadedPicture(
                        $_FILES['newPicture'],
                        $_POST['author'],
                        $_POST['location']
                    )
                );
            }
        } else {

            return $this->render( $this->showForm() );
        }
    }

    private function storeUploadedPicture(array $uploadedFile, string $author, string $location ) : string
    {
        $destination = __DIR__ . '/../../uploads/' . basename($uploadedFile['name']);
        if ( move_uploaded_file( $uploadedFile['tmp_name'], $destination ) ) {
            $metaDataFileName = substr( $destination, 0, strrpos($destination, '.') ).'.json';

            $metaData = [
                'date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'author' => $author,
                'location' => $location,
            ];

            file_put_contents( $metaDataFileName, json_encode( $metaData ) );

            return '
            <h2>Sasquatch Spotted! </h2>
            <p>Click <u><a class="text-white" href="/">here</a></u> to see your image and the rest of the sightings!</p>';
        } else {

            return '
            <h2 class="mb-4 text-center">Sneaky like a Sasquatch, that upload didn\'t work!</h2>
            <p class="text-center"><a class="btn btn-light btn-lg" href="upload">Try Again</a></p>
            
            ';
        }
    }

    private function showForm() : string
    {
        return '
        <div class="row">
            <div class="col">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Add your Big Foot image</label>
                        <input class="form-control-file" type="file" id="file" name="newPicture"/>
                    </div>
                    <div class="form-group">
                        <label for="author">Your Name</label>
                        <input class="form-control" type="text" id="author" name="author"/>
                    </div>
                    <div class="form-group">
                        <label for="location">Coordinates of Sighting in Degrees (Lat Long)</label>
                        <input class="form-control" type="text" id="location" name="location" placeholder="48.5100000°, -121.2500000°"/>
                    </div>
                    <button class="btn btn-light btn-lg" type="submit">Share it with the world!</button>
                </form>
            </div>
        </div>
        ';
    }

    private function error400() : string
    {
        http_send_status( 400 );

        return $this->render( "<p>Sorry... I didn't understand :(...</p><p>Wanna <a href='upload'>try again?</a></p>");
    }

    public function show( string $fileName ) : string
    {
        return file_get_contents( __DIR__.'/../../uploads/'.$fileName );
    }
}