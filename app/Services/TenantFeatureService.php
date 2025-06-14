<?php


namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class TenantFeatureService
{
    protected array $modules;
    protected array $features;
    protected array $tenants;

    public function __construct()
    {
        $config = config('tenant-features');
        $this->modules = $config['modules'] ?? [];
        $this->features = $config['features'] ?? [];
        $this->tenants = $config['tenants'] ?? [];
    }

    public function hasModule(string $moduleKey, ?string $tenantId = null): bool
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();
        dd('stop');
        
        if (!$tenantId) {
            return false;
        }
        return in_array($moduleKey, $this->getTenantModules($tenantId));
    }

    public function hasFeature(string $featureKey, ?string $tenantId = null): bool
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();
        
        if (!$tenantId) {
            return false;
        }

        // Check if feature is always enabled
        if (isset($this->features[$featureKey]['always_enabled']) && $this->features[$featureKey]['always_enabled']) {
            return true;
        }

        // Check tenant-specific features
        if (!isset($this->tenants[$tenantId])) {
            return false;
        }

        return in_array($featureKey, $this->tenants[$tenantId]['enabled_features'] ?? []);
    }

    public function getTenantModules(?string $tenantId = null): array
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();
        
        if (!$tenantId || !isset($this->tenants[$tenantId])) {
            return [];
        }

        // Cache the result for performance
        return Cache::remember("tenant_modules_{$tenantId}", 300, function () use ($tenantId) {
            $tenantConfig = $this->tenants[$tenantId];
            $availableModules = [];

            // 1. Get modules from enabled features
            $enabledFeatures = $tenantConfig['enabled_features'] ?? [];
            foreach ($enabledFeatures as $featureKey) {
                if (isset($this->features[$featureKey]['modules'])) {
                    $availableModules = array_merge(
                        $availableModules,
                        $this->features[$featureKey]['modules']
                    );
                }
            }

            // 2. Add always-enabled feature modules
            foreach ($this->features as $featureKey => $feature) {
                if (isset($feature['always_enabled']) && $feature['always_enabled']) {
                    $availableModules = array_merge(
                        $availableModules,
                        $feature['modules'] ?? []
                    );
                }
            }

            // 3. Add additional modules directly assigned to tenant
            if (!empty($tenantConfig['additional_modules'])) {
                $availableModules = array_merge(
                    $availableModules,
                    $tenantConfig['additional_modules']
                );
            }

            // 4. Remove disabled modules
            if (!empty($tenantConfig['disabled_modules'])) {
                $availableModules = array_diff(
                    $availableModules,
                    $tenantConfig['disabled_modules']
                );
            }

            return array_unique($availableModules);
        });
    }

    public function getTenantFeatures(?string $tenantId = null): array
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();
        
        if (!$tenantId || !isset($this->tenants[$tenantId])) {
            return [];
        }

        $enabledFeatures = $this->tenants[$tenantId]['enabled_features'] ?? [];
        
        // Always include features that are marked as always_enabled
        foreach ($this->features as $key => $feature) {
            if (isset($feature['always_enabled']) && $feature['always_enabled']) {
                $enabledFeatures[] = $key;
            }
        }

        return array_unique($enabledFeatures);
    }

    public function getAvailableRoutes(?string $tenantId = null): array
    {
        $tenantModules = $this->getTenantModules($tenantId);
        $availableRoutes = [];

        foreach ($tenantModules as $moduleKey) {
            if (isset($this->modules[$moduleKey]['routes'])) {
                $availableRoutes = array_merge(
                    $availableRoutes,
                    $this->modules[$moduleKey]['routes']
                );
            }
        }

        return array_unique($availableRoutes);
    }

    public function canAccessRoute(string $routeName, ?string $tenantId = null): bool
    {
        $availableRoutes = $this->getAvailableRoutes($tenantId);
        
        foreach ($availableRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }
        
        return false;
    }

    public function getTenantInfo(?string $tenantId = null): ?array
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();
        
        if (!$tenantId || !isset($this->tenants[$tenantId])) {
            return null;
        }

        $tenantConfig = $this->tenants[$tenantId];
        
        return [
            'tenant_id' => $tenantId,
            'enabled_features' => $this->getTenantFeatures($tenantId),
            'available_modules' => $this->getTenantModules($tenantId),
            'additional_modules' => $tenantConfig['additional_modules'] ?? [],
            'disabled_modules' => $tenantConfig['disabled_modules'] ?? [],
            'notes' => $tenantConfig['notes'] ?? '',
        ];
    }

    public function getAllModules(): array
    {
        return $this->modules;
    }

    public function getAllFeatures(): array
    {
        return $this->features;
    }

    public function getModulesByFeature(string $featureKey): array
    {
        return $this->features[$featureKey]['modules'] ?? [];
    }

    protected function getCurrentTenantId(): ?string
    {
        $tenant = app('currentTenant');
        return $tenant ? $tenant->domain : null;
    }

    public function clearCache(?string $tenantId = null): void
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();
        if ($tenantId) {
            Cache::forget("tenant_modules_{$tenantId}");
        }
    }
}
