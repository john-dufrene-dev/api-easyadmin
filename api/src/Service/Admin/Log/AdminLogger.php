<?php

namespace App\Service\Admin\Log;

use Psr\Log\LoggerInterface;
use App\Entity\Monitoring\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

final class AdminLogger
{
    public const ADMIN_CONNECTION_CONNET = 'admin_connection';
    public const ADMIN_CONNECTION_LEVEL_NAME = 'NOTICE';
    public const ADMIN_CONNECTION_LEVEL = 1;
    public const ADMIN_CONNECTION_MESSAGE = 'Admin Connection';

    public const ADMIN_ACTION_CONNET = 'admin_action';
    public const ADMIN_ACTION_ACTION = 'NO ACTION';
    public const ADMIN_ACTION_LEVEL_NAME = 'NOTICE';
    public const ADMIN_ACTION_LEVEL = 1;
    public const ADMIN_ACTION_MESSAGE = '';

    /**
     * logger
     *
     * @var mixed
     */
    protected $logger;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * request
     *
     * @var mixed
     */
    protected $request;

    /**
     * security
     *
     * @var mixed
     */
    protected $security;

    /**
     * __construct
     *
     * @param  mixed $adminLogger
     * @return void
     */
    public function __construct(
        LoggerInterface $adminLogger,
        EntityManagerInterface $em,
        RequestStack $request,
        Security $security
    ) {
        $this->logger = $adminLogger;
        $this->em = $em;
        $this->request = $request;
        $this->security = $security;
    }

    /**
     * adminConnection
     *
     * @return void
     */
    public function adminConnection(
        bool $active = false,
        string $level_name = self::ADMIN_CONNECTION_LEVEL_NAME,
        float $level = self::ADMIN_CONNECTION_LEVEL,
        string $message = self::ADMIN_CONNECTION_MESSAGE,
        array $extra = [],
        array $context = [self::ADMIN_CONNECTION_CONNET]
    ): void {

        if (null !== $this->security->getUser()) {
            $message = $message . " - " . $this->security->getUser()->getEmail();
        }

        if (-1 === $level && isset($extra['exception'])) {
            $extra['exception'] = $extra['exception'];
        }

        $extra = $this->processRecord($extra);

        if ($active) {

            $logEntry = new Log();

            $logEntry->setMessage($message);
            $logEntry->setLevel($level);
            $logEntry->setLevelName($level_name);
            $logEntry->setExtra($extra);
            $logEntry->setContext($context);

            $this->em->persist($logEntry);
            $this->em->flush();
        }

        $this->logVerbosity($level, $message, $extra);
    }

    /**
     * adminAction
     *
     * @return void
     */
    public function adminAction(
        bool $active = false,
        string $action = self::ADMIN_ACTION_ACTION,
        ?string $entity = null,
        string $level_name = self::ADMIN_ACTION_LEVEL_NAME,
        float $level = self::ADMIN_ACTION_LEVEL,
        string $message = self::ADMIN_ACTION_MESSAGE,
        array $extra = [],
        array $context = [self::ADMIN_ACTION_CONNET]
    ): void {

        if (null !== $this->security->getUser()) {
            $message = $action . ' - '  . $entity . ' - '  . $message . $this->security->getUser()->getEmail();
        }

        if (-1 === $level && isset($extra['exception'])) {
            $extra['exception'] = $extra['exception'];
        }

        $extra = $this->processRecord($extra);

        if ($active) {

            $logEntry = new Log();

            $logEntry->setMessage($message);
            $logEntry->setLevel($level);
            $logEntry->setLevelName($level_name);
            $logEntry->setExtra($extra);
            $logEntry->setContext($context);

            $this->em->persist($logEntry);
            $this->em->flush();
        }

        $this->logVerbosity($level, $message, $extra);
    }

    /**
     * @param array $record
     * @return array
     */
    public function processRecord(array $record): array
    {
        $req = $this->request->getCurrentRequest();

        $record['extra']['client_ip']       = $req->getClientIp();
        $record['extra']['client_port']     = $req->getPort();
        $record['extra']['uri']             = $req->getUri();
        $record['extra']['method']          = $req->getMethod();

        return $record;
    }

    /**
     * logVerbosity
     *
     * @param  mixed $level
     * @param  mixed $message
     * @param  mixed $extra
     * @return void
     */
    public function logVerbosity(float $level, string $message, array $extra): void
    {
        switch ($level) {
            case -1:
                $this->logger->error($message, [$extra]);
                break;
            case 1:
                $this->logger->notice($message, [$extra]);
                break;
            case 2:
                $this->logger->info($message, [$extra]);
                break;
            case 3:
                $this->logger->debug($message, [$extra]);
                break;
            default:
                $this->logger->info($message, [$extra]);
                break;
        }
    }
}
