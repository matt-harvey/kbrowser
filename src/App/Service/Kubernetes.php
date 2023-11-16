<?php

namespace App\Service;

use App\ResourceType;

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

    private function doExpandPodName(string $namespace, string $podName): string
    {
        $pods = $this->runConsoleCommand('kubectl get pods -o name');
        foreach ($pods as $pod) {
            if ($pod === $podName) {
                return $pod;
            }
        }
        throw new \Exception('Could not find pod to expand name');
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

    public function describe(ResourceType $resourceType, string $namespace, string $resourceName): string
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
    public function getPods(string $namespace): array
    {
        $command = "kubectl get pods -o name";
        $escapedNamespace = \escapeshellarg($namespace);
        $command .= " --namespace=$escapedNamespace";
        $output = $this->runConsoleCommand($command);
        return \array_map(simplifiedPodName(...), $output);
    }

    /** @return array<array<string, string>> */
    public function getPodsWithNamespaces(): array
    {
        $pods = $this->runConsoleCommand(
            'kubectl get pods -A -o custom-columns=":metadata.name,:metadata.namespace"',
        );
        $result = [];
        foreach ($pods as $line) {
            if (\strlen(\trim($line)) == 0) {
                continue;
            }
            [$pod, $namespace] = \preg_split('/\s+/', $line);
            $result[] = ['pod' => $pod, 'namespace' => $namespace];
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

    /** @return array<string> */
    public function getDeployments(string $namespace): array
    {
        $command = "kubectl get deployments -o name";
        $escapedNamespace = \escapeshellarg($namespace);
        $command .= " --namespace=$escapedNamespace";
        $deployments = $this->runConsoleCommand($command);
        return \array_map(simplifiedDeploymentName(...), $deployments);
    }

    /** @return array<array<string, string>> */
    public function getDeploymentsWithNamespaces(): array
    {
        $command = 'kubectl get deployments -A -o custom-columns=":metadata.name,:metadata.namespace"';
        $deployments = $this->runConsoleCommand($command);
        $result = [];
        foreach ($deployments as $line) {
            if (\strlen(\trim($line)) == 0) {
                continue;
            }
            [$deployment, $namespace] = \preg_split('/\s+/', $line);
            $result[] = ['deployment' => $deployment, 'namespace' => $namespace];
        }
        return $result;
    }

    /** @return array<string> */
    public function getDaemonSets(string $namespace): array
    {
        $command = "kubectl get daemonsets -o name";
        $escapedNamespace = \escapeshellarg($namespace);
        $command .= " --namespace=$escapedNamespace";
        $daemonSets = $this->runConsoleCommand($command);
        return \array_map(simplifiedDaemonSetName(...), $daemonSets);
    }

    /** @return array<array<string, string>> */
    public function getDaemonSetsWithNamespaces(): array
    {
        $command = 'kubectl get daemonsets -A -o custom-columns=":metadata.name,:metadata.namespace"';
        $daemonSets = $this->runConsoleCommand($command);
        $result = [];
        foreach ($daemonSets as $line) {
            if (\strlen(\trim($line)) == 0) {
                continue;
            }
            [$daemonSet, $namespace] = \preg_split('/\s+/', $line);
            $result[] = ['daemonset' => $daemonSet, 'namespace' => $namespace];
        }
        return $result;
    }

    /** @return array<string> */
    public function getStatefulSets(string $namespace): array
    {
        $command = "kubectl get statefulsets -o name";
        $escapedNamespace = \escapeshellarg($namespace);
        $command .= " --namespace=$escapedNamespace";
        $statefulSets = $this->runConsoleCommand($command);
        return \array_map(simplifiedStatefulSetName(...), $statefulSets);
    }

    /** @return array<array<string, string>> */
    public function getStatefulSetsWithNamespaces(): array
    {
        $command = 'kubectl get statefulsets -A -o custom-columns=":metadata.name,:metadata.namespace"';
        $statefulSets = $this->runConsoleCommand($command);
        $result = [];
        foreach ($statefulSets as $line) {
            if (\strlen(\trim($line)) == 0) {
                continue;
            }
            [$statefulSet, $namespace] = \preg_split('/\s+/', $line);
            $result[] = ['statefulset' => $statefulSet, 'namespace' => $namespace];
        }
        return $result;
    }

}