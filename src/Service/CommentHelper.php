<?php

namespace App\Service;

use App\Entity\User;

class CommentHelper
{
    public function countRecentCommentsForUser(User $user): int
    {
        $comments = $user->getComments();
        $commentCount = 0;
        $recentDate = new \DateTimeImmutable('-3 months');
        foreach ($comments as $comment) {
            if ($comment->getCreatedAt() > $recentDate) {
                $commentCount++;
            }
        }

        return $commentCount;
    }
}
