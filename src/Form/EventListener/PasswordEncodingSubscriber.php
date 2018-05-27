<?php

namespace App\Form\EventListener;

use App\Form\Model\UserData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class PasswordEncodingSubscriber implements EventSubscriberInterface {
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function onPostSubmit(FormEvent $event) {
        if (!$event->getForm()->isValid()) {
            return;
        }

        /* @var UserData $user */
        $user = $event->getForm()->getData();

        if (!$user instanceof UserData) {
            throw new \UnexpectedValueException(
                'Form data must be instance of '.UserData::class
            );
        }

        if ($user->getPlainPassword() !== null) {
            $encoded = $this->encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return [
            FormEvents::POST_SUBMIT => ['onPostSubmit', -200],
        ];
    }
}
