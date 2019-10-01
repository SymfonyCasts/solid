<?php


namespace sasquatch\Services;

use sasquatch\Models\Picture;

class PictureRenderer
{
    public static function render( Picture $picture )
    {
        return "                                                                                      
    <div class='col-sm-4 mb-5'>                                                               
        <img src='show?file={$picture->getFileName()}' class='big-foot-img'/>                    
        <p class='mt-3 mb-0'>Image taken by: <strong>{$picture->getAuthor()}</strong></p>        
        <p class='mb-0'>Coordinates: <strong>{$picture->getLocation()}</strong></p>              
        <p class='mb-0'>Date: <strong>{$picture->getDate()->format('d/m/Y')}</strong></p>        
    </div>                                                                                    
";
    }    
}