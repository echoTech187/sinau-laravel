<?php

namespace App\Enums;

enum ManifestResult: string
{
    case PASS = 'pass';
    case PASS_WITH_NOTE = 'pass_with_note';
    case FAIL = 'fail';
    case NOT_APPLICABLE = 'n_a';
}
