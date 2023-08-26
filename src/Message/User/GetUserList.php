<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Message\QueryInterface;
use Doctrine\Common\Collections\Criteria;

final readonly class GetUserList implements QueryInterface
{
    public function __construct(
        public Criteria $criteria = new Criteria(
            orderings: ['emailAddress' => Criteria::ASC]
        )
    ) {
    }
}