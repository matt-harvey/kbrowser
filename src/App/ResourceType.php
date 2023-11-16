<?php

namespace App;

enum ResourceType
{
    case DAEMON_SET;
    case DEPLOYMENT;
    case NAMESPACE;
    case POD;
    case STATEFUL_SET;

    public function smallTitle(): string
    {
        return match ($this) {
            self::DAEMON_SET => 'daemonset',
            self::DEPLOYMENT => 'deployment',
            self::NAMESPACE => 'namespace',
            self::POD => 'pod',
            self::STATEFUL_SET => 'statefulset',
        };
    }

    public function title(): string
    {
        return match ($this) {
            self::DAEMON_SET => 'DaemonSet',
            self::DEPLOYMENT => 'Deployment',
            self::NAMESPACE => 'Namespace',
            self::POD => 'Pod',
            self::STATEFUL_SET => 'StatefulSet',
        };
    }

    public function pluralSmallTitle(): string
    {
        return $this->smallTitle() . 's';
    }

    public function pluralTitle(): string
    {
        return $this->title() . 's';
    }
}