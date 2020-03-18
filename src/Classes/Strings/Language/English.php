<?php

namespace Nonetallt\Helpers\Strings\Language;

class English
{
    CONST ALPHABET = [
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
    ];

    /**
     * Describe a list of items
     *
     */
    public static function listWords(string ...$items) : string
    {
        $result = '';

        foreach($items as $pos => $item) {

            /* Add article for the first item */
            if($pos === 0) {
                $article = static::article($item);
                $result .= "$article $item";
            }
            else {
                $result .= $item;
            }

            if($pos + 2 === count($items)) {
                /* Add 'and' after item if the next item is the last one*/ 
                $result .= ' and ';
            }
            else if($pos + 1 < count($items)) {
                /* Add comma after item */
                $result .= ', ';
            }
        }

        return $result;
    }

    /**
     * Get the article for a word
     *
     */
    public static function article(string $word) : string
    {
        return in_array(strtolower(substr($word, 0, 1)), static::vowels()) ? 'an' : 'a';
    }

    /**
     * Get all characters in the alphabet
     *
     */
    public static function alphabet(bool $includeUppercase = false) : array
    {
        $result = static::ALPHABET;

        if($includeUppercase) {
            $result += array_map(function($letter) {
                return strtoupper($letter);
            }, static::ALPHABET);
        }

        return $result;
    }

    /**
     * Get all vowels in the alphabet
     *
     */
    public static function vowels(bool $includeUppercase = false) : array
    {
        $letters = [
            'a',
            'e',
            'i',
            'o',
            'u',
            // sometimes consonant?
            'y',
        ];

        if($includeUppercase) {
            $uc = [];
            foreach($letters as $letter) {
                $uc[] = strtoupper($letter);
            }
            $letters = array_merge($leters, $uc);
        }

        return $letters;
    }

    /**
     * Get all consonants in the alphabet
     *
     */
    public static function consonants(bool $includeUppercase = false) : array
    {
        $letters = [
            'b',
            'c',
            'd',
            'f',
            'g',
            'h',
            'j',
            'k',
            'l',
            'm',
            'n',
            'p',
            'q',
            'r',
            's',
            't',
            'w',
            'x',
            'z'
        ];

        if($includeUppercase) {
            $uc = [];
            foreach($letters as $letter) {
                $uc[] = strtoupper($letter);
            }
            $letters = array_merge($leters, $uc);
        }

        return $letters;
    }
}
