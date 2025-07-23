<?php

namespace Codefog\EventsSubscriptionsBundle;

use Contao\CoreBundle\Exception\ResponseException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

class ExporterHelper
{
    public function __construct(private readonly MimeTypeGuesserInterface $mimeTypeGuesser)
    {
    }

    public function download(\SplFileInfo $fileInfo, string $fileName): void
    {
        $response = new BinaryFileResponse($fileInfo->getPathname());
        $response->setPrivate(); // public by default
        $response->setAutoEtag();
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        $response->headers->addCacheControlDirective('must-revalidate');
        $response->headers->set('Connection', 'close');
        $response->headers->set('Content-Type', $this->mimeTypeGuesser->guessMimeType($fileInfo->getPathname()));

        throw new ResponseException($response);
    }
}
