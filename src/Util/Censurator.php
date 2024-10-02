<?php

namespace App\Util;

class Censurator
{
    //Constante contenant un tableau de mot censuré.
    const UNWANTED_WORDS=["casino","viagra","bad","banana"];
    public function purify(?string $text):string
    {
         foreach (self::UNWANTED_WORDS as $unwantedWord){
             //Autant d'étoiles que de lettres dans le mot:
             $replacement = str_repeat("*",mb_strlen($unwantedWord));
             $text=str_ireplace($unwantedWord,$replacement,$text);
         }
         return $text;
    }
}