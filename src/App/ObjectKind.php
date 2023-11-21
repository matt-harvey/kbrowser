<?php

namespace App;

enum ObjectKind: string
{
    case NAMESPACE = 'Namespace';
    case POD = 'Pod';
    case DEPLOYMENT = 'Deployment';

    case REPLICA_SET = 'ReplicaSet';
    case DAEMON_SET = 'DaemonSet';
    case STATEFUL_SET = 'StatefulSet';
    case JOB = 'Job';
    case CRON_JOB = 'CronJob';

    case CONFIG_MAP = 'ConfigMap';

    case SECRET = 'Secret';

    case NODE = 'Node';

    public function smallTitle(): string
    {
        return \strtolower($this->value);
    }

    public function title(): string
    {
        return $this->value;
    }

    public function pluralSmallTitle(): string
    {
        return $this->smallTitle() . 's';
    }

    public function pluralTitle(): string
    {
        return $this->title() . 's';
    }

    public function isNamespaced(): string
    {
        return match ($this) {
            self::NAMESPACE, self::NODE => false,
            default => true,
        };
    }
}