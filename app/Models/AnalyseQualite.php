<?php

namespace App\Models;

use Database\Factories\AnalyseQualiteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyseQualite extends Model
{
    /** @use HasFactory<AnalyseQualiteFactory> */
    use HasFactory;

    protected $table = 'analyse_qualites';

    protected $fillable = [
        'point_id',
        'date_prelevement',
        'ph',
        'chlore_residuel',
        'turbidite',
        'nitrates',
        'bacteries_coliformes',
        'laboratoire',
    ];

    /**
     * Seuils de potabilité utilisés pour juger une analyse
     * (valeurs de référence inspirées des recommandations de l'OMS).
     * min / max = null signifie « pas de limite de ce côté ».
     */
    public const NORMES = [
        'ph' => ['label' => 'pH', 'unite' => '', 'min' => 6.5, 'max' => 8.5],
        'chlore_residuel' => ['label' => 'Chlore résiduel', 'unite' => 'mg/L', 'min' => 0.2, 'max' => 1.0],
        'turbidite' => ['label' => 'Turbidité', 'unite' => 'NTU', 'min' => null, 'max' => 5.0],
        'nitrates' => ['label' => 'Nitrates', 'unite' => 'mg/L', 'min' => null, 'max' => 50.0],
        'bacteries_coliformes' => ['label' => 'Coliformes', 'unite' => 'UFC/100 mL', 'min' => null, 'max' => 0],
    ];

    protected function casts(): array
    {
        return [
            'date_prelevement' => 'date',
            'ph' => 'float',
            'chlore_residuel' => 'float',
            'turbidite' => 'float',
            'nitrates' => 'float',
            'bacteries_coliformes' => 'integer',
            'conforme' => 'boolean',
        ];
    }

    /**
     * « conforme » n'est jamais saisi : il est recalculé à chaque
     * enregistrement à partir des normes de potabilité.
     */
    protected static function booted(): void
    {
        static::saving(function (AnalyseQualite $analyse) {
            $analyse->conforme = self::estConforme($analyse->only(array_keys(self::NORMES)));
        });
    }

    public function point(): BelongsTo
    {
        return $this->belongsTo(PointPrelevement::class, 'point_id');
    }

    /**
     * Indique si une valeur respecte la norme du paramètre donné.
     */
    public static function respecteNorme(string $parametre, float|int|null $valeur): bool
    {
        if ($valeur === null || ! isset(self::NORMES[$parametre])) {
            return true;
        }

        $norme = self::NORMES[$parametre];

        return ($norme['min'] === null || $valeur >= $norme['min'])
            && ($norme['max'] === null || $valeur <= $norme['max']);
    }

    /**
     * @param  array<string, float|int|null>  $valeurs
     */
    public static function estConforme(array $valeurs): bool
    {
        foreach (array_keys(self::NORMES) as $parametre) {
            if (! self::respecteNorme($parametre, $valeurs[$parametre] ?? null)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Liste des paramètres hors norme (ex. ['Turbidité', 'Coliformes']).
     *
     * @return list<string>
     */
    public function parametresNonConformes(): array
    {
        return collect(self::NORMES)
            ->filter(fn ($norme, $parametre) => ! self::respecteNorme($parametre, $this->{$parametre}))
            ->pluck('label')
            ->values()
            ->all();
    }

    /**
     * Texte lisible de la norme d'un paramètre (ex. « 6.5 – 8.5 », « ≤ 5 NTU »).
     */
    public static function normeTexte(string $parametre): string
    {
        $n = self::NORMES[$parametre];
        $unite = $n['unite'] ? ' '.$n['unite'] : '';

        if ($n['min'] !== null && $n['max'] !== null) {
            return $n['min'].' – '.$n['max'].$unite;
        }

        return ($n['max'] === 0 ? '= 0' : '≤ '.$n['max']).$unite;
    }
}
