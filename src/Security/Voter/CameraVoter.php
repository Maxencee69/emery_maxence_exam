<?php

namespace App\Security\Voter;

use App\Entity\Camera;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CameraVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Camera;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Camera $camera */
        $camera = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($camera, $user);
            case self::DELETE:
                return $this->canDelete($camera, $user);
        }

        return false;
    }

    private function canEdit(Camera $camera, UserInterface $user): bool
    {
        // Seul le propriétaire ou un admin peut éditer
        return $camera->getOwner() === $user || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canDelete(Camera $camera, UserInterface $user): bool
    {
        // Seul le propriétaire ou un admin peut supprimer
        return $camera->getOwner() === $user || in_array('ROLE_ADMIN', $user->getRoles());
    }
}
