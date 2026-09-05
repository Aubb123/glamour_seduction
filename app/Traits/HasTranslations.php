<?php

namespace App\Traits;

trait HasTranslations
{
    // Relation vers la table _translations
    public function translations()
    {
        // Si le modèle a explicitement désactivé les traductions
        if (isset($this->hasTranslations) && $this->hasTranslations === false) {
            // On essaie de garder le bon modèle "related" (translation model) pour un
            // typage cohérent, MAIS uniquement s'il existe réellement : un modèle qui
            // désactive les traductions (ex: User) n'a souvent jamais eu de modèle de
            // traduction associé (ex: UserTranslation n'existe pas), donc on retombe
            // sur $this pour éviter un "Class not found" fatal.
            $translationModel = $this->getTranslationModelName();
            $relatedModel = class_exists($translationModel) ? $translationModel : static::class;

            return $this->hasMany($relatedModel, 'id', 'id')->whereRaw('1 = 0');
        }

        // Si c'est une traduction, on retourne une relation vide
        if (str_ends_with(get_class($this), 'Translation')) {
            return $this->hasMany(get_class($this), 'id', 'id')->whereRaw('1 = 0');
        }

        $foreignKey = $this->translationForeignKey ?? null;

        return $this->hasMany($this->getTranslationModelName(), $foreignKey);
    }

    // Traduction selon la locale active ou spécifiée
    public function getTranslationFor(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if ($this->relationLoaded('translations')) {
            return $this->translations->first(fn ($t) => $t->lang?->code === $locale)
                ?? $this->translateFallback();
        }

        return $this->translations()
            ->whereHas('lang', fn ($q) => $q->where('code', $locale))
            ->first() ?? $this->translateFallback();
    }

    // Fallback vers la langue par défaut si traduction manquante
    public function translateFallback()
    {
        $defaultInfo = static::getDefaultLangInfo();

        // Pas de langue par défaut définie, ou langue par défaut désactivée :
        // on ne va pas chercher de traduction dans une langue inactive.
        if (! $defaultInfo || ! $defaultInfo['is_active']) {
            return null;
        }

        $defaultCode = $defaultInfo['code'];

        if ($this->relationLoaded('translations')) {
            return $this->translations->first(fn ($t) => $t->lang?->code === $defaultCode);
        }

        return $this->translations()
            ->whereHas('lang', fn ($q) => $q->where('code', $defaultCode))
            ->first();
    }

    // Récupérer un champ traduit
    public function getTranslation(string $field, ?string $locale = null)
    {
        $translation = $this->getTranslationFor($locale);

        if ($translation) {
            return $translation->$field;
        }

        // Aucune traduction trouvée (ni dans la locale demandée, ni en fallback).
        // Si c'est parce que la langue par défaut est désactivée, on le signale
        // explicitement plutôt que de renvoyer null silencieusement.
        return static::defaultLangDisabledLabel();
    }

    // Accesseur pour la traduction active (locale de l'application)
    public function getActiveTranslationAttribute()
    {
        $locale = app()->getLocale();

        // Récupère les traductions chargées (eager loaded) ou effectue une requête filtrée.
        // NB: ce trait suppose que le modèle de traduction utilise SoftDeletes ET que, si les
        // traductions sont eager-loadées ailleurs dans le code, elles le sont via l'un des scopes
        // ci-dessous (whereNull('deleted_at')). Si ce n'est pas le cas, des traductions supprimées
        // pourraient réapparaître silencieusement ici.
        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->whereNull('deleted_at')->with('lang')->get();

        // On ne considère JAMAIS une traduction dont la langue est désactivée — même en
        // dernier recours. Utile notamment quand la collection a été chargée via
        // scopeWithTrashedActiveTranslation() (qui n'exclut pas les langues inactives)
        // ou via un ->with('translations.lang') fait ailleurs dans le code.
        $activeTranslations = $translations->filter(fn ($t) => (bool) $t->lang?->is_active);

        return $activeTranslations->first(fn ($t) => $t->lang?->code === $locale)
            ?? $activeTranslations->first();
    }

