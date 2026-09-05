<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasSlug
{
    // Chemin de base pour les traductions (centralisé, surchargeable par modèle)
    protected static string $langPath = 'v1/backend/others/traits/messages-trait.hasSlug';

    // Nombre maximal de tentatives pour trouver un slug unique (garde-fou anti-boucle infinie)
    protected static int $maxSlugAttempts = 1000;

    // Boot du trait pour générer le slug automatiquement
    public static function bootHasSlug(): void
    {
        // Résout le nom de la table via getTable(), qui respecte une propriété
        // $table personnalisée (préfixe, nom non conventionnel) tout en conservant
        // la dérivation automatique par défaut (ex : BlogCategory → blog_categories).
        //
        // ⚠️ On instancie SANS constructeur (newInstanceWithoutConstructor) car le
        // constructeur Eloquent appelle bootIfNotBooted() : pendant bootHasSlug(),
        // le modèle est justement en cours de boot (flag $booting posé), donc
        // `new static` lèverait une LogicException. Sans constructeur, ni le boot
        // ni les traits ne sont ré-exécutés, et getTable() reste fiable car les
        // propriétés avec valeur par défaut ($table) sont initialisées quand même.
        $table = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor()->getTable();
        // $table = Str::snake(Str::pluralStudly(class_basename(static::class)));

        // Récupère la liste des colonnes de la table (requête directe : pas de cache,
        // afin qu'une migration ajoutant/retirant la colonne `slug` soit prise en compte
        // immédiatement sans avoir à purger le cache applicatif).
        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        // Si la table n'a pas de colonne 'slug', on ne fait rien (permet d'ajouter ce trait à des modèles sans slug)
        if (! in_array('slug', $columns)) {
            return;
        }

        // Vérifie si la table a une colonne 'lang_id' (pour les modèles de traduction)
        $hasLangId = in_array('lang_id', $columns);

        // Générer le slug lors de la création du modèle
        static::creating(function ($model) use ($hasLangId) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug(
                    $model->{static::getSlugSourceField()},
                    null,
                    $hasLangId ? ($model->lang_id ?? null) : null
                );
            }
        });

        // Mettre à jour le slug si le champ source change lors de la mise à jour du modèle
        static::updating(function ($model) use ($hasLangId) {

            $watchedFields = static::getSlugWatchedFields();

            foreach ($watchedFields as $field) {
                // Si le champ source du slug a été modifié, régénérer le slug.
                // `isDirty()` vérifie si le champ a été modifié par rapport à sa valeur initiale.
                if ($model->isDirty($field)) {
                    $model->slug = static::generateUniqueSlug(
                        $model->{static::getSlugSourceField()},
                        $model->id,
                        $hasLangId ? ($model->lang_id ?? null) : null
                    );

                    break;
                }
            }
        });
    }

    // Champ source du slug (par défaut 'name', peut être surchargé)
    protected static function getSlugSourceField(): string
    {
        return 'name';
    }

    /**
     * Force la régénération du slug à partir du champ source actuel.
     * (ex : après un restore, pour garantir l'unicité (slug, lang_id) si un
     * conflit est apparu pendant que la ligne était en corbeille).
     */
    public function regenerateSlug(): void
    {
        $this->slug = static::generateUniqueSlug(
            $this->{static::getSlugSourceField()},
            $this->id,
            $this->lang_id ?? null
        );

        $this->save();
    }

    // Champs réels à surveiller pour la régénération du slug lors d'une mise à jour
    // (par défaut : le champ source lui-même, peut être surchargé — ex: champs
    // virtuels comme 'first_last' chez User qui surveillent first_name + last_name)
    protected static function getSlugWatchedFields(): array
    {
        return [static::getSlugSourceField()];
    }

    /**
     * Slugifie une valeur brute sans gérer l'unicité : brique de base partagée
     * entre le trait lui-même (generateUniqueSlug) et les helpers de validation
     * (BackendHelper::assertTranslationUniqueness) afin de garantir une même
     * logique de slugification dans tout le projet.
     */
    public static function makeSlug(string $value): string
    {
        $slug = Str::slug($value);

        // Si le résultat est une chaîne vide (ex: valeur uniquement composée
        // de caractères non-alphanumériques ou d'emojis comme « 😀😀😀 »), on
        // retombe sur un slug aléatoire de secours. L'unicité n'est pas vérifiée
        // ici : c'est le rôle de generateUniqueSlug() (ou de la contrainte DB).
        return $slug === '' ? 'n-'.Str::lower(Str::random(8)) : $slug;
    }

    // Générer un slug unique
    protected static function generateUniqueSlug(?string $value, ?int $excludeId = null, ?int $langId = null): string
    {
        // Garde-fou : le champ source est vide/null → on lève une exception explicite
        // plutôt que de laisser Str::slug() planter avec un TypeError obscur
        if (empty($value)) {
            throw new \InvalidArgumentException(
                __(static::$langPath.'.empty_source_field', ['champ_source' => static::getSlugSourceField()])
            );
        }

        $slug = static::makeSlug($value);

        // Si makeSlug() a produit un slug aléatoire de secours (source sans
        // caractères utilisables, ex: emojis), on régénère un nouvel aléatoire en
        // cas de collision afin de conserver un format « n-xxxx » uniforme plutôt
        // qu'un suffixe du type « n-xxxx-1 ».
        if (Str::slug($value) === '') {
            while (static::slugExists($slug, $excludeId, $langId)) {
                $slug = static::makeSlug($value);
            }

            return $slug;
        }

        $original = $slug;
        $count = 1;

        // Ajouter un suffixe numérique si le slug existe déjà (Ex: 'example', 'example-1', 'example-2', etc.)
        while (static::slugExists($slug, $excludeId, $langId)) {
            if ($count > static::$maxSlugAttempts) {
                // Filet de sécurité : au-delà de N tentatives, on bascule sur un
                // suffixe aléatoire dont l'unicité est revérifiée avant d'être retenue.
                do {
                    $slug = $original.'-'.Str::lower(Str::random(6));
                } while (static::slugExists($slug, $excludeId, $langId));

                break;
            }

            $slug = $original.'-'.$count++;
        }

        return $slug;
    }

    // Vérifier si le slug existe déjà
    protected static function slugExists(string $slug, ?int $excludeId = null, ?int $langId = null): bool
    {
        $query = static::where('slug', $slug);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        // Si un lang_id est fourni, on restreint l'unicité à la même langue
        // Utile pour les modèles de traduction où l'unicité est (slug, lang_id)
        if ($langId !== null) {
            $query->where('lang_id', $langId);
        }

        return $query->exists();
    }
}
