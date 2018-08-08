<?php

namespace TheAentMachine\AentTraefik\Command;

use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Aenthill\CommonEvents;
use TheAentMachine\Command\AbstractJsonEventCommand;
use TheAentMachine\Exception\AenthillException;
use TheAentMachine\Question\CommonValidators;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

class NewVirtualHostEventCommand extends AbstractJsonEventCommand
{
    protected function getEventName(): string
    {
        return CommonEvents::NEW_VIRTUAL_HOST_EVENT;
    }

    /**
     * @param mixed[] $payload
     * @return array|null
     * @throws ServiceException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $service = Service::parsePayload($payload);
        $serviceName = $service->getServiceName();
        $virtualHosts = $service->getVirtualHosts();

        if (empty($virtualHosts)) {
            throw new AenthillException('In Traefik image, no virtualhosts passed in service.');
        }

        $this->output->writeln("You are about to <info>configure the domain name</info> of the service <info>$serviceName</info> in the reverse proxy (Traefik).");

        $service->addLabel('traefik.enable', 'true');

        $baseDomainName = Aenthill::metadata('BASE_DOMAIN_NAME');

        foreach ($virtualHosts as $key => $virtualHost) {
            $virtualPort = (string) $virtualHost['port'];
            $comment = $virtualHost['comment'] ?? '';

            if (isset($virtualHost['host'])) {
                $virtualHost = $virtualHost['host'];
            } elseif (isset($virtualHost['hostPrefix'])) {
                $virtualHost = $virtualHost['hostPrefix'].$baseDomainName;
            } else {
                $virtualHost = $this->getAentHelper()->question('What is the domain name of this service?')
                    ->compulsory()
                    ->setDefault($serviceName.$baseDomainName)
                    ->setValidator(CommonValidators::getDomainNameValidator())
                    ->ask();
            }

            $this->output->writeln("<info>Adding host redirection from '$virtualHost' to service '$serviceName' on port '$virtualPort'</info>");

            $service->addLabel('traefik.s'.$key.'.backend', $serviceName);
            $service->addLabel('traefik.s'.$key.'.frontend.rule', 'Host:' . $virtualHost, (string) $comment);
            $service->addLabel('traefik.s'.$key.'.port', $virtualPort);
        }

        return $service->jsonSerialize();
    }
}
