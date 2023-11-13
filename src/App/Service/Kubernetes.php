<?php

namespace App\Service;

class Kubernetes
{
    private function doExpandPodName(string $namespace, string $podName): string
    {
        \exec('kubectl get pods -o name', $pods, $resultCode);
        if ($resultCode !== 0) {
            throw new \Exception('Could not list pods to expand name');
        }
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
        \exec('kubectl get namespaces -o name', $namespaces, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Error getting namespaces');
        }
        return \array_map(
            fn ($ns) => \preg_replace('/^namespace\//', '', $ns),
            $namespaces,
        );
    }

    public function describePod(string $pod, string $namespace): string
    {
        $command = 'kubectl describe pod';
        $command .= ' -n ' . \escapeshellarg($namespace);
        $command .= ' ' . \escapeshellarg($pod);
        \exec($command, $output, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Could not describe pod');
        }
        return \implode(PHP_EOL, $output);
    }

    public function describeDeployment(string $deployment, string $namespace): string
    {
        $command = 'kubectl describe deployment';
        $command .= ' -n ' . \escapeshellarg($namespace);
        $command .= ' ' . \escapeshellarg($deployment);
        \exec($command, $output, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Could not describe deployment');
        }
        return \implode(PHP_EOL, $output);
    }

    public function describeDaemonSet(string $daemonSet, string $namespace): string
    {
        $command = 'kubectl describe daemonset';
        $command .= ' -n ' . \escapeshellarg($namespace);
        $command .= ' ' . \escapeshellarg($daemonSet);
        \exec($command, $output, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Could not describe daemonset');
        }
        return \implode(PHP_EOL, $output);
    }

    public function getCurrentNamespace(): string
    {
        \exec(
            "kubectl ns --current view --minify -o jsonpath='{..namespace}'",
            $currentNamespace,
            $resultCode,
        );
        if ($resultCode != 0) {
            throw new \Exception('Error getting current namespace');
        }
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
        \exec($command, $pods, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Error getting pods');
        }
        return \array_map(simplifiedPodName(...), $pods);
    }

    /** @return array<array<string, string>> */
    public function getPodsWithNamespaces(): array
    {
        $command = 'kubectl get pods -A -o custom-columns=":metadata.name,:metadata.namespace"';
        \exec($command, $pods, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Error getting pods');
        }
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
        $command = "kubectl config view -o jsonpath='{.clusters[].name}'";
        \exec($command, $names, $resultCode);
        if ($resultCode !== 0) {
            throw new \Exception('Could not get cluster name');
        }
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
        \exec($command, $deployments, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Error getting deployments');
        }
        return \array_map(simplifiedDeploymentName(...), $deployments);
    }

    /** @return array<array<string, string>> */
    public function getDeploymentsWithNamespaces(): array
    {
        $command = 'kubectl get deployments -A -o custom-columns=":metadata.name,:metadata.namespace"';
        \exec($command, $deployments, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Error getting deployments');
        }
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
        \exec($command, $daemonSets, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Error getting daemonsets');
        }
        return \array_map(simplifiedDaemonSetName(...), $daemonSets);
    }

    /** @return array<array<string, string>> */
    public function getDaemonSetsWithNamespaces(): array
    {
        $command = 'kubectl get daemonsets -A -o custom-columns=":metadata.name,:metadata.namespace"';
        \exec($command, $daemonSets, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Error getting daemonsets');
        }
        $result = [];
        foreach ($daemonSets as $line) {
            if (\strlen(\trim($line)) == 0) {
                continue;
            }
            [$daemonSet, $namespace] = \preg_split('/\s+/', $line);
            $result[] = ['daemonSet' => $daemonSet, 'namespace' => $namespace];
        }
        return $result;
    }

}