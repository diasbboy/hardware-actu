<?php

namespace App\MesServices;


use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class HandleImage
{
    protected $slugger;
    protected $containerBag;

    public function __construct(SluggerInterface $slugger, ContainerBagInterface $containerBag)
    {
        $this->slugger = $slugger;
        $this->containerBag = $containerBag;
    }

    public function saveImage($imageFile, object $object)
    {
        $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($originalFileName);
        $uniqFileName = $safeFileName .'-' . uniqid() .'.'. $imageFile->guessExtension();

        $imageFile->move(
            $this->containerBag->get('app_images_directory'),
            $uniqFileName
        );

        $object->setImage('/uploads/'.$uniqFileName);

    }

    public function editImage($imageFile, object $object, $vintageImage)
    {
        $this->saveImage($imageFile, $object);
    }

    public function deleteImage($vintageImage)
    {
        if($vintageImage)
        {
            $pathToVintageImage = $this->containerBag->get('app_images_directory')."/..". $vintageImage;

            if(file_exists($pathToVintageImage))
            {
                unlink($pathToVintageImage);
            }
        }
    }
}