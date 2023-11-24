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
    public function getContexts(): array
    {
        return $this->runConsoleCommand('kubectl config get-contexts -o name');
    }

    /** @return array<string> */
    public function getShortContexts(): array
    {
        return \array_map(
            fn ($c) => \preg_replace('|^.+/|', '', $c),
            $this->getContexts(),
        );
    }

    /** @return array<string> */
    public function getNamespaces(string $context): array
    {
        $command = 'kubectl get namespaces -o name';
        $command .= ' --context=' . \escapeshellarg($context);
        $namespaces = $this->runConsoleCommand($command);
        return \array_map(
            fn ($ns) => \preg_replace('/^namespace\//', '', $ns),
            $namespaces,
        );
    }

    public function describe(
        string $context,
        ObjectKind $kind,
        ?string $namespace,
        string $resourceName,
    ): string
    {
        $kind = \escapeshellarg($kind->smallTitle());
        $command = "kubectl describe $kind";
        $command .= ' --context=' . \escapeshellarg($context);
        if ($namespace !== null) {
            $command .= ' -n ' . \escapeshellarg($namespace);
        }
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
    public function getObjects(string $context, ObjectKind $objectKind, ?string $namespace): array
    {
        $escapedObjectKindPlural = \escapeshellarg($objectKind->pluralSmallTitle());
        $command = "kubectl get $escapedObjectKindPlural -o name";
        $command .= ' --context=' . \escapeshellarg($context);
        if ($namespace !== null) {
            $escapedNamespace = \escapeshellarg($namespace);
            $command .= " --namespace=$escapedNamespace";
        }
        $output = $this->runConsoleCommand($command);
        return \array_map(simplifiedObjectName(...), $output);
    }

    /** @return array<array<string, string>> */
    public function getObjectsWithNamespaces(string $context, ObjectKind $objectKind): array
    {
        $escapedObjectKindPlural = \escapeshellarg($objectKind->pluralSmallTitle());
        $command = "kubectl get $escapedObjectKindPlural -A -o custom-columns=':metadata.name,:metadata.namespace'";
        $command .= ' --context=' . \escapeshellarg($context);

        $objects = $this->runConsoleCommand($command);
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
}
