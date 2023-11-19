<?php

namespace App\Service;

use App\ObjectKind;

class Kubernetes
{
    /** @return array<string> the output */
    private function runConsoleCommand(string $command): array
    {
        \exec($command, $output, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception("Error executing command: $command");
        }
        return $output;
    }

    /** @return array<string> */
    public function getNamespaces(): array
    {
        $namespaces = $this->runConsoleCommand('kubectl get namespaces -o name');
        return \array_map(
            fn ($ns) => \preg_replace('/^namespace\//', '', $ns),
            $namespaces,
        );
    }

    public function describe(ObjectKind $resourceType, string $namespace, string $resourceName): string
    {
        $command = "kubectl describe {$resourceType->smallTitle()}";
        $command .= ' -n ' . \escapeshellarg($namespace);
        $command .= ' ' . \escapeshellarg($resourceName);
        $output = $this->runConsoleCommand($command);
        return \implode(PHP_EOL, $output);

    }

    public function getCurrentNamespace(): string
    {
        $currentNamespace = $this->runConsoleCommand(
            "kubectl ns --current view --minify -o jsonpath='{..namespace}'",
        );
        if (\count($currentNamespace) != 1) {
            throw new \Exception('Error getting current namespace (non-zero count)');
        }
        return $currentNamespace[0];
    }

    /** @return array<string> */
    public function getObjects(ObjectKind $objectKind, string $namespace): array
    {
        $escapedObjectKindPlural = \escapeshellarg($objectKind->pluralSmallTitle());
        $command = "kubectl get $escapedObjectKindPlural -o name";
        $escapedNamespace = \escapeshellarg($namespace);
        $command .= " --namespace=$escapedNamespace";
        $output = $this->runConsoleCommand($command);
        return \array_map(simplifiedObjectName(...), $output);
    }

    /** @return array<array<string, string>> */
    public function getObjectsWithNamespaces(ObjectKind $objectKind): array
    {
        $escapedObjectKindPlural = \escapeshellarg($objectKind->pluralSmallTitle());

        $objects = $this->runConsoleCommand(
            "kubectl get $escapedObjectKindPlural -A -o custom-columns=':metadata.name,:metadata.namespace'",
        );
        $result = [];
        foreach ($objects as $line) {
            if (\strlen(\trim($line)) == 0) {
                continue;
            }
            [$object, $namespace] = \preg_split('/\s+/', $line);
            $result[] = [
                $objectKind->smallTitle() => $object,
                'namespace' => $namespace,
            ];
        }
        return $result;
    }

    public function getShortClusterName(): string
    {
        $names = $this->runConsoleCommand("kubectl config view -o jsonpath='{.clusters[].name}'");
        if (\count($names) != 1) {
            throw new \Exception('Cannot determine single cluster from config');
        }
        return \preg_replace('/^.+\//', '', $names[0]);
    }
}