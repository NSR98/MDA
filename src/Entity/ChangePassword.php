<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @Assert\Length(min="1", max="30")
     */
    private $old_password;

    /**
     * @Assert\Length(min="1", max="30")
     */
    private $new_password;


    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->old_password;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword): void
    {
        $this->old_password = $oldPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->new_password;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword): void
    {
        $this->new_password = $newPassword;
    }
}