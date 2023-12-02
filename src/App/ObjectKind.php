<?php

namespace App;

enum ObjectKind: string
{
    case CONFIG_MAP = 'ConfigMap';
    case CRON_JOB = 'CronJob';
    case DAEMON_SET = 'DaemonSet';
    case DEPLOYMENT = 'Deployment';
    case ENDPOINT_SLICE = 'EndpointSlice';
    case INGRESS = 'Ingress';
    case JOB = 'Job';
    case NAMESPACE = 'Namespace';
    case NODE = 'Node';
    case POD = 'Pod';
    case STATEFUL_SET = 'StatefulSet';
    case REPLICA_SET = 'ReplicaSet';
    case SECRET = 'Secret';
    case SERVICE = 'Service';

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
        if ($this === self::INGRESS) {
            return 'ingresses';
        }
        return $this->smallTitle() . 's';
    }

    public function pluralTitle(): string
    {
        if ($this === self::INGRESS) {
            return 'Ingresses';
        }
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
