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
    case PERSISTENT_VOLUME = 'PersistentVolume';
    case PERSISTENT_VOLUME_CLAIM = 'PersistentVolumeClaim';
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
            self::NAMESPACE, self::NODE, self::PERSISTENT_VOLUME => false,
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


        $ownerKindColumn = new Column('Owner kind', 'ownerKind', function (mixed $dataSource): string {
            $ownerReferences = $dataSource['metadata']['ownerReferences'] ?? [];
            $ownerKinds = \array_map(fn ($arr) => $arr['kind'] ?? '- ', $ownerReferences);
            return \implode(', ', $ownerKinds);
        });

        $ownedByColumn = new Column('Owned by', 'ownedBy', function (mixed $dataSource): string {
            $ownerReferences = $dataSource['metadata']['ownerReferences'] ?? [];
            $owners = \array_map(fn ($arr) => $arr['name'], $ownerReferences);
            return \implode(', ', $owners);
        }, function (string $context, mixed $dataSource): ?string {
            $ownerReferences = $dataSource['metadata']['ownerReferences'] ?? [];
            if (\count($ownerReferences) != 1) {
                return null;
            }
            $owner = $ownerReferences[0];
            $ownerKind = ObjectKind::tryFrom($owner['kind'] ?? null);
            if ($ownerKind === null) {
                return null;
            }
            if ($ownerKind->isNamespaced()) {
                $namespace = $dataSource['metadata']['namespace'];
                return Route::forNamespacedResource($context, $ownerKind, $owner['name'], $namespace)->toUrl();
            }
            return Route::forNonNamespacedResource($context, $ownerKind, $owner['name'])->toUrl();
        });

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
            self::NAMESPACE =>
                $table
                    ->add($nameColumn)
                    ->add($statusColumn)
                    ->add($createdColumn),
            self::POD =>
                $table
                    ->add($nameColumn)
                    ->add($statusColumn)
                    ->add($ownerKindColumn)
                    ->add($ownedByColumn)
                    ->add($createdColumn),
            self::PERSISTENT_VOLUME =>
                $table
                    ->add($nameColumn)
                    ->add(Column::fromJsonPath('Capacity', 'spec.capacity.storage', 'capacity'))
                    ->add($statusColumn)
                    ->add($createdColumn),
            self::INGRESS =>
                $table
                    ->add($nameColumn)
                    ->add(Column::fromJsonPath('Class', 'spec.ingressClassName', 'class'))
                    ->add(
                        new Column('Hosts', 'hosts', function (mixed $dataSource): string {
                            $rules = $dataSource['spec']['rules'] ?? [];
                            $hosts = \array_map(fn (mixed $rule) => $rule['host'], $rules);
                            return \implode(', ', $hosts);
                        }, function(string $context, mixed $dataSource): ?string {
                            $rules = $dataSource['spec']['rules'] ?? [];
                            if (\count($rules) == 1) {
                                $host = $rules[0]['host'] ?? null;
                                if ($host) {
                                    return "https://$host";
                                }
                            }
                            return null;
                        }),
                    )
                    ->add($createdColumn),
            self::REPLICA_SET =>
                $table
                    ->add($nameColumn)
                    ->add(Column::fromJsonPath(
                        'Observed generation',
                        'status.observedGeneration',
                        'observedGeneration',
                    ))
                    ->add(Column::fromJsonPath(
                        'Replicas',
                        'status.replicas',
                        'replicas',
                    ))
                    ->add($ownerKindColumn)
                    ->add($ownedByColumn)
                    ->add($createdColumn),

            self::CONFIG_MAP, self::SECRET =>
                $table
                    ->add($nameColumn)
                    ->add(new Column('Data', 'data', function (mixed $dataSource): string {
                        return \strval(\count($dataSource['data'] ?? []));
                    }))
                    ->add($createdColumn),
            self::PERSISTENT_VOLUME_CLAIM =>
                $table
                    ->add($nameColumn)
                    ->add(new Column('Volume', 'volume', function (mixed $dataSource): string {
                        return $dataSource['spec']['volumeName'];
                    }, function(string $context, mixed $dataSource): ?string {
                        return Route::forNonNamespacedResource(
                            $context,
                            ObjectKind::PERSISTENT_VOLUME,
                            $dataSource['spec']['volumeName'],
                        )->toUrl();
                    }))
                    ->add($statusColumn)
                    ->add($createdColumn),
            self::ENDPOINT_SLICE =>
                $table
                    ->add($nameColumn)
                    ->add($ownerKindColumn)
                    ->add($ownedByColumn)
                    ->add($createdColumn),
            default =>
                $table
                    ->add($nameColumn)
                    ->add($createdColumn)

        };
    }
}
