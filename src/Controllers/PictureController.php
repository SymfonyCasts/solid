<?php

namespace sasquatch\Controllers;

use sasquatch\Models\Picture;

class PictureController
{
    public function index()
    {
        $contents = "
        <table>
";
        foreach ( Picture::findAll() as $picture ) {
            $contents .= "
<tr>
    <td>
        <img src='uploads/{$picture->getPath()}'/>
    </td>
    <td>
        By: {$picture->getAuthor()}
    </td>
    <td>
        At: {$picture->getLocation()}
    </td>
    <td>
        On: {$picture->getDate()->format('Y/m/d')}
    </td>
</tr>";
        }

        $contents .= "
        </table>
    </body>
</html>
       ";

        return $this->render( $contents );
    }

    private function render( string $contents )
    {
        return preg_replace( '/{contents}/', $contents, $this->getLayout() );
    }

    private function getLayout()
    {
        return "
<html>
    <body>
        <h1>Sasquatch pictures</h1>
        {contents}
    </body>
</html>
        ";
    }

    public function upload()
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

    private function storeUploadedPicture(array $uploadedFile, string $author, string $location )
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

            return "<h2>File uploaded! Yoohoo!</h2><p>Click <a href='/'>here</a> to see it together with the rest!</p>";
        } else {

            return "<h2>Sorry dude... Your pic was not uploaded :(</h2><p>Wanna <a href='upload'>try again?</a></p>";
        }
    }

    private function showForm()
    {
        return "
<form method=\"post\" enctype='multipart/form-data'>
    <p><label for='file'>Show us what you got!</label><input type='file' id='file' name='newPicture'/></p>
    <p><label for='author'>What's your name friend?</label><input type='text' id='author' name='author'/></p>
    <p><label for='location'>Where did you take this pic?</label><input type='text' id='location' name='location'/></p>
    <input type='submit' value='Share it with the world!'/>
</form>
        ";
    }

    private function error400()
    {
        http_send_status( 400 );

        return $this->render( "<p>Sorry... I didn't understand :(...</p><p>Wanna <a href='upload'>try again?</a></p>");
    }

    public function show()
    {
        return file_get_contents( __DIR__.'/../../uploads/'.$_GET['file'] );
    }
}