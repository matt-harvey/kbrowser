<?php

namespace App;

class Route
{
    private function __construct(
        private readonly string $url,
        private readonly string $breadcrumbName,
    )
    {
    }

    public static function forHome(): self
    {
        return new self('/', HOME_CHAR);
    }

    public static function forContext(string $context): self
    {
        return new self(
            '/context?' . \http_build_query(['context' => $context]),
            simplifiedContextName($context),
        );
    }

    public static function forNamespaces(string $context): self
    {
        return new self(
            '/namespaces?' . \http_build_query(['context' => $context]),
            'namespaces',
        );
    }

    public static function forNamespace(string $context, string $namespace): self
    {
        return new self(
            '/namespace?' . \http_build_query(['context' => $context, 'namespace' => $namespace]),
            $namespace,
        );
    }

    public static function forResources(
        string $context,
        ObjectKind $kind,
        ?string $namespace = null,
    ): self
    {
        $query = ['context' => $context, 'kind' => $kind->title()];
        if ($namespace !== null) {
            $query['namespace'] = $namespace;
        }
        return new self(
            '/resources?' . \http_build_query($query),
            $kind->pluralSmallTitle(),
        );
    }

    public static function forNamespacedResource(
        string $context,
        ObjectKind $resourceType,
        string $resourceName,
        string $namespace,
    ): self
    {
        $query = [
            'context' => $context,
            'namespace' => $namespace,
            'kind' => $resourceType->title(),
            'object' => $resourceName,
        ];
        return new self(
            '/resource?' . \http_build_query($query),
            simplifiedObjectName($resourceName),
        );
    }

    public static function forNonNamespacedResource(
        string $context,
        ObjectKind $resourceType,
        string $resourceName,
    ): self
    {
        $query = [
            'context' => $context,
            'kind' => $resourceType->title(),
            'object' => $resourceName,
        ];
        return new self(
            '/nns-resource?' . \http_build_query($query),
            simplifiedObjectName($resourceName),
        );
    }

    /** @return array<string, ?string> */
    public function toBreadcrumb(bool $navigable = true): array
    {
        return [$this->breadcrumbName => ($navigable ? $this->url : null)];
    }

    public function toUrl(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}