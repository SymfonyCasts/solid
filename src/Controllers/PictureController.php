<?php

namespace Sasquatch\Controllers;

use Sasquatch\Repositories\PictureRepository;
use Sasquatch\Services\PictureRenderer;
use Sasquatch\Services\UploadManager;
use Sasquatch\Services\WebSiteRenderer;
use Sasquatch\Models\Picture;

class PictureController
{
    public function index(): string
    {
        $contents = '
        <p>All reported Sasquatch sightings, we will find the truth, upload your image to help us find Bigfoot!</p>
        <a class="btn btn-info btn-lg" href="upload">Upload your Bigfoot Image</a>
        <div class="my-5 row">';

        $pictureRenderer = new PictureRenderer();
        $pictureRepository = new PictureRepository(__DIR__ . '/../../picture_info');
        foreach ($pictureRepository->findAll() as $picture) {
            $contents .= $pictureRenderer->render($picture);
        }

        $contents .= '</div>';

        return (new WebSiteRenderer())->renderPage('All Reported Sightings', $contents);
    }

    public function upload(): string
    {
        $webSiteRenderer = new WebSiteRenderer();
        if ('post' !== strtolower($_SERVER['REQUEST_METHOD'])) {

            return $webSiteRenderer->renderUploadForm();
        }

        // Process form
        if (!array_key_exists('newPicture', $_FILES) || !array_key_exists('author', $_POST)) {
            http_send_status(400);

            return $webSiteRenderer->renderPage('New sighting', "<p>Sorry... I didn't understand :(...</p><p>Wanna <a href='upload'>try again?</a></p>");
        } else {
            $uploadManager = new UploadManager();
            $pictureRepository = new PictureRepository(__DIR__ . '/../../picture_info');
            try {
                $newPicture = new Picture(
                    $_POST['author'],
                    new \DateTimeImmutable(),
                    $_POST['location'],
                    $uploadManager->storeUploadedFile($_FILES['newPicture'])
                );

                $pictureRepository->save($newPicture);

                return $webSiteRenderer->renderPage('New sighting', '
            <h2>Sasquatch Spotted! </h2>
            <p>Click <u><a class="text-white" href="/">here</a></u> to see your image and the rest of the sightings!</p>'
                );
            } catch (\Exception $e) {
                error_log(__FILE__ . ': ' . $e->getMessage());

                return $webSiteRenderer->renderPage('New sighting!', '
        <h2 class="mb-4 text-center">Sneaky like a Sasquatch, that upload didn\'t work!</h2>
        <p class="text-center"><a class="btn btn-light btn-lg" href="upload">Try Again</a></p>
        '
                );
            }
        }
    }

    public function show(string $fileName): string
    {
        return file_get_contents(__DIR__ . '/../../uploads/' . $fileName);
    }

    public function api()
    {
        $repo = new PictureRepository();

        $ret = [];
        foreach ( $repo->findAll() as $picture ) {
            $ret[] = [
                'author' => $picture->getAuthor(),
                'date' => $picture->getDate()->format('Y/m/d'),
                'location' => $picture->getLocation(),
                'data' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/show?file={$picture->getFileName()}",
            ];
        }

        return json_encode( $ret );
    }
}