    // Nom du modèle de traduction (surchargeable dans le modèle)
    public function getTranslationModelName(): string
    {
        // Si la propriété $translationModel est définie dans le modèle, on l'utilise
        // (Ex: protected string $translationModel = PlatformVersionTranslation::class;)
        if (isset($this->translationModel)) {
            return $this->translationModel;
        }

        // Sinon, on construit le nom du modèle de traduction à partir du nom du modèle principal
        // (ex: "PlatformVersion" → "PlatformVersionTranslation")
        $class = get_class($this);

        // Si le modèle se termine déjà par "Translation", on ne double pas
        if (str_ends_with($class, 'Translation')) {
            return $class; // pointe sur lui-même, la relation ne sera jamais utilisée
        }

        return $class.'Translation';
    }

    // Clé étrangère utilisée par les traductions (surchargeable dans le modèle)
    public function getTranslationForeignKey(): ?string
    {
        return $this->translationForeignKey ?? null;
    }

    // Scope pour eager loading optimisé (locale active + fallback)
    // NB : ne charge QUE les traductions dont la langue (`lang.is_active`) est active.
    // Une langue désactivée ne doit jamais fournir de contenu affiché, même en fallback.
    public function scopeWithActiveTranslation($query, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $defaultCode = static::getDefaultLangCode();

        $codes = array_filter(array_unique([$locale, $defaultCode]));

        return $query->with([
            'translations' => fn ($q) => $q
                ->whereNull('deleted_at')
                ->whereHas('lang', fn ($q) => $q->whereIn('code', $codes)->where('is_active', true))
                ->with('lang'),
        ]);
    }

    // Scope pour eager loading optimisé qui inclut aussi les traductions soft-deletées
    // (pour les pages de gestion des traductions dans le backend)
    public function scopeWithTrashedActiveTranslation($query, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $defaultCode = static::getDefaultLangCode();

        $codes = array_filter(array_unique([$locale, $defaultCode]));

        return $query->with([
            'translations' => fn ($q) => $q
                ->withTrashed() // ← inclut les soft-deletées
                ->whereHas('lang', fn ($q) => $q->whereIn('code', $codes))
                ->with('lang'),
        ]);
    }

    /**
     * Code de la langue "de référence" pour les traductions, basé sur
     * getActiveLangs()['currentLang'] (helper applicatif qui détermine la
     * langue active courante). C'est cette langue qui sert de fallback
     * strict quand la traduction demandée n'existe pas.
     */
    protected static function getDefaultLangCode(): ?string
    {
        return getActiveLangs()['currentLang']->code ?? null;
    }

    /**
     * Récupère le code ET le statut actif/inactif de la langue de référence,
     * directement depuis le modèle Lang renvoyé par getActiveLangs() —
     * pas de requête supplémentaire, pas de cache : on fait confiance à
     * l'objet déjà résolu par ce helper pour la requête en cours.
     *
     * @return array{code: string, is_active: bool}|null
     */
    protected static function getDefaultLangInfo(): ?array
    {
        $currentLang = getActiveLangs()['currentLang'] ?? null;

        if (! $currentLang) {
            return null;
        }

        return [
            'code' => $currentLang->code,
            'is_active' => (bool) $currentLang->is_active,
        ];
    }

    /**
     * Retourne un libellé explicite (ex: "Lang_fr désactivé") si la langue de
     * référence (getActiveLangs()['currentLang']) est désactivée. Retourne null
     * si tout est normal (langue active, ou langue introuvable).
     */
    protected static function defaultLangDisabledLabel(): ?string
    {
        $info = static::getDefaultLangInfo();

        if ($info && ! $info['is_active']) {
            return "Lang_{$info['code']} désactivé";
        }

        return null;
    }
}
