<?php
/**
 * Created by PhpStorm.
 * User: Ch'Mohamed
 * Date: 5/21/2020
 * Time: 4:31 PM
 */

namespace App\Entity;


interface PublishDateEntityInterface
{
    public function setPublished(\DateTimeInterface $published): PublishDateEntityInterface;
}