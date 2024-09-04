<?php

namespace App\Security\Voter;

use App\Entity\User;  // Assure-toi que cette ligne est correcte
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    
    protected function supports(string $attribute, $subject): bool
    {
        // Détermine si l'attribut est supporté par ce voter
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;  // Corrige le type ici
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Ajoute la logique d'autorisation ici
        switch ($attribute) {
            case self::VIEW:
                // L'utilisateur peut voir le profil s'il est admin ou s'il s'agit de son propre profil
                return $this->canView($subject, $user);
            case self::EDIT:
                // L'utilisateur peut éditer son propre profil
                return $this->canEdit($subject, $user);
        }

        return false;
    }

    private function canView(User $user, UserInterface $currentUser): bool
    {
        // L'utilisateur peut voir son propre profil ou s'il est admin
        return $user === $currentUser || in_array('ROLE_ADMIN', $currentUser->getRoles());
    }

    private function canEdit(User $user, UserInterface $currentUser): bool
    {
        // L'utilisateur peut éditer son propre profil
        return $user === $currentUser;
    }
}
