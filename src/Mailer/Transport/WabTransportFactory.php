<?php

namespace App\Mailer\Transport;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportInterface;

#[AutoconfigureTag('mailer.transport_factory')]
final class WabTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();

        if (!\in_array($scheme, $this->getSupportedSchemes(), true)) {
            throw new UnsupportedSchemeException($dsn, 'wmailer', $this->getSupportedSchemes());
        }

        $host = $dsn->getHost();
        $port = $dsn->getPort();
        $apiToken = $this->getUser($dsn);

        return (new WabApiTransport($apiToken, $host, $this->client, $this->dispatcher, $this->logger))->setPort($port);
    }

    protected function getSupportedSchemes(): array
    {
        return ['wmailer', 'wmailer+api'];
    }
}
