<?php

namespace App\FileManager;

use Alexusmai\LaravelFileManager\Services\ConfigService\ConfigRepository;
use Illuminate\Support\Facades\Auth;

class UserConfigRepository implements ConfigRepository
{
    public function getRoutePrefix(): string
    {
        return config('file-manager.routePrefix', 'file-manager');
    }

    public function getDiskList(): array
    {
        return (array) config('file-manager.diskList', ['public']);
    }

    public function getLeftDisk(): ?string
    {
        return config('file-manager.leftDisk', 'public');
    }

    public function getRightDisk(): ?string
    {
        return config('file-manager.rightDisk', 'public');
    }

    public function getLeftPath(): ?string
    {
        $id = Auth::id();
        return $id ? 'files/'.$id : 'files';
    }

    public function getRightPath(): ?string
    {
        $id = Auth::id();
        return $id ? 'files/'.$id : 'files';
    }

    public function getWindowsConfig(): int
    {
        return (int) config('file-manager.windowsConfig', 2);
    }

    public function getMaxUploadFileSize(): ?int
    {
        return config('file-manager.maxUploadFileSize');
    }

    public function getAllowFileTypes(): array
    {
        return (array) config('file-manager.allowFileTypes', []);
    }

    public function getHiddenFiles(): bool
    {
        return (bool) config('file-manager.hiddenFiles', false);
    }

    public function getMiddleware(): array
    {
        return (array) config('file-manager.middleware', []);
    }

    public function getAcl(): bool
    {
        return (bool) config('file-manager.acl', false);
    }

    public function getAclHideFromFM(): bool
    {
        return (bool) config('file-manager.aclHideFromFM', false);
    }

    public function getAclStrategy(): string
    {
        return (string) config('file-manager.aclStrategy', 'blacklist');
    }

    public function getAclRepository(): string
    {
        return (string) config('file-manager.aclRepository');
    }

    public function getAclRulesCache(): ?int
    {
        return config('file-manager.aclRulesCache');
    }

    public function getSlugifyNames(): bool
    {
        return (bool) config('file-manager.slugifyNames', false);
    }
}
