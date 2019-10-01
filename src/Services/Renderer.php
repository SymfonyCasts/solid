<?php


namespace sasquatch\Services;

class Renderer
{
    public function renderPage( string $title, string $contents )
    {
        return $this->render('<h1>'.$title.'</h1>'.$contents );
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

    public function renderUploadForm() : string
    {
        return $this->render('
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
        ');
    }
}