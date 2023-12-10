<?php

namespace App\Service;

use App\Exception\NotFoundException;
use App\ObjectKind;
use App\Table;

class Kubernetes
{
    /**
     * @return array<string> the output
     * @throws \Exception
     * @throws NotFoundException
     */
    private function runConsoleCommand(string $command): array
    {
        \exec("$command 2>&1", $output, $resultCode);
        if ($resultCode != 0) {
            if (\count($output) != 0 && \str_starts_with($output[0], 'Error from server (NotFound)')) {
                throw new NotFoundException();
            }
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

    /**
     * @throws NotFoundException
     */
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

    /**
     * @throws NotFoundException
     * @return array<string>
     */
    public function getPodLogs(
        string $context,
        string $namespace,
        string $podName,
        bool $showNewestFirst = true,
    ): array
    {
        $command = "kubectl logs";
        $command .= ' --context=' . \escapeshellarg($context);
        $command .= ' --namespace=' . \escapeshellarg($namespace);
        $command .= ' ' . \escapeshellarg($podName);
        $lines = $this->runConsoleCommand($command);
        if ($showNewestFirst) {
            $lines = \array_reverse($lines);
        }
        return $lines;
    }

    /** @return array<string> */
    public function getSelectorLogs(
        string $context,
        string $namespace,
        string $selector,
        bool $showNewestFirst = true,
    ): array
    {
        $command = 'kubectl logs --all-containers ';
        $command .= ' --context=' . \escapeshellarg($context);
        $command .= ' --namespace=' . \escapeshellarg($namespace);
        $command .= ' -l ' . \escapeshellarg($selector);
        $lines = $this->runConsoleCommand($command);
        if ($showNewestFirst) {
            $lines = \array_reverse($lines);
        }
        return $lines;
    }

    public function getObjectsTable(
        string $context,
        ObjectKind $objectKind,
        ?string $namespace,
        bool $includeNamespaceInTable,
    ): Table
    {
        $escapedObjectKindPlural = \escapeshellarg($objectKind->pluralSmallTitle());
        $command = "kubectl get $escapedObjectKindPlural -o json";
        if ($namespace === null) {
            $command .= ' -A';
        } else {
            $command .= ' --namespace=' . \escapeshellarg($namespace);
        }
        $command .= ' --context=' . \escapeshellarg($context);

        $json = \join('', $this->runConsoleCommand($command));
        $arr = \json_decode(json: $json, associative: true);
        $items = $arr['items'];

        $table = $objectKind->makeTable($includeNamespaceInTable);
        $table->setSources($items);

        return $table;
    }
}
