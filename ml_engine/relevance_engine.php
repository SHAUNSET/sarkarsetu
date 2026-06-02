<?php
namespace SarkarSetu\ML;

class RelevanceEngine {
    private $themes = [];

    public function __construct($themesFile) {
        $this->loadThemes($themesFile);
    }

    private function loadThemes($file) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            list($cat, $keys) = explode(':', $line);
            $this->themes[trim($cat)] = array_map('trim', explode(',', $keys));
        }
    }

    public function getScore($text) {
        $score = 0;
        foreach ($this->themes as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    $score++;
                }
            }
        }
        return $score;
    }
}