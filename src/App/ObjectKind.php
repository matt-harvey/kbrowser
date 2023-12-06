<?php

declare(strict_types=1);

namespace App;

use Carbon\Carbon;

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
        $nameColumn = Column::fromJsonPath(
            $this->title(),
            'metadata.name',
            'name',
            function (string $context, mixed $dataSource): string {
                $name = $dataSource['metadata']['name'];
                if ($this == self::NAMESPACE) {
                    return Route::forNamespace($context, $name)->toUrl();
                }
                if ($this->isNamespaced()) {
                    $namespace = $dataSource['metadata']['namespace'];
                    return Route::forNamespacedResource($context, $this, $name, $namespace)->toUrl();
                }
                return Route::forNonNamespacedResource($context, $this, $name)->toUrl();
            },
        );

        $namespaceColumn = Column::fromJsonPath(
            'Namespace',
            'metadata.namespace',
            'namespace',
            function (string $context, mixed $dataSource): string {
                $namespace = $dataSource['metadata']['namespace'];
                return Route::forNamespace($context, $namespace)->toUrl();
            },
        );

        $statusColumn = Column::fromJsonPath('Status', 'status.phase', 'status');
        $createdColumn = new Column('Created', 'created', function (mixed $dataSource): string {
            $isoCreatedAt = $dataSource['metadata']['creationTimestamp'];
            $createdAt = Carbon::parse($isoCreatedAt);
            return $createdAt->diffForHumans();
        });

        $table = Table::create();
        if ($includeNamespace && $this->isNamespaced()) {
            $table->add($namespaceColumn);
        }

        return match ($this) {
            self::NAMESPACE, self::POD =>
                $table
                    ->add($nameColumn)
                    ->add($statusColumn)
                    ->add($createdColumn),
            self::CONFIG_MAP, self::SECRET =>
                $table
                    ->add($nameColumn)
                    ->add(new Column('Data', 'data', function (mixed $dataSource): string {
                        return \strval(\count($dataSource['data'] ?? []));
                    }))
                    ->add($createdColumn),
            default =>
                $table
                    ->add($nameColumn)
                    ->add($createdColumn)

        };
    }
}
