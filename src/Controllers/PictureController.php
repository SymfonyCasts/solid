<?php

namespace Sasquatch\Controllers;

use Sasquatch\Models\Picture;
use Sasquatch\Services\WebSiteRenderer;

/**
 * Class PictureController
 * @package Sasquatch\Controllers
 */
class PictureController
{
    /**
     * Index action
     * @return string
     */
    public function index(): string
    {
        $contents = '
        <p>All reported Sasquatch sightings, we will find the truth, upload your image to help us find Bigfoot!</p>
        <a class="btn btn-info btn-lg" href="upload">Upload your Bigfoot Image</a>
        <div class="my-5 row">';

        foreach (Picture::findAll() as $picture) {
            $contents .= $picture->render();
        }

        $contents .= '</div>';

        return (new WebSiteRenderer())->renderPage('All Reported Sightings', $contents);
    }

    /**
     * Upload action
     * @return string
     */
    public function upload(): string
    {
        $renderer = new WebSiteRenderer();
        if ('post' !== strtolower($_SERVER['REQUEST_METHOD'])) {
            return $renderer->renderUploadForm();
        }

        // Process form
        if (!array_key_exists('newPicture', $_FILES) || !array_key_exists('author', $_POST)) {
            http_send_status(400);

            return $renderer->renderPage('New sighting', "<p>Sorry... I didn't understand :(...</p><p>Wanna <a href='upload'>try again?</a></p>");
        } else {
            try {
                $newPicture = Picture::createFromUpload(
                    $_FILES['newPicture'],
                    new \DateTimeImmutable(),
                    $_POST['author'],
                    $_POST['location'],
                );
                $newPicture->save();

                return $renderer->renderPage('New sighting', '
            <h2>Sasquatch Spotted! </h2>
            <p>Click <u><a class="text-white" href="/">here</a></u> to see your image and the rest of the sightings!</p>'
                );
            } catch (\Exception $e) {
                error_log(__FILE__.': '.$e->getMessage());

                return $renderer->renderPage('New sighting!', '
        <h2 class="mb-4 text-center">Sneaky like a Sasquatch, that upload didn\'t work!</h2>
        <p class="text-center"><a class="btn btn-light btn-lg" href="upload">Try Again</a></p>
        '
                );
            }
        }
    }

    /**
     * @param string $fileName
     * @return string
     * Show action
     */
    public function show(string $fileName): string
    {
        return file_get_contents(__DIR__.'/../../uploads/'.$fileName);
    }
}