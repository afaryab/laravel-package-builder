<?php

namespace LaravelApp\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ApplicationConfigService
{
    /**
     * Get menu items for the specified type
     */
    public function getMenus(string $type = 'backend'): array
    {
        $menus = Config::get("application.menus.{$type}", []);
        
        // Filter menus based on user permissions and active status
        return collect($menus)
            ->filter(function ($menu) {
                return $menu['active'] && $this->hasPermission($menu['permission']);
            })
            ->map(function ($menu) {
                // Process submenus if they exist
                if (isset($menu['submenu'])) {
                    $menu['submenu'] = collect($menu['submenu'])
                        ->filter(function ($submenu) {
                            return $submenu['active'] && $this->hasPermission($submenu['permission']);
                        })
                        ->sortBy('order')
                        ->toArray();
                }
                return $menu;
            })
            ->sortBy('order')
            ->toArray();
    }

    /**
     * Get backend navigation menus
     */
    public function getBackendMenus(): array
    {
        return $this->getMenus('backend');
    }

    /**
     * Get settings navigation menus
     */
    public function getSettingsMenus(): array
    {
        return $this->getMenus('settings');
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $permission): bool
    {
        // For now, return true for authenticated users
        // In a real app, you'd implement proper permission checking
        return Auth::check();
    }

    /**
     * Get notification configuration
     */
    public function getNotificationConfig(): array
    {
        return Config::get('application.notifications', []);
    }

    /**
     * Get notification channels
     */
    public function getNotificationChannels(): array
    {
        return Config::get('application.notifications.channels', []);
    }

    /**
     * Get notification types
     */
    public function getNotificationTypes(): array
    {
        return Config::get('application.notifications.types', []);
    }

    /**
     * Get all permissions
     */
    public function getPermissions(): array
    {
        return Config::get('application.permissions', []);
    }

    /**
     * Get roles configuration
     */
    public function getRoles(): array
    {
        return Config::get('application.roles', []);
    }

    /**
     * Get feature flags
     */
    public function getFeatures(): array
    {
        return Config::get('application.features', []);
    }

    /**
     * Check if a feature is enabled
     */
    public function isFeatureEnabled(string $feature): bool
    {
        return Config::get("application.features.{$feature}", false);
    }

    /**
     * Get app configuration
     */
    public function getAppConfig(): array
    {
        return Config::get('application.app', []);
    }

    /**
     * Get menu item by key
     */
    public function getMenuItem(string $type, string $key): ?array
    {
        return Config::get("application.menus.{$type}.{$key}");
    }

    /**
     * Check if current route matches menu item
     */
    public function isActiveRoute(array $menu): bool
    {
        $currentPath = request()->path();
        $currentRoute = request()->route() ? request()->route()->getName() : '';
        
        // Check if the menu item itself has an active route
        if (isset($menu['route']) && (request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*'))) {
            return true;
        }

        // Check by URL path matching - support hierarchical URLs
        if (isset($menu['url'])) {
            // Exact match or path starts with menu URL
            if ($currentPath === $menu['url'] || str_starts_with($currentPath, $menu['url'] . '/')) {
                return true;
            }
        }
        
        // For settings menu, check if current path starts with settings
        if (isset($menu['route']) && $menu['route'] === 'settings.index' && str_starts_with($currentPath, 'settings')) {
            return true;
        }
        
        // For setup menu, check if current path contains setup
        if (isset($menu['route']) && $menu['route'] === 'setup' && str_contains($currentPath, 'setup')) {
            return true;
        }

        // Check if any submenu item is active
        if (isset($menu['submenu'])) {
            foreach ($menu['submenu'] as $submenu) {
                if (isset($submenu['route']) && (request()->routeIs($submenu['route']) || request()->routeIs($submenu['route'] . '.*'))) {
                    return true;
                }
                
                // Check submenu URL path
                if (isset($submenu['url'])) {
                    if ($currentPath === $submenu['url'] || str_starts_with($currentPath, $submenu['url'] . '/')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get breadcrumb for current route
     */
    public function getBreadcrumb(): array
    {
        $breadcrumb = [];
        $currentRoute = request()->route()->getName();
        
        // Check backend menus
        foreach ($this->getBackendMenus() as $key => $menu) {
            if ($currentRoute === $menu['route'] || str_starts_with($currentRoute, $menu['route'] . '.')) {
                $breadcrumb[] = [
                    'label' => $menu['label'],
                    'route' => $menu['route'],
                    'active' => $currentRoute === $menu['route']
                ];
            }
        }

        // Check settings menus
        if (str_starts_with($currentRoute, 'settings.')) {
            $breadcrumb[] = [
                'label' => 'Settings',
                'route' => 'settings',
                'active' => false
            ];

            foreach ($this->getSettingsMenus() as $key => $menu) {
                if ($currentRoute === $menu['route']) {
                    $breadcrumb[] = [
                        'label' => $menu['label'],
                        'route' => $menu['route'],
                        'active' => true
                    ];
                }
            }
        }

        return $breadcrumb;
    }
}
