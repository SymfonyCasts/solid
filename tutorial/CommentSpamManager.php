<?php

namespace App\Manager;

use App\Entity\Comment;

class CommentSpamManager
{
    public function validate(Comment $comment)
    {
        $content = $comment->getContent();
        $badWordsOnComment = [];

        $regex = implode('|', $this->spamWords());

        preg_match("/$regex/", $content, $badWordsOnComment);

        if (count($badWordsOnComment) >= 2) {
            // We could throw a custom exception if needed
            throw new \RuntimeException('Message detected as spam');
        }
    }

    private function spamWords(): array
    {
        return [
            'follow me',
            'twitter',
            'facebook',
            'earn money',
            'SymfonyCats',
        ];
    }
}
