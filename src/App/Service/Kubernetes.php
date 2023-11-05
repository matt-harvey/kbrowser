<?php

namespace App\Service;

class Kubernetes
{
    private function doSimplifyPodName(string $raw): string
    {
        $tailPattern = '/-[a-z0-9]+-[a-z0-9]+$/';
        $prelim = \preg_replace($tailPattern, '', $raw);
        return \preg_replace('/^pod\//', '', $prelim);
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

    public function describePod(string $namespace, string $pod): string
    {
        $command = 'kubectl describe';
        $command .= ' --namespace=' . \escapeshellarg($namespace);
        $command .= ' ' . \escapeshellarg($pod);
        \exec($command, $output, $resultCode);
        if ($resultCode != 0) {
            throw new \Exception('Could not describe pod');
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
        $escapedNamespace = \escapeshellarg($namespace);
        $command = "kubectl get pods -o name --namespace=$escapedNamespace";
        \exec($command, $pods, $resultCode);
        return $pods;
    }

    private function expandPodNameFull(string $raw): string
    {
        \exec('kubectl get pods -o name', $pods, $resultCode);
        if ($resultCode !== 0) {
            throw new \Exception('Error expanding pod name');
        }
        foreach ($pods as $pod) {
            if ($this->doSimplifyPodName($pod) === $raw) {
                return $pod;
            }
        }
        throw new \Exception('Could not expand pod name');
    }

    private function expandPodName(string $raw): string
    {
        \exec('kubectl get pods -o name', $pods, $resultCode);
        if ($resultCode !== 0) {
            throw new \Exception('Error expanding pod name');
        }
        foreach ($pods as $pod) {
            if ($this->doSimplifyPodName($pod) === $raw) {
                return \preg_replace('/^pod\//', '', $pod);
            }
        }
        throw new \Exception('Could not expand pod name');
    }
}