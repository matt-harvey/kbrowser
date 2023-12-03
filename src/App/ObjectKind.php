<?php

declare(strict_types=1);

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

    public function isNamespaced(): bool
    {
        return match ($this) {
            self::NAMESPACE, self::NODE => false,
            default => true,
        };
    }

    public function makeTable(bool $includeNamespace): Table
    {
        switch ($this) {
        case self::NAMESPACE:
        case self::NODE:
            return Table::create()->add(Column::fromJsonPath('Name', 'metadata.name', 'name'));
        case self::POD:
        case self::JOB:
        case self::CRON_JOB:
            $table = Table::create();
            if ($includeNamespace) {
                $table->add(Column::fromJsonPath('Namespace', 'metadata.namespace', 'namespace'));
            }
            return $table
                ->add(Column::fromJsonPath('Name', 'metadata.name', 'name'))
                ->add(Column::fromJsonPath('Status', 'status.phase', 'status'));
        default:
            $table = Table::create();
            if ($includeNamespace) {
                $table->add(Column::fromJsonPath('Namespace', 'metadata.namespace', 'namespace'));
            }
            return $table->add(Column::fromJsonPath('Name', 'metadata.name', 'name'));
        }
    }
}
