<?php

namespace App\Controller;

use App\Repository\Submission\SubmissionPager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractController extends BaseAbstractController {
    protected function submissionPage(string $sortBy, Request $request): array {
        return SubmissionPager::getParamsFromRequest($sortBy, $request);
    }

    /**
     * @param string       $id
     * @param string|mixed $token
     *
     * @throws BadRequestHttpException if the token isn't valid
     */
    protected function validateCsrf(string $id, $token): void {
        if (!\is_string($token) || !$this->isCsrfTokenValid($id, $token)) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }
    }
}
