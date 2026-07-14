<?php

namespace StaticRegistry\Model\Entity;

/**
 * Lokální metadata static stránky synchronizovaná z red modulu.
 */
class StaticRegistryEntry {

    private int $menuItemId;
    private int $redStaticId;
    private string $path;
    private string $template;
    private ?string $creator;
    private string $updated;
    private string $siteCode;

    public function getMenuItemId(): int {
        return $this->menuItemId;
    }

    public function setMenuItemId(int $menuItemId): StaticRegistryEntry {
        $this->menuItemId = $menuItemId;
        return $this;
    }

    public function getRedStaticId(): int {
        return $this->redStaticId;
    }

    public function setRedStaticId(int $redStaticId): StaticRegistryEntry {
        $this->redStaticId = $redStaticId;
        return $this;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function setPath(string $path): StaticRegistryEntry {
        $this->path = $path;
        return $this;
    }

    public function getTemplate(): string {
        return $this->template;
    }

    public function setTemplate(string $template): StaticRegistryEntry {
        $this->template = $template;
        return $this;
    }

    public function getCreator(): ?string {
        return $this->creator;
    }

    public function setCreator(?string $creator): StaticRegistryEntry {
        $this->creator = $creator;
        return $this;
    }

    public function getUpdated(): string {
        return $this->updated;
    }

    public function setUpdated(string $updated): StaticRegistryEntry {
        $this->updated = $updated;
        return $this;
    }

    public function getSiteCode(): string {
        return $this->siteCode;
    }

    public function setSiteCode(string $siteCode): StaticRegistryEntry {
        $this->siteCode = $siteCode;
        return $this;
    }
}
