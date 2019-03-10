<?php

namespace App\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CommentDenormalizer implements ContextAwareNormalizerInterface, ContextAwareDenormalizerInterface, SerializerAwareInterface {
    /**
     * @var DenormalizerInterface|NormalizerInterface
     */
    private $decorated;

    /**
     * @var IriConverterInterface
     */
    private $iriConverter;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        NormalizerInterface $decorated,
        IriConverterInterface $iriConverter,
        TokenStorageInterface $tokenStorage
    ) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException('poop');
        }

        $this->decorated = $decorated;
        $this->iriConverter = $iriConverter;
        $this->tokenStorage = $tokenStorage;
    }

    public function denormalize($data, $class, $format = null, array $context = []) {
        if ($class === Comment::class) {
            $data['user'] = $this->iriConverter->getIriFromItem(
                $this->tokenStorage->getToken()->getUser()
            );

            $context['groups'][] = 'inject_user';
        }

        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []) {
        if (!$this->decorated instanceof ContextAwareDenormalizerInterface) {
            return $this->decorated->supportsDenormalization($data, $type, $format);
        }

        return $this->decorated->supportsDenormalization($data, $type, $format, $context);
    }

    public function supportsNormalization($data, $format = null, array $context = []) {
        return $this->decorated->normalize($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []) {
        if (!$this->decorated instanceof ContextAwareNormalizerInterface) {
            return $this->decorated->supportsNormalization($object, $format);
        }

        return $this->decorated->supportsNormalization($object, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer) {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
