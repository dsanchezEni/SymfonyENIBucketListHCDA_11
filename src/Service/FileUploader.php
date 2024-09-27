<?php

namespace App\Service;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
final class FileUploader
{
    private readonly string $targetDirectory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->targetDirectory = $parameterBag->get('app.images_wish_directory');
    }


    public function upload(UploadedFile $file): string
    {
        $fileName = uniqid().'.'.$file->guessExtension();
        try{
            $file->move($this->getTargetDirectory(), $fileName);
        }catch (FileException $e){
            throw new FileException($e);
        }
        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function delete(?string $fileName, string $rep): void{
        if(null != $fileName){
            if(file_exists($rep.'/'.$fileName)){
                unlink($rep.'/'.$fileName);
            }
        }
    }
}