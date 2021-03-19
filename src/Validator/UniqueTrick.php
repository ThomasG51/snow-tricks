<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueTrick extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = '* Ce trick existe deja';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }


}
