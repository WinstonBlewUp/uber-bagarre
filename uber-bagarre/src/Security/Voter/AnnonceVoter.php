<?php

namespace App\Security\Voter;

use App\Entity\Annonce;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AnnonceVoter extends Voter
{
    public const EDIT = 'annonce_edit';
    public const DELETE = 'annonce_delete';
    public const VALIDATE_PARTICIPATION = 'annonce_validate';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VALIDATE_PARTICIPATION])
            && $subject instanceof Annonce;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            dump("Utilisateur non authentifié");
            return false;
        }

        /** @var Annonce $annonce */
        $annonce = $subject;

        return match ($attribute) {
            self::EDIT, self::DELETE, self::VALIDATE_PARTICIPATION => $user->getId() === $annonce->getCreatedBy()->getId(),
            default => false,
        };
    }
}
