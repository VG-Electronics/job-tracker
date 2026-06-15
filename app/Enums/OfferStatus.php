<?php

namespace App\Enums;

enum OfferStatus: string
{
    case New = 'new';
    case Ignored = 'ignored';
    case LowSalary = 'low_salary';
    case Interested = 'interested';
    case Applied = 'applied';
    case Rejected = 'rejected';
    case Interview = 'interview';
    case Offer = 'offer';
}
