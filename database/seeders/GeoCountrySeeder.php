<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\GeoCountryTranslation;
use App\Models\Lang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GeoCountrySeeder extends Seeder
{
    /**
     * Rattachements souveraineté (territoires / SAR / entités contestées).
     * Résolus en fin de run() une fois que le pays de rattachement existe
     * en base (peut nécessiter plusieurs exécutions du seeder au fur et à
     * mesure que les lots suivants sont ajoutés).
     */
    private array $sovereigntyOverrides = [
        'AI' => 'GB', // Anguilla
        'AS' => 'US', // Samoa américaines
        'AW' => 'NL', // Aruba
        'AX' => 'FI', // Îles Åland
        'BL' => 'FR', // Saint-Barthélemy
        'BM' => 'GB', // Bermudes
        'BQ' => 'NL', // Bonaire, Saint-Eustache et Saba
        'BV' => 'NO', // Île Bouvet
        'CC' => 'AU', // Îles Cocos
        'CK' => 'NZ', // Îles Cook
        'CW' => 'NL', // Curaçao
        'CX' => 'AU', // Île Christmas
        'FK' => 'GB', // Îles Falkland
        'FO' => 'DK', // Îles Féroé
        'GF' => 'FR', // Guyane française
        'GB-ENG' => 'GB', // Angleterre (nation constitutive)
        'GB-NIR' => 'GB', // Irlande du Nord (nation constitutive)
        'GB-SCT' => 'GB', // Écosse (nation constitutive)
        'GB-WLS' => 'GB', // Pays de Galles (nation constitutive)
        'GG' => 'GB', // Guernesey
        'GI' => 'GB', // Gibraltar
        'GL' => 'DK', // Groenland
        'GP' => 'FR', // Guadeloupe
        'GS' => 'GB', // Géorgie du Sud-et-les-îles Sandwich du Sud
        'GU' => 'US', // Guam
        'HK' => 'CN', // Hong Kong
        'HM' => 'AU', // Îles Heard-et-MacDonald
        'IM' => 'GB', // Île de Man
        'IO' => 'GB', // Territoire britannique de l\'océan Indien
        'JE' => 'GB', // Jersey
        'KY' => 'GB', // Îles Caïmans
        'MF' => 'FR', // Saint-Martin
        'MO' => 'CN', // Macao
        'MP' => 'US', // Îles Mariannes du Nord
        'MQ' => 'FR', // Martinique
        'MS' => 'GB', // Montserrat
        'NC' => 'FR', // Nouvelle-Calédonie
        'NF' => 'AU', // Île Norfolk
        'NU' => 'NZ', // Niue
        'PF' => 'FR', // Polynésie française
        'PM' => 'FR', // Saint-Pierre-et-Miquelon
        'PN' => 'GB', // Îles Pitcairn
        'PR' => 'US', // Porto Rico
        'RE' => 'FR', // La Réunion
        'SH' => 'GB', // Sainte-Hélène, Ascension et Tristan da Cunha
        'SJ' => 'NO', // Svalbard et Jan Mayen
        'SX' => 'NL', // Sint Maarten
        'TC' => 'GB', // Îles Turques-et-Caïques
        'TF' => 'FR', // Terres australes et antarctiques françaises
        'TK' => 'NZ', // Tokelau
        'UM' => 'US', // Îles mineures éloignées des États-Unis
        'VG' => 'GB', // Îles Vierges britanniques
        'VI' => 'US', // Îles Vierges des États-Unis
        'WF' => 'FR', // Wallis-et-Futuna
        'YT' => 'FR', // Mayotte
        // Le reste sera ajouté au fil des lots suivants
    ];

    public function run(): void
    {
        $countries = [
            ['code_2' => 'ad', 'code' => 'AND', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+376', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Andorre', 'slug' => 'andorre', 'description' => 'Petite principauté nichée dans les Pyrénées.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Andorra', 'slug' => 'andorra', 'description' => 'A small principality nestled in the Pyrenees.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'ae', 'code' => 'ARE', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+971', 'currency_code' => 'AED', 'currency_symbol' => 'د.إ',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Émirats arabes unis', 'slug' => 'emirats-arabes-unis', 'description' => 'Fédération de sept émirats du golfe Persique.', 'currency_name' => 'Dirham des Émirats arabes unis'],
                    ['lang_code' => 'en', 'name' => 'United Arab Emirates', 'slug' => 'united-arab-emirates', 'description' => 'A federation of seven emirates on the Persian Gulf.', 'currency_name' => 'UAE Dirham'],
                ],
            ],
            ['code_2' => 'af', 'code' => 'AFG', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+93', 'currency_code' => 'AFN', 'currency_symbol' => '؋',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Afghanistan', 'slug' => 'afghanistan', 'description' => 'Pays montagneux d\'Asie centrale et du Sud.', 'currency_name' => 'Afghani afghan'],
                    ['lang_code' => 'en', 'name' => 'Afghanistan', 'slug' => 'afghanistan', 'description' => 'A mountainous country in Central and South Asia.', 'currency_name' => 'Afghan Afghani'],
                ],
            ],
            ['code_2' => 'ag', 'code' => 'ATG', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1268', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Antigua-et-Barbuda', 'slug' => 'antigua-et-barbuda', 'description' => 'État insulaire des Caraïbes orientales.', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Antigua and Barbuda', 'slug' => 'antigua-and-barbuda', 'description' => 'An island nation in the eastern Caribbean.', 'currency_name' => 'East Caribbean Dollar'],
                ],
            ],
            ['code_2' => 'ai', 'code' => 'AIA', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1264', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Anguilla', 'slug' => 'anguilla', 'description' => 'Territoire britannique d\'outre-mer des Caraïbes.', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Anguilla', 'slug' => 'anguilla', 'description' => 'A British Overseas Territory in the Caribbean.', 'currency_name' => 'East Caribbean Dollar'],
                ],
            ],
            ['code_2' => 'al', 'code' => 'ALB', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+355', 'currency_code' => 'ALL', 'currency_symbol' => 'L',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Albanie', 'slug' => 'albanie', 'description' => 'Pays des Balkans bordant la mer Adriatique.', 'currency_name' => 'Lek albanais'],
                    ['lang_code' => 'en', 'name' => 'Albania', 'slug' => 'albania', 'description' => 'A Balkan country on the Adriatic Sea.', 'currency_name' => 'Albanian Lek'],
                ],
            ],
            ['code_2' => 'am', 'code' => 'ARM', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+374', 'currency_code' => 'AMD', 'currency_symbol' => '֏',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Arménie', 'slug' => 'armenie', 'description' => 'Pays montagneux du Caucase du Sud.', 'currency_name' => 'Dram arménien'],
                    ['lang_code' => 'en', 'name' => 'Armenia', 'slug' => 'armenia', 'description' => 'A mountainous country in the South Caucasus.', 'currency_name' => 'Armenian Dram'],
                ],
            ],
            ['code_2' => 'ao', 'code' => 'AGO', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+244', 'currency_code' => 'AOA', 'currency_symbol' => 'Kz',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Angola', 'slug' => 'angola', 'description' => 'Pays d\'Afrique australe riche en pétrole.', 'currency_name' => 'Kwanza angolais'],
                    ['lang_code' => 'en', 'name' => 'Angola', 'slug' => 'angola', 'description' => 'A Southern African country rich in oil.', 'currency_name' => 'Angolan Kwanza'],
                ],
            ],
            ['code_2' => 'aq', 'code' => 'ATA', 'continent_code' => 'AN', 'type' => 'territory', 'phone_code' => null, 'currency_code' => null, 'currency_symbol' => null,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Antarctique', 'slug' => 'antarctique-territoire', 'description' => 'Continent glacé sans souveraineté nationale unique.', 'currency_name' => null],
                    ['lang_code' => 'en', 'name' => 'Antarctica', 'slug' => 'antarctica-territory', 'description' => 'An icy continent with no single national sovereignty.', 'currency_name' => null],
                ],
            ],
            ['code_2' => 'ar', 'code' => 'ARG', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+54', 'currency_code' => 'ARS', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Argentine', 'slug' => 'argentine', 'description' => 'Vaste pays d\'Amérique du Sud, du tango aux Andes.', 'currency_name' => 'Peso argentin'],
                    ['lang_code' => 'en', 'name' => 'Argentina', 'slug' => 'argentina', 'description' => 'A vast South American country, from tango to the Andes.', 'currency_name' => 'Argentine Peso'],
                ],
            ],
            ['code_2' => 'as', 'code' => 'ASM', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+1684', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Samoa américaines', 'slug' => 'samoa-americaines', 'description' => 'Territoire non incorporé des États-Unis en Océanie.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'American Samoa', 'slug' => 'american-samoa', 'description' => 'An unincorporated U.S. territory in Oceania.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'at', 'code' => 'AUT', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+43', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Autriche', 'slug' => 'autriche', 'description' => 'Pays alpin d\'Europe centrale.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Austria', 'slug' => 'austria', 'description' => 'An alpine country in Central Europe.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'au', 'code' => 'AUS', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+61', 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Australie', 'slug' => 'australie', 'description' => 'Pays-continent d\'Océanie.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Australia', 'slug' => 'australia', 'description' => 'A continent-country in Oceania.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'aw', 'code' => 'ABW', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+297', 'currency_code' => 'AWG', 'currency_symbol' => 'ƒ',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Aruba', 'slug' => 'aruba', 'description' => 'Île des Caraïbes du royaume des Pays-Bas.', 'currency_name' => 'Florin arubais'],
                    ['lang_code' => 'en', 'name' => 'Aruba', 'slug' => 'aruba', 'description' => 'A Caribbean island of the Kingdom of the Netherlands.', 'currency_name' => 'Aruban Florin'],
                ],
            ],
            ['code_2' => 'ax', 'code' => 'ALA', 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+358 18', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Åland', 'slug' => 'iles-aland', 'description' => 'Archipel autonome finlandais en mer Baltique.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Åland Islands', 'slug' => 'aland-islands', 'description' => 'An autonomous Finnish archipelago in the Baltic Sea.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'az', 'code' => 'AZE', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+994', 'currency_code' => 'AZN', 'currency_symbol' => '₼',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Azerbaïdjan', 'slug' => 'azerbaidjan', 'description' => 'Pays du Caucase bordant la mer Caspienne.', 'currency_name' => 'Manat azerbaïdjanais'],
                    ['lang_code' => 'en', 'name' => 'Azerbaijan', 'slug' => 'azerbaijan', 'description' => 'A Caucasus country on the Caspian Sea.', 'currency_name' => 'Azerbaijani Manat'],
                ],
            ],
            ['code_2' => 'ba', 'code' => 'BIH', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+387', 'currency_code' => 'BAM', 'currency_symbol' => 'KM',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bosnie-Herzégovine', 'slug' => 'bosnie-herzegovine', 'description' => 'Pays des Balkans à la mosaïque culturelle.', 'currency_name' => 'Mark convertible de Bosnie-Herzégovine'],
                    ['lang_code' => 'en', 'name' => 'Bosnia and Herzegovina', 'slug' => 'bosnia-and-herzegovina', 'description' => 'A Balkan country with a rich cultural mosaic.', 'currency_name' => 'Bosnia-Herzegovina Convertible Mark'],
                ],
            ],
            ['code_2' => 'bb', 'code' => 'BRB', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1246', 'currency_code' => 'BBD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Barbade', 'slug' => 'barbade', 'description' => 'Île des Caraïbes orientales.', 'currency_name' => 'Dollar barbadien'],
                    ['lang_code' => 'en', 'name' => 'Barbados', 'slug' => 'barbados', 'description' => 'An island in the eastern Caribbean.', 'currency_name' => 'Barbadian Dollar'],
                ],
            ],
            ['code_2' => 'bd', 'code' => 'BGD', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+880', 'currency_code' => 'BDT', 'currency_symbol' => '৳',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bangladesh', 'slug' => 'bangladesh', 'description' => 'Pays très peuplé du delta du Gange.', 'currency_name' => 'Taka bangladais'],
                    ['lang_code' => 'en', 'name' => 'Bangladesh', 'slug' => 'bangladesh', 'description' => 'A densely populated country in the Ganges delta.', 'currency_name' => 'Bangladeshi Taka'],
                ],
            ],
            ['code_2' => 'be', 'code' => 'BEL', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+32', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Belgique', 'slug' => 'belgique', 'description' => 'Pays d\'Europe occidentale, siège de nombreuses institutions.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Belgium', 'slug' => 'belgium', 'description' => 'A Western European country hosting many institutions.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'bf', 'code' => 'BFA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+226', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Burkina Faso', 'slug' => 'burkina-faso', 'description' => 'Pays sahélien d\'Afrique de l\'Ouest.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Burkina Faso', 'slug' => 'burkina-faso', 'description' => 'A Sahelian country in West Africa.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'bg', 'code' => 'BGR', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+359', 'currency_code' => 'BGN', 'currency_symbol' => 'лв',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bulgarie', 'slug' => 'bulgarie', 'description' => 'Pays des Balkans bordant la mer Noire.', 'currency_name' => 'Lev bulgare'],
                    ['lang_code' => 'en', 'name' => 'Bulgaria', 'slug' => 'bulgaria', 'description' => 'A Balkan country on the Black Sea.', 'currency_name' => 'Bulgarian Lev'],
                ],
            ],
            ['code_2' => 'bh', 'code' => 'BHR', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+973', 'currency_code' => 'BHD', 'currency_symbol' => '.د.ب',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bahreïn', 'slug' => 'bahrein', 'description' => 'Petit royaume insulaire du golfe Persique.', 'currency_name' => 'Dinar bahreïni'],
                    ['lang_code' => 'en', 'name' => 'Bahrain', 'slug' => 'bahrain', 'description' => 'A small island kingdom in the Persian Gulf.', 'currency_name' => 'Bahraini Dinar'],
                ],
            ],
            ['code_2' => 'bi', 'code' => 'BDI', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+257', 'currency_code' => 'BIF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Burundi', 'slug' => 'burundi', 'description' => 'Petit pays de la région des Grands Lacs africains.', 'currency_name' => 'Franc burundais'],
                    ['lang_code' => 'en', 'name' => 'Burundi', 'slug' => 'burundi', 'description' => 'A small country in Africa\'s Great Lakes region.', 'currency_name' => 'Burundian Franc'],
                ],
            ],
            ['code_2' => 'bj', 'code' => 'BEN', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+229', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bénin', 'slug' => 'benin', 'description' => 'Pays d\'Afrique de l\'Ouest bordant le golfe de Guinée.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Benin', 'slug' => 'benin', 'description' => 'A West African country on the Gulf of Guinea.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'bl', 'code' => 'BLM', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+590', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Saint-Barthélemy', 'slug' => 'saint-barthelemy', 'description' => 'Collectivité française des Caraïbes.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Saint Barthélemy', 'slug' => 'saint-barthelemy', 'description' => 'A French collectivity in the Caribbean.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'bm', 'code' => 'BMU', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1441', 'currency_code' => 'BMD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bermudes', 'slug' => 'bermudes', 'description' => 'Territoire britannique d\'outre-mer de l\'Atlantique Nord.', 'currency_name' => 'Dollar bermudien'],
                    ['lang_code' => 'en', 'name' => 'Bermuda', 'slug' => 'bermuda', 'description' => 'A British Overseas Territory in the North Atlantic.', 'currency_name' => 'Bermudian Dollar'],
                ],
            ],
            ['code_2' => 'bn', 'code' => 'BRN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+673', 'currency_code' => 'BND', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Brunei', 'slug' => 'brunei', 'description' => 'Petit sultanat pétrolier de l\'île de Bornéo.', 'currency_name' => 'Dollar brunéien'],
                    ['lang_code' => 'en', 'name' => 'Brunei', 'slug' => 'brunei', 'description' => 'A small oil-rich sultanate on the island of Borneo.', 'currency_name' => 'Brunei Dollar'],
                ],
            ],
            ['code_2' => 'bo', 'code' => 'BOL', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+591', 'currency_code' => 'BOB', 'currency_symbol' => 'Bs.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bolivie', 'slug' => 'bolivie', 'description' => 'Pays andin sans littoral, riche en biodiversité.', 'currency_name' => 'Boliviano bolivien'],
                    ['lang_code' => 'en', 'name' => 'Bolivia', 'slug' => 'bolivia', 'description' => 'A landlocked Andean country rich in biodiversity.', 'currency_name' => 'Bolivian Boliviano'],
                ],
            ],
            ['code_2' => 'bq', 'code' => 'BES', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+599', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bonaire, Saint-Eustache et Saba', 'slug' => 'bonaire-saint-eustache-saba', 'description' => 'Communes néerlandaises des Caraïbes.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Bonaire, Sint Eustatius and Saba', 'slug' => 'bonaire-sint-eustatius-saba', 'description' => 'Dutch Caribbean municipalities.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'br', 'code' => 'BRA', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+55', 'currency_code' => 'BRL', 'currency_symbol' => 'R$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Brésil', 'slug' => 'bresil', 'description' => 'Plus vaste pays d\'Amérique du Sud.', 'currency_name' => 'Réal brésilien'],
                    ['lang_code' => 'en', 'name' => 'Brazil', 'slug' => 'brazil', 'description' => 'The largest country in South America.', 'currency_name' => 'Brazilian Real'],
                ],
            ],
            ['code_2' => 'bs', 'code' => 'BHS', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1242', 'currency_code' => 'BSD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bahamas', 'slug' => 'bahamas', 'description' => 'Archipel des Caraïbes réputé pour ses plages.', 'currency_name' => 'Dollar bahaméen'],
                    ['lang_code' => 'en', 'name' => 'Bahamas', 'slug' => 'bahamas', 'description' => 'A Caribbean archipelago known for its beaches.', 'currency_name' => 'Bahamian Dollar'],
                ],
            ],
            ['code_2' => 'bt', 'code' => 'BTN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+975', 'currency_code' => 'BTN', 'currency_symbol' => 'Nu.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bhoutan', 'slug' => 'bhoutan', 'description' => 'Royaume himalayen connu pour le Bonheur National Brut.', 'currency_name' => 'Ngultrum bhoutanais'],
                    ['lang_code' => 'en', 'name' => 'Bhutan', 'slug' => 'bhutan', 'description' => 'A Himalayan kingdom known for Gross National Happiness.', 'currency_name' => 'Bhutanese Ngultrum'],
                ],
            ],
            ['code_2' => 'bv', 'code' => 'BVT', 'continent_code' => 'AN', 'type' => 'territory', 'phone_code' => null, 'currency_code' => 'NOK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Île Bouvet', 'slug' => 'ile-bouvet', 'description' => 'Île inhabitée norvégienne de l\'Atlantique Sud.', 'currency_name' => 'Couronne norvégienne'],
                    ['lang_code' => 'en', 'name' => 'Bouvet Island', 'slug' => 'bouvet-island', 'description' => 'An uninhabited Norwegian island in the South Atlantic.', 'currency_name' => 'Norwegian Krone'],
                ],
            ],
            ['code_2' => 'bw', 'code' => 'BWA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+267', 'currency_code' => 'BWP', 'currency_symbol' => 'P',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Botswana', 'slug' => 'botswana', 'description' => 'Pays d\'Afrique australe connu pour ses réserves naturelles.', 'currency_name' => 'Pula botswanais'],
                    ['lang_code' => 'en', 'name' => 'Botswana', 'slug' => 'botswana', 'description' => 'A Southern African country known for its wildlife reserves.', 'currency_name' => 'Botswana Pula'],
                ],
            ],
            ['code_2' => 'by', 'code' => 'BLR', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+375', 'currency_code' => 'BYN', 'currency_symbol' => 'Br',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bélarus', 'slug' => 'belarus', 'description' => 'Pays d\'Europe de l\'Est sans accès à la mer.', 'currency_name' => 'Rouble biélorusse'],
                    ['lang_code' => 'en', 'name' => 'Belarus', 'slug' => 'belarus', 'description' => 'A landlocked Eastern European country.', 'currency_name' => 'Belarusian Ruble'],
                ],
            ],
            ['code_2' => 'bz', 'code' => 'BLZ', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+501', 'currency_code' => 'BZD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Belize', 'slug' => 'belize', 'description' => 'Pays d\'Amérique centrale bordant la mer des Caraïbes.', 'currency_name' => 'Dollar bélizéen'],
                    ['lang_code' => 'en', 'name' => 'Belize', 'slug' => 'belize', 'description' => 'A Central American country on the Caribbean Sea.', 'currency_name' => 'Belize Dollar'],
                ],
            ],
            ['code_2' => 'ca', 'code' => 'CAN', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1', 'currency_code' => 'CAD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Canada', 'slug' => 'canada', 'description' => 'Deuxième plus grand pays du monde par la superficie.', 'currency_name' => 'Dollar canadien'],
                    ['lang_code' => 'en', 'name' => 'Canada', 'slug' => 'canada', 'description' => 'The second largest country in the world by area.', 'currency_name' => 'Canadian Dollar'],
                ],
            ],
            ['code_2' => 'cc', 'code' => 'CCK', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+61', 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Cocos', 'slug' => 'iles-cocos', 'description' => 'Territoire australien de l\'océan Indien.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Cocos (Keeling) Islands', 'slug' => 'cocos-keeling-islands', 'description' => 'An Australian territory in the Indian Ocean.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'cd', 'code' => 'COD', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+243', 'currency_code' => 'CDF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'RD Congo', 'slug' => 'rd-congo', 'description' => 'Vaste pays d\'Afrique centrale, bassin du fleuve Congo.', 'currency_name' => 'Franc congolais'],
                    ['lang_code' => 'en', 'name' => 'DR Congo', 'slug' => 'dr-congo', 'description' => 'A vast Central African country in the Congo River basin.', 'currency_name' => 'Congolese Franc'],
                ],
            ],
            ['code_2' => 'cf', 'code' => 'CAF', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+236', 'currency_code' => 'XAF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'République centrafricaine', 'slug' => 'republique-centrafricaine', 'description' => 'Pays enclavé au cœur de l\'Afrique.', 'currency_name' => 'Franc CFA (CEMAC)'],
                    ['lang_code' => 'en', 'name' => 'Central African Republic', 'slug' => 'central-african-republic', 'description' => 'A landlocked country at the heart of Africa.', 'currency_name' => 'Central African CFA Franc'],
                ],
            ],
            ['code_2' => 'cg', 'code' => 'COG', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+242', 'currency_code' => 'XAF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Congo', 'slug' => 'congo', 'description' => 'Pays d\'Afrique centrale bordant l\'Atlantique.', 'currency_name' => 'Franc CFA (CEMAC)'],
                    ['lang_code' => 'en', 'name' => 'Congo', 'slug' => 'congo', 'description' => 'A Central African country on the Atlantic coast.', 'currency_name' => 'Central African CFA Franc'],
                ]],

            // --- Lot 2 : CH -> GF ---
            ['code_2' => 'ch', 'code' => 'CHE', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+41', 'currency_code' => 'CHF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Suisse', 'slug' => 'suisse', 'description' => 'Pays alpin connu pour sa neutralité et ses banques.', 'currency_name' => 'Franc suisse'],
                    ['lang_code' => 'en', 'name' => 'Switzerland', 'slug' => 'switzerland', 'description' => 'An alpine country known for neutrality and banking.', 'currency_name' => 'Swiss Franc'],
                ],
            ],
            ['code_2' => 'ci', 'code' => 'CIV', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+225', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Côte d\'Ivoire', 'slug' => 'cote-divoire', 'description' => 'Pays d\'Afrique de l\'Ouest, premier producteur mondial de cacao.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Côte d\'Ivoire', 'slug' => 'cote-divoire', 'description' => 'A West African country, the world\'s top cocoa producer.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'ck', 'code' => 'COK', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+682', 'currency_code' => 'NZD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Cook', 'slug' => 'iles-cook', 'description' => 'État librement associé à la Nouvelle-Zélande, dans le Pacifique Sud.', 'currency_name' => 'Dollar néo-zélandais'],
                    ['lang_code' => 'en', 'name' => 'Cook Islands', 'slug' => 'cook-islands', 'description' => 'A state in free association with New Zealand, in the South Pacific.', 'currency_name' => 'New Zealand Dollar'],
                ],
            ],
            ['code_2' => 'cl', 'code' => 'CHL', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+56', 'currency_code' => 'CLP', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Chili', 'slug' => 'chili', 'description' => 'Pays tout en longueur le long de la côte pacifique.', 'currency_name' => 'Peso chilien'],
                    ['lang_code' => 'en', 'name' => 'Chile', 'slug' => 'chile', 'description' => 'A long, narrow country along the Pacific coast.', 'currency_name' => 'Chilean Peso'],
                ],
            ],
            ['code_2' => 'cm', 'code' => 'CMR', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+237', 'currency_code' => 'XAF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Cameroun', 'slug' => 'cameroun', 'description' => 'Pays d\'Afrique centrale surnommé "l\'Afrique en miniature".', 'currency_name' => 'Franc CFA (CEMAC)'],
                    ['lang_code' => 'en', 'name' => 'Cameroon', 'slug' => 'cameroon', 'description' => 'A Central African country nicknamed "Africa in miniature".', 'currency_name' => 'Central African CFA Franc'],
                ],
            ],
            ['code_2' => 'cn', 'code' => 'CHN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+86', 'currency_code' => 'CNY', 'currency_symbol' => '¥',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Chine', 'slug' => 'chine', 'description' => 'Pays le plus peuplé du monde.', 'currency_name' => 'Yuan renminbi chinois'],
                    ['lang_code' => 'en', 'name' => 'China', 'slug' => 'china', 'description' => 'The world\'s most populous country.', 'currency_name' => 'Chinese Yuan Renminbi'],
                ],
            ],
            ['code_2' => 'co', 'code' => 'COL', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+57', 'currency_code' => 'COP', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Colombie', 'slug' => 'colombie', 'description' => 'Pays d\'Amérique du Sud entre Pacifique et Caraïbes.', 'currency_name' => 'Peso colombien'],
                    ['lang_code' => 'en', 'name' => 'Colombia', 'slug' => 'colombia', 'description' => 'A South American country between the Pacific and the Caribbean.', 'currency_name' => 'Colombian Peso'],
                ],
            ],
            ['code_2' => 'cr', 'code' => 'CRI', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+506', 'currency_code' => 'CRC', 'currency_symbol' => '₡',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Costa Rica', 'slug' => 'costa-rica', 'description' => 'Pays d\'Amérique centrale réputé pour son écotourisme.', 'currency_name' => 'Colón costaricien'],
                    ['lang_code' => 'en', 'name' => 'Costa Rica', 'slug' => 'costa-rica', 'description' => 'A Central American country known for ecotourism.', 'currency_name' => 'Costa Rican Colón'],
                ],
            ],
            ['code_2' => 'cu', 'code' => 'CUB', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+53', 'currency_code' => 'CUP', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Cuba', 'slug' => 'cuba', 'description' => 'Plus grande île des Caraïbes.', 'currency_name' => 'Peso cubain'],
                    ['lang_code' => 'en', 'name' => 'Cuba', 'slug' => 'cuba', 'description' => 'The largest island in the Caribbean.', 'currency_name' => 'Cuban Peso'],
                ],
            ],
            ['code_2' => 'cv', 'code' => 'CPV', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+238', 'currency_code' => 'CVE', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Cap-Vert', 'slug' => 'cap-vert', 'description' => 'Archipel d\'Afrique de l\'Ouest dans l\'Atlantique.', 'currency_name' => 'Escudo cap-verdien'],
                    ['lang_code' => 'en', 'name' => 'Cabo Verde', 'slug' => 'cabo-verde', 'description' => 'A West African archipelago in the Atlantic.', 'currency_name' => 'Cape Verdean Escudo'],
                ],
            ],
            ['code_2' => 'cw', 'code' => 'CUW', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+599', 'currency_code' => 'ANG', 'currency_symbol' => 'ƒ',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Curaçao', 'slug' => 'curacao', 'description' => 'Île des Caraïbes du royaume des Pays-Bas.', 'currency_name' => 'Florin des Antilles néerlandaises'],
                    ['lang_code' => 'en', 'name' => 'Curaçao', 'slug' => 'curacao', 'description' => 'A Caribbean island of the Kingdom of the Netherlands.', 'currency_name' => 'Netherlands Antillean Guilder'],
                ],
            ],
            ['code_2' => 'cx', 'code' => 'CXR', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+61', 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Île Christmas', 'slug' => 'ile-christmas', 'description' => 'Territoire australien dans l\'océan Indien.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Christmas Island', 'slug' => 'christmas-island', 'description' => 'An Australian territory in the Indian Ocean.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'cy', 'code' => 'CYP', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+357', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Chypre', 'slug' => 'chypre', 'description' => 'Île méditerranéenne membre de l\'Union européenne.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Cyprus', 'slug' => 'cyprus', 'description' => 'A Mediterranean island and EU member state.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'cz', 'code' => 'CZE', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+420', 'currency_code' => 'CZK', 'currency_symbol' => 'Kč',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tchéquie', 'slug' => 'tchequie', 'description' => 'Pays d\'Europe centrale, ancienne partie de la Tchécoslovaquie.', 'currency_name' => 'Couronne tchèque'],
                    ['lang_code' => 'en', 'name' => 'Czechia', 'slug' => 'czechia', 'description' => 'A Central European country, formerly part of Czechoslovakia.', 'currency_name' => 'Czech Koruna'],
                ],
            ],
            ['code_2' => 'de', 'code' => 'DEU', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+49', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Allemagne', 'slug' => 'allemagne', 'description' => 'Première puissance économique d\'Europe.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Germany', 'slug' => 'germany', 'description' => 'Europe\'s largest economy.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'dj', 'code' => 'DJI', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+253', 'currency_code' => 'DJF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Djibouti', 'slug' => 'djibouti', 'description' => 'Petit pays de la Corne de l\'Afrique.', 'currency_name' => 'Franc djiboutien'],
                    ['lang_code' => 'en', 'name' => 'Djibouti', 'slug' => 'djibouti', 'description' => 'A small country in the Horn of Africa.', 'currency_name' => 'Djiboutian Franc'],
                ],
            ],
            ['code_2' => 'dk', 'code' => 'DNK', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+45', 'currency_code' => 'DKK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Danemark', 'slug' => 'danemark', 'description' => 'Royaume scandinave, souvent classé parmi les pays les plus heureux.', 'currency_name' => 'Couronne danoise'],
                    ['lang_code' => 'en', 'name' => 'Denmark', 'slug' => 'denmark', 'description' => 'A Scandinavian kingdom, often ranked among the happiest countries.', 'currency_name' => 'Danish Krone'],
                ],
            ],
            ['code_2' => 'dm', 'code' => 'DMA', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1767', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Dominique', 'slug' => 'dominique', 'description' => 'Île volcanique des Caraïbes orientales.', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Dominica', 'slug' => 'dominica', 'description' => 'A volcanic island in the eastern Caribbean.', 'currency_name' => 'East Caribbean Dollar'],
                ],
            ],
            ['code_2' => 'do', 'code' => 'DOM', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1809', 'currency_code' => 'DOP', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'République dominicaine', 'slug' => 'republique-dominicaine', 'description' => 'Pays des Caraïbes partageant l\'île d\'Hispaniola avec Haïti.', 'currency_name' => 'Peso dominicain'],
                    ['lang_code' => 'en', 'name' => 'Dominican Republic', 'slug' => 'dominican-republic', 'description' => 'A Caribbean country sharing Hispaniola with Haiti.', 'currency_name' => 'Dominican Peso'],
                ],
            ],
            ['code_2' => 'dz', 'code' => 'DZA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+213', 'currency_code' => 'DZD', 'currency_symbol' => 'د.ج',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Algérie', 'slug' => 'algerie', 'description' => 'Plus grand pays d\'Afrique par la superficie.', 'currency_name' => 'Dinar algérien'],
                    ['lang_code' => 'en', 'name' => 'Algeria', 'slug' => 'algeria', 'description' => 'The largest country in Africa by area.', 'currency_name' => 'Algerian Dinar'],
                ],
            ],
            ['code_2' => 'ec', 'code' => 'ECU', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+593', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Équateur', 'slug' => 'equateur', 'description' => 'Pays d\'Amérique du Sud abritant les îles Galápagos.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Ecuador', 'slug' => 'ecuador', 'description' => 'A South American country home to the Galápagos Islands.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'ee', 'code' => 'EST', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+372', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Estonie', 'slug' => 'estonie', 'description' => 'Pays balte pionnier du numérique.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Estonia', 'slug' => 'estonia', 'description' => 'A Baltic country and digital pioneer.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'eg', 'code' => 'EGY', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+20', 'currency_code' => 'EGP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Égypte', 'slug' => 'egypte', 'description' => 'Pays du Nil, berceau d\'une civilisation millénaire.', 'currency_name' => 'Livre égyptienne'],
                    ['lang_code' => 'en', 'name' => 'Egypt', 'slug' => 'egypt', 'description' => 'The country of the Nile, cradle of an ancient civilization.', 'currency_name' => 'Egyptian Pound'],
                ],
            ],
            ['code_2' => 'eh', 'code' => 'ESH', 'continent_code' => 'AF', 'type' => 'disputed', 'phone_code' => '+212', 'currency_code' => 'MAD', 'currency_symbol' => 'د.م.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Sahara occidental', 'slug' => 'sahara-occidental', 'description' => 'Territoire d\'Afrique du Nord au statut contesté.', 'currency_name' => 'Dirham marocain'],
                    ['lang_code' => 'en', 'name' => 'Western Sahara', 'slug' => 'western-sahara', 'description' => 'A North African territory with disputed status.', 'currency_name' => 'Moroccan Dirham'],
                ],
            ],
            ['code_2' => 'er', 'code' => 'ERI', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+291', 'currency_code' => 'ERN', 'currency_symbol' => 'Nfk',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Érythrée', 'slug' => 'erythree', 'description' => 'Pays de la Corne de l\'Afrique bordant la mer Rouge.', 'currency_name' => 'Nakfa érythréen'],
                    ['lang_code' => 'en', 'name' => 'Eritrea', 'slug' => 'eritrea', 'description' => 'A Horn of Africa country on the Red Sea.', 'currency_name' => 'Eritrean Nakfa'],
                ],
            ],
            ['code_2' => 'es', 'code' => 'ESP', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+34', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Espagne', 'slug' => 'espagne', 'description' => 'Pays d\'Europe du Sud, entre Atlantique et Méditerranée.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Spain', 'slug' => 'spain', 'description' => 'A Southern European country between the Atlantic and the Mediterranean.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'et', 'code' => 'ETH', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+251', 'currency_code' => 'ETB', 'currency_symbol' => 'Br',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Éthiopie', 'slug' => 'ethiopie', 'description' => 'Pays d\'Afrique de l\'Est jamais colonisé durablement.', 'currency_name' => 'Birr éthiopien'],
                    ['lang_code' => 'en', 'name' => 'Ethiopia', 'slug' => 'ethiopia', 'description' => 'An East African country never durably colonized.', 'currency_name' => 'Ethiopian Birr'],
                ],
            ],
            ['code_2' => 'fi', 'code' => 'FIN', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+358', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Finlande', 'slug' => 'finlande', 'description' => 'Pays nordique aux mille lacs.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Finland', 'slug' => 'finland', 'description' => 'A Nordic country with a thousand lakes.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'fj', 'code' => 'FJI', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+679', 'currency_code' => 'FJD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Fidji', 'slug' => 'fidji', 'description' => 'Archipel du Pacifique Sud.', 'currency_name' => 'Dollar fidjien'],
                    ['lang_code' => 'en', 'name' => 'Fiji', 'slug' => 'fiji', 'description' => 'A South Pacific archipelago.', 'currency_name' => 'Fijian Dollar'],
                ],
            ],
            ['code_2' => 'fk', 'code' => 'FLK', 'continent_code' => 'SA', 'type' => 'territory', 'phone_code' => '+500', 'currency_code' => 'FKP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Falkland', 'slug' => 'iles-falkland', 'description' => 'Territoire britannique d\'outre-mer de l\'Atlantique Sud.', 'currency_name' => 'Livre des îles Falkland'],
                    ['lang_code' => 'en', 'name' => 'Falkland Islands', 'slug' => 'falkland-islands', 'description' => 'A British Overseas Territory in the South Atlantic.', 'currency_name' => 'Falkland Islands Pound'],
                ],
            ],
            ['code_2' => 'fm', 'code' => 'FSM', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+691', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Micronésie', 'slug' => 'micronesie', 'description' => 'État fédéral du Pacifique composé de centaines d\'îles.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Micronesia', 'slug' => 'micronesia', 'description' => 'A federal Pacific state made up of hundreds of islands.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'fo', 'code' => 'FRO', 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+298', 'currency_code' => 'DKK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Féroé', 'slug' => 'iles-feroe', 'description' => 'Archipel autonome du royaume du Danemark.', 'currency_name' => 'Couronne danoise'],
                    ['lang_code' => 'en', 'name' => 'Faroe Islands', 'slug' => 'faroe-islands', 'description' => 'An autonomous archipelago of the Kingdom of Denmark.', 'currency_name' => 'Danish Krone'],
                ],
            ],
            ['code_2' => 'fr', 'code' => 'FRA', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+33', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'France', 'slug' => 'france', 'description' => 'Pays d\'Europe occidentale, de la métropole aux outre-mer.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'France', 'slug' => 'france', 'description' => 'A Western European country, from the mainland to its overseas territories.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'ga', 'code' => 'GAB', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+241', 'currency_code' => 'XAF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Gabon', 'slug' => 'gabon', 'description' => 'Pays d\'Afrique centrale largement recouvert de forêt équatoriale.', 'currency_name' => 'Franc CFA (CEMAC)'],
                    ['lang_code' => 'en', 'name' => 'Gabon', 'slug' => 'gabon', 'description' => 'A Central African country largely covered by rainforest.', 'currency_name' => 'Central African CFA Franc'],
                ],
            ],
            ['code_2' => 'gb', 'code' => 'GBR', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+44', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Royaume-Uni', 'slug' => 'royaume-uni', 'description' => 'Union de l\'Angleterre, l\'Écosse, le Pays de Galles et l\'Irlande du Nord.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'United Kingdom', 'slug' => 'united-kingdom', 'description' => 'A union of England, Scotland, Wales and Northern Ireland.', 'currency_name' => 'British Pound Sterling'],
                ]],
            // Nations constitutives du Royaume-Uni (codes étendus, non-ISO 3166-1)
            ['code_2' => 'gb-eng', 'code' => null, 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+44', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Angleterre', 'slug' => 'angleterre', 'description' => 'Nation constitutive du Royaume-Uni.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'England', 'slug' => 'england', 'description' => 'A constituent country of the United Kingdom.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'gb-nir', 'code' => null, 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+44', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Irlande du Nord', 'slug' => 'irlande-du-nord', 'description' => 'Nation constitutive du Royaume-Uni.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'Northern Ireland', 'slug' => 'northern-ireland', 'description' => 'A constituent country of the United Kingdom.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'gb-sct', 'code' => null, 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+44', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Écosse', 'slug' => 'ecosse', 'description' => 'Nation constitutive du Royaume-Uni.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'Scotland', 'slug' => 'scotland', 'description' => 'A constituent country of the United Kingdom.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'gb-wls', 'code' => null, 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+44', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Pays de Galles', 'slug' => 'pays-de-galles', 'description' => 'Nation constitutive du Royaume-Uni.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'Wales', 'slug' => 'wales', 'description' => 'A constituent country of the United Kingdom.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'gd', 'code' => 'GRD', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1473', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Grenade', 'slug' => 'grenade', 'description' => 'Île des Caraïbes surnommée "l\'île aux épices".', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Grenada', 'slug' => 'grenada', 'description' => 'A Caribbean island nicknamed "the spice isle".', 'currency_name' => 'East Caribbean Dollar'],
                ],
            ],
            ['code_2' => 'ge', 'code' => 'GEO', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+995', 'currency_code' => 'GEL', 'currency_symbol' => '₾',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Géorgie', 'slug' => 'georgie', 'description' => 'Pays du Caucase entre mer Noire et montagnes.', 'currency_name' => 'Lari géorgien'],
                    ['lang_code' => 'en', 'name' => 'Georgia', 'slug' => 'georgia', 'description' => 'A Caucasus country between the Black Sea and the mountains.', 'currency_name' => 'Georgian Lari'],
                ],
            ],
            ['code_2' => 'gf', 'code' => 'GUF', 'continent_code' => 'SA', 'type' => 'territory', 'phone_code' => '+594', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guyane française', 'slug' => 'guyane-francaise', 'description' => 'Territoire français d\'Amérique du Sud.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'French Guiana', 'slug' => 'french-guiana', 'description' => 'A French territory in South America.', 'currency_name' => 'Euro'],
                ]],

            // --- Lot 3 : GG -> LC ---
            ['code_2' => 'gg', 'code' => 'GGY', 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+44 1481', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guernesey', 'slug' => 'guernesey', 'description' => 'Dépendance de la Couronne britannique dans la Manche.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'Guernsey', 'slug' => 'guernsey', 'description' => 'A British Crown Dependency in the English Channel.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'gh', 'code' => 'GHA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+233', 'currency_code' => 'GHS', 'currency_symbol' => '₵',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Ghana', 'slug' => 'ghana', 'description' => 'Pays d\'Afrique de l\'Ouest bordant le golfe de Guinée.', 'currency_name' => 'Cedi ghanéen'],
                    ['lang_code' => 'en', 'name' => 'Ghana', 'slug' => 'ghana', 'description' => 'A West African country on the Gulf of Guinea.', 'currency_name' => 'Ghanaian Cedi'],
                ],
            ],
            ['code_2' => 'gi', 'code' => 'GIB', 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+350', 'currency_code' => 'GIP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Gibraltar', 'slug' => 'gibraltar', 'description' => 'Territoire britannique d\'outre-mer à la pointe de l\'Espagne.', 'currency_name' => 'Livre de Gibraltar'],
                    ['lang_code' => 'en', 'name' => 'Gibraltar', 'slug' => 'gibraltar', 'description' => 'A British Overseas Territory at the tip of Spain.', 'currency_name' => 'Gibraltar Pound'],
                ],
            ],
            ['code_2' => 'gl', 'code' => 'GRL', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+299', 'currency_code' => 'DKK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Groenland', 'slug' => 'groenland', 'description' => 'Plus grande île du monde, territoire autonome du Danemark.', 'currency_name' => 'Couronne danoise'],
                    ['lang_code' => 'en', 'name' => 'Greenland', 'slug' => 'greenland', 'description' => 'The world\'s largest island, an autonomous Danish territory.', 'currency_name' => 'Danish Krone'],
                ],
            ],
            ['code_2' => 'gm', 'code' => 'GMB', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+220', 'currency_code' => 'GMD', 'currency_symbol' => 'D',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Gambie', 'slug' => 'gambie', 'description' => 'Plus petit pays d\'Afrique continentale.', 'currency_name' => 'Dalasi gambien'],
                    ['lang_code' => 'en', 'name' => 'Gambia', 'slug' => 'gambia', 'description' => 'The smallest country in mainland Africa.', 'currency_name' => 'Gambian Dalasi'],
                ],
            ],
            ['code_2' => 'gn', 'code' => 'GIN', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+224', 'currency_code' => 'GNF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guinée', 'slug' => 'guinee', 'description' => 'Pays d\'Afrique de l\'Ouest riche en bauxite.', 'currency_name' => 'Franc guinéen'],
                    ['lang_code' => 'en', 'name' => 'Guinea', 'slug' => 'guinea', 'description' => 'A West African country rich in bauxite.', 'currency_name' => 'Guinean Franc'],
                ],
            ],
            ['code_2' => 'gp', 'code' => 'GLP', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+590', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guadeloupe', 'slug' => 'guadeloupe', 'description' => 'Archipel français des Caraïbes.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Guadeloupe', 'slug' => 'guadeloupe', 'description' => 'A French archipelago in the Caribbean.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'gq', 'code' => 'GNQ', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+240', 'currency_code' => 'XAF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guinée équatoriale', 'slug' => 'guinee-equatoriale', 'description' => 'Seul pays hispanophone d\'Afrique.', 'currency_name' => 'Franc CFA (CEMAC)'],
                    ['lang_code' => 'en', 'name' => 'Equatorial Guinea', 'slug' => 'equatorial-guinea', 'description' => 'The only Spanish-speaking country in Africa.', 'currency_name' => 'Central African CFA Franc'],
                ],
            ],
            ['code_2' => 'gr', 'code' => 'GRC', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+30', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Grèce', 'slug' => 'grece', 'description' => 'Berceau de la démocratie et de la civilisation occidentale.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Greece', 'slug' => 'greece', 'description' => 'The cradle of democracy and Western civilization.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'gs', 'code' => 'SGS', 'continent_code' => 'AN', 'type' => 'territory', 'phone_code' => '+500', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Géorgie du Sud-et-les-îles Sandwich du Sud', 'slug' => 'georgie-du-sud', 'description' => 'Territoire britannique isolé de l\'Atlantique Sud.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'South Georgia and the South Sandwich Islands', 'slug' => 'south-georgia', 'description' => 'A remote British territory in the South Atlantic.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'gt', 'code' => 'GTM', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+502', 'currency_code' => 'GTQ', 'currency_symbol' => 'Q',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guatemala', 'slug' => 'guatemala', 'description' => 'Pays d\'Amérique centrale berceau de la civilisation maya.', 'currency_name' => 'Quetzal guatémaltèque'],
                    ['lang_code' => 'en', 'name' => 'Guatemala', 'slug' => 'guatemala', 'description' => 'A Central American country and cradle of Maya civilization.', 'currency_name' => 'Guatemalan Quetzal'],
                ],
            ],
            ['code_2' => 'gu', 'code' => 'GUM', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+1671', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guam', 'slug' => 'guam', 'description' => 'Territoire non incorporé des États-Unis dans le Pacifique.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Guam', 'slug' => 'guam', 'description' => 'An unincorporated U.S. territory in the Pacific.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'gw', 'code' => 'GNB', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+245', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guinée-Bissau', 'slug' => 'guinee-bissau', 'description' => 'Pays d\'Afrique de l\'Ouest, ancienne colonie portugaise.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Guinea-Bissau', 'slug' => 'guinea-bissau', 'description' => 'A West African country and former Portuguese colony.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'gy', 'code' => 'GUY', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+592', 'currency_code' => 'GYD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Guyana', 'slug' => 'guyana', 'description' => 'Pays anglophone d\'Amérique du Sud.', 'currency_name' => 'Dollar guyanien'],
                    ['lang_code' => 'en', 'name' => 'Guyana', 'slug' => 'guyana', 'description' => 'An English-speaking country in South America.', 'currency_name' => 'Guyanese Dollar'],
                ],
            ],
            ['code_2' => 'hk', 'code' => 'HKG', 'continent_code' => 'AS', 'type' => 'special_administrative_region', 'phone_code' => '+852', 'currency_code' => 'HKD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Hong Kong', 'slug' => 'hong-kong', 'description' => 'Région administrative spéciale de la Chine.', 'currency_name' => 'Dollar de Hong Kong'],
                    ['lang_code' => 'en', 'name' => 'Hong Kong', 'slug' => 'hong-kong', 'description' => 'A special administrative region of China.', 'currency_name' => 'Hong Kong Dollar'],
                ],
            ],
            ['code_2' => 'hm', 'code' => 'HMD', 'continent_code' => 'AN', 'type' => 'territory', 'phone_code' => null, 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Heard-et-MacDonald', 'slug' => 'iles-heard-et-macdonald', 'description' => 'Territoire australien inhabité et isolé.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Heard Island and McDonald Islands', 'slug' => 'heard-mcdonald-islands', 'description' => 'An uninhabited, remote Australian territory.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'hn', 'code' => 'HND', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+504', 'currency_code' => 'HNL', 'currency_symbol' => 'L',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Honduras', 'slug' => 'honduras', 'description' => 'Pays d\'Amérique centrale bordant les Caraïbes.', 'currency_name' => 'Lempira hondurien'],
                    ['lang_code' => 'en', 'name' => 'Honduras', 'slug' => 'honduras', 'description' => 'A Central American country on the Caribbean Sea.', 'currency_name' => 'Honduran Lempira'],
                ],
            ],
            ['code_2' => 'hr', 'code' => 'HRV', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+385', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Croatie', 'slug' => 'croatie', 'description' => 'Pays des Balkans à la côte adriatique découpée.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Croatia', 'slug' => 'croatia', 'description' => 'A Balkan country with a rugged Adriatic coastline.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'ht', 'code' => 'HTI', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+509', 'currency_code' => 'HTG', 'currency_symbol' => 'G',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Haïti', 'slug' => 'haiti', 'description' => 'Première république noire indépendante du monde.', 'currency_name' => 'Gourde haïtienne'],
                    ['lang_code' => 'en', 'name' => 'Haiti', 'slug' => 'haiti', 'description' => 'The world\'s first independent Black republic.', 'currency_name' => 'Haitian Gourde'],
                ],
            ],
            ['code_2' => 'hu', 'code' => 'HUN', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+36', 'currency_code' => 'HUF', 'currency_symbol' => 'Ft',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Hongrie', 'slug' => 'hongrie', 'description' => 'Pays d\'Europe centrale traversé par le Danube.', 'currency_name' => 'Forint hongrois'],
                    ['lang_code' => 'en', 'name' => 'Hungary', 'slug' => 'hungary', 'description' => 'A Central European country crossed by the Danube.', 'currency_name' => 'Hungarian Forint'],
                ],
            ],
            ['code_2' => 'id', 'code' => 'IDN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+62', 'currency_code' => 'IDR', 'currency_symbol' => 'Rp',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Indonésie', 'slug' => 'indonesie', 'description' => 'Plus grand archipel du monde.', 'currency_name' => 'Roupie indonésienne'],
                    ['lang_code' => 'en', 'name' => 'Indonesia', 'slug' => 'indonesia', 'description' => 'The world\'s largest archipelago.', 'currency_name' => 'Indonesian Rupiah'],
                ],
            ],
            ['code_2' => 'ie', 'code' => 'IRL', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+353', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Irlande', 'slug' => 'irlande', 'description' => 'Île d\'Europe occidentale, l\'île d\'Émeraude.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Ireland', 'slug' => 'ireland', 'description' => 'A Western European island, the Emerald Isle.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'il', 'code' => 'ISR', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+972', 'currency_code' => 'ILS', 'currency_symbol' => '₪',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Israël', 'slug' => 'israel', 'description' => 'Pays du Proche-Orient sur la Méditerranée.', 'currency_name' => 'Shekel israélien'],
                    ['lang_code' => 'en', 'name' => 'Israel', 'slug' => 'israel', 'description' => 'A Middle Eastern country on the Mediterranean.', 'currency_name' => 'Israeli New Shekel'],
                ],
            ],
            ['code_2' => 'im', 'code' => 'IMN', 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+44 1624', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Île de Man', 'slug' => 'ile-de-man', 'description' => 'Dépendance de la Couronne britannique en mer d\'Irlande.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'Isle of Man', 'slug' => 'isle-of-man', 'description' => 'A British Crown Dependency in the Irish Sea.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'in', 'code' => 'IND', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+91', 'currency_code' => 'INR', 'currency_symbol' => '₹',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Inde', 'slug' => 'inde', 'description' => 'Pays le plus peuplé du monde, sous-continent d\'Asie du Sud.', 'currency_name' => 'Roupie indienne'],
                    ['lang_code' => 'en', 'name' => 'India', 'slug' => 'india', 'description' => 'The world\'s most populous country, a South Asian subcontinent.', 'currency_name' => 'Indian Rupee'],
                ],
            ],
            ['code_2' => 'io', 'code' => 'IOT', 'continent_code' => 'AF', 'type' => 'territory', 'phone_code' => '+246', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Territoire britannique de l\'océan Indien', 'slug' => 'territoire-britannique-ocean-indien', 'description' => 'Archipel des Chagos administré par le Royaume-Uni.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'British Indian Ocean Territory', 'slug' => 'british-indian-ocean-territory', 'description' => 'The Chagos Archipelago, administered by the UK.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'iq', 'code' => 'IRQ', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+964', 'currency_code' => 'IQD', 'currency_symbol' => 'ع.د',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Irak', 'slug' => 'irak', 'description' => 'Pays du Moyen-Orient berceau de la Mésopotamie.', 'currency_name' => 'Dinar irakien'],
                    ['lang_code' => 'en', 'name' => 'Iraq', 'slug' => 'iraq', 'description' => 'A Middle Eastern country, cradle of Mesopotamia.', 'currency_name' => 'Iraqi Dinar'],
                ],
            ],
            ['code_2' => 'ir', 'code' => 'IRN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+98', 'currency_code' => 'IRR', 'currency_symbol' => '﷼',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Iran', 'slug' => 'iran', 'description' => 'Pays perse riche d\'une histoire millénaire.', 'currency_name' => 'Rial iranien'],
                    ['lang_code' => 'en', 'name' => 'Iran', 'slug' => 'iran', 'description' => 'A Persian country with a millennia-old history.', 'currency_name' => 'Iranian Rial'],
                ],
            ],
            ['code_2' => 'is', 'code' => 'ISL', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+354', 'currency_code' => 'ISK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Islande', 'slug' => 'islande', 'description' => 'Île nordique de feu et de glace.', 'currency_name' => 'Couronne islandaise'],
                    ['lang_code' => 'en', 'name' => 'Iceland', 'slug' => 'iceland', 'description' => 'A Nordic island of fire and ice.', 'currency_name' => 'Icelandic Króna'],
                ],
            ],
            ['code_2' => 'it', 'code' => 'ITA', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+39', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Italie', 'slug' => 'italie', 'description' => 'Pays d\'Europe du Sud en forme de botte.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Italy', 'slug' => 'italy', 'description' => 'A boot-shaped Southern European country.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'je', 'code' => 'JEY', 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+44 1534', 'currency_code' => 'GBP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Jersey', 'slug' => 'jersey', 'description' => 'Dépendance de la Couronne britannique dans la Manche.', 'currency_name' => 'Livre sterling'],
                    ['lang_code' => 'en', 'name' => 'Jersey', 'slug' => 'jersey', 'description' => 'A British Crown Dependency in the English Channel.', 'currency_name' => 'British Pound Sterling'],
                ],
            ],
            ['code_2' => 'jm', 'code' => 'JAM', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1876', 'currency_code' => 'JMD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Jamaïque', 'slug' => 'jamaique', 'description' => 'Île des Caraïbes berceau du reggae.', 'currency_name' => 'Dollar jamaïcain'],
                    ['lang_code' => 'en', 'name' => 'Jamaica', 'slug' => 'jamaica', 'description' => 'A Caribbean island, the birthplace of reggae.', 'currency_name' => 'Jamaican Dollar'],
                ],
            ],
            ['code_2' => 'jo', 'code' => 'JOR', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+962', 'currency_code' => 'JOD', 'currency_symbol' => 'د.ا',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Jordanie', 'slug' => 'jordanie', 'description' => 'Pays du Moyen-Orient abritant la cité antique de Pétra.', 'currency_name' => 'Dinar jordanien'],
                    ['lang_code' => 'en', 'name' => 'Jordan', 'slug' => 'jordan', 'description' => 'A Middle Eastern country home to the ancient city of Petra.', 'currency_name' => 'Jordanian Dinar'],
                ],
            ],
            ['code_2' => 'jp', 'code' => 'JPN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+81', 'currency_code' => 'JPY', 'currency_symbol' => '¥',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Japon', 'slug' => 'japon', 'description' => 'Archipel d\'Asie de l\'Est, pays du Soleil-Levant.', 'currency_name' => 'Yen japonais'],
                    ['lang_code' => 'en', 'name' => 'Japan', 'slug' => 'japan', 'description' => 'An East Asian archipelago, the Land of the Rising Sun.', 'currency_name' => 'Japanese Yen'],
                ],
            ],
            ['code_2' => 'ke', 'code' => 'KEN', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+254', 'currency_code' => 'KES', 'currency_symbol' => 'Sh',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Kenya', 'slug' => 'kenya', 'description' => 'Pays d\'Afrique de l\'Est réputé pour ses safaris.', 'currency_name' => 'Shilling kényan'],
                    ['lang_code' => 'en', 'name' => 'Kenya', 'slug' => 'kenya', 'description' => 'An East African country renowned for its safaris.', 'currency_name' => 'Kenyan Shilling'],
                ],
            ],
            ['code_2' => 'kg', 'code' => 'KGZ', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+996', 'currency_code' => 'KGS', 'currency_symbol' => 'с',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Kirghizistan', 'slug' => 'kirghizistan', 'description' => 'Pays montagneux d\'Asie centrale.', 'currency_name' => 'Som kirghize'],
                    ['lang_code' => 'en', 'name' => 'Kyrgyzstan', 'slug' => 'kyrgyzstan', 'description' => 'A mountainous Central Asian country.', 'currency_name' => 'Kyrgyzstani Som'],
                ],
            ],
            ['code_2' => 'kh', 'code' => 'KHM', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+855', 'currency_code' => 'KHR', 'currency_symbol' => '៛',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Cambodge', 'slug' => 'cambodge', 'description' => 'Pays d\'Asie du Sud-Est abritant Angkor Vat.', 'currency_name' => 'Riel cambodgien'],
                    ['lang_code' => 'en', 'name' => 'Cambodia', 'slug' => 'cambodia', 'description' => 'A Southeast Asian country home to Angkor Wat.', 'currency_name' => 'Cambodian Riel'],
                ],
            ],
            ['code_2' => 'ki', 'code' => 'KIR', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+686', 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Kiribati', 'slug' => 'kiribati', 'description' => 'État insulaire du Pacifique réparti sur trois archipels.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Kiribati', 'slug' => 'kiribati', 'description' => 'A Pacific island nation spread across three archipelagos.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'km', 'code' => 'COM', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+269', 'currency_code' => 'KMF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Comores', 'slug' => 'comores', 'description' => 'Archipel volcanique de l\'océan Indien.', 'currency_name' => 'Franc comorien'],
                    ['lang_code' => 'en', 'name' => 'Comoros', 'slug' => 'comoros', 'description' => 'A volcanic archipelago in the Indian Ocean.', 'currency_name' => 'Comorian Franc'],
                ],
            ],
            ['code_2' => 'kn', 'code' => 'KNA', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1869', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Saint-Christophe-et-Niévès', 'slug' => 'saint-christophe-et-nieves', 'description' => 'Plus petit État souverain des Amériques.', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Saint Kitts and Nevis', 'slug' => 'saint-kitts-and-nevis', 'description' => 'The smallest sovereign state in the Americas.', 'currency_name' => 'East Caribbean Dollar'],
                ],
            ],
            ['code_2' => 'kp', 'code' => 'PRK', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+850', 'currency_code' => 'KPW', 'currency_symbol' => '₩',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Corée du Nord', 'slug' => 'coree-du-nord', 'description' => 'Pays d\'Asie de l\'Est au régime isolé.', 'currency_name' => 'Won nord-coréen'],
                    ['lang_code' => 'en', 'name' => 'North Korea', 'slug' => 'north-korea', 'description' => 'An isolated East Asian country.', 'currency_name' => 'North Korean Won'],
                ],
            ],
            ['code_2' => 'kr', 'code' => 'KOR', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+82', 'currency_code' => 'KRW', 'currency_symbol' => '₩',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Corée du Sud', 'slug' => 'coree-du-sud', 'description' => 'Pays d\'Asie de l\'Est, puissance technologique.', 'currency_name' => 'Won sud-coréen'],
                    ['lang_code' => 'en', 'name' => 'South Korea', 'slug' => 'south-korea', 'description' => 'An East Asian country and technology powerhouse.', 'currency_name' => 'South Korean Won'],
                ],
            ],
            ['code_2' => 'kw', 'code' => 'KWT', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+965', 'currency_code' => 'KWD', 'currency_symbol' => 'د.ك',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Koweït', 'slug' => 'koweit', 'description' => 'Petit émirat pétrolier du golfe Persique.', 'currency_name' => 'Dinar koweïtien'],
                    ['lang_code' => 'en', 'name' => 'Kuwait', 'slug' => 'kuwait', 'description' => 'A small oil-rich emirate on the Persian Gulf.', 'currency_name' => 'Kuwaiti Dinar'],
                ],
            ],
            ['code_2' => 'ky', 'code' => 'CYM', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1345', 'currency_code' => 'KYD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Caïmans', 'slug' => 'iles-caimans', 'description' => 'Territoire britannique d\'outre-mer, paradis fiscal des Caraïbes.', 'currency_name' => 'Dollar des îles Caïmans'],
                    ['lang_code' => 'en', 'name' => 'Cayman Islands', 'slug' => 'cayman-islands', 'description' => 'A British Overseas Territory and Caribbean tax haven.', 'currency_name' => 'Cayman Islands Dollar'],
                ],
            ],
            ['code_2' => 'kz', 'code' => 'KAZ', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+7', 'currency_code' => 'KZT', 'currency_symbol' => '₸',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Kazakhstan', 'slug' => 'kazakhstan', 'description' => 'Plus vaste pays enclavé du monde.', 'currency_name' => 'Tenge kazakh'],
                    ['lang_code' => 'en', 'name' => 'Kazakhstan', 'slug' => 'kazakhstan', 'description' => 'The world\'s largest landlocked country.', 'currency_name' => 'Kazakhstani Tenge'],
                ],
            ],
            ['code_2' => 'la', 'code' => 'LAO', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+856', 'currency_code' => 'LAK', 'currency_symbol' => '₭',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Laos', 'slug' => 'laos', 'description' => 'Pays enclavé d\'Asie du Sud-Est traversé par le Mékong.', 'currency_name' => 'Kip laotien'],
                    ['lang_code' => 'en', 'name' => 'Laos', 'slug' => 'laos', 'description' => 'A landlocked Southeast Asian country crossed by the Mekong.', 'currency_name' => 'Lao Kip'],
                ],
            ],
            ['code_2' => 'lb', 'code' => 'LBN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+961', 'currency_code' => 'LBP', 'currency_symbol' => 'ل.ل',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Liban', 'slug' => 'liban', 'description' => 'Pays du Levant sur la Méditerranée orientale.', 'currency_name' => 'Livre libanaise'],
                    ['lang_code' => 'en', 'name' => 'Lebanon', 'slug' => 'lebanon', 'description' => 'A Levantine country on the eastern Mediterranean.', 'currency_name' => 'Lebanese Pound'],
                ],
            ],
            ['code_2' => 'lc', 'code' => 'LCA', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1758', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Sainte-Lucie', 'slug' => 'sainte-lucie', 'description' => 'Île volcanique des Caraïbes orientales.', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Saint Lucia', 'slug' => 'saint-lucia', 'description' => 'A volcanic island in the eastern Caribbean.', 'currency_name' => 'East Caribbean Dollar'],
                ]],

            // --- Lot 4 : LI -> NU ---
            ['code_2' => 'li', 'code' => 'LIE', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+423', 'currency_code' => 'CHF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Liechtenstein', 'slug' => 'liechtenstein', 'description' => 'Micro-État alpin entre la Suisse et l\'Autriche.', 'currency_name' => 'Franc suisse'],
                    ['lang_code' => 'en', 'name' => 'Liechtenstein', 'slug' => 'liechtenstein', 'description' => 'A tiny alpine state between Switzerland and Austria.', 'currency_name' => 'Swiss Franc'],
                ],
            ],
            ['code_2' => 'lk', 'code' => 'LKA', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+94', 'currency_code' => 'LKR', 'currency_symbol' => 'Rs',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Sri Lanka', 'slug' => 'sri-lanka', 'description' => 'Île d\'Asie du Sud réputée pour son thé.', 'currency_name' => 'Roupie srilankaise'],
                    ['lang_code' => 'en', 'name' => 'Sri Lanka', 'slug' => 'sri-lanka', 'description' => 'A South Asian island known for its tea.', 'currency_name' => 'Sri Lankan Rupee'],
                ],
            ],
            ['code_2' => 'lr', 'code' => 'LBR', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+231', 'currency_code' => 'LRD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Liberia', 'slug' => 'liberia', 'description' => 'Pays d\'Afrique de l\'Ouest fondé par d\'anciens esclaves affranchis.', 'currency_name' => 'Dollar libérien'],
                    ['lang_code' => 'en', 'name' => 'Liberia', 'slug' => 'liberia', 'description' => 'A West African country founded by freed former slaves.', 'currency_name' => 'Liberian Dollar'],
                ],
            ],
            ['code_2' => 'ls', 'code' => 'LSO', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+266', 'currency_code' => 'LSL', 'currency_symbol' => 'L',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Lesotho', 'slug' => 'lesotho', 'description' => 'Royaume enclavé entièrement entouré par l\'Afrique du Sud.', 'currency_name' => 'Loti lesothan'],
                    ['lang_code' => 'en', 'name' => 'Lesotho', 'slug' => 'lesotho', 'description' => 'A landlocked kingdom entirely surrounded by South Africa.', 'currency_name' => 'Lesotho Loti'],
                ],
            ],
            ['code_2' => 'lt', 'code' => 'LTU', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+370', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Lituanie', 'slug' => 'lituanie', 'description' => 'Le plus méridional des trois pays baltes.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Lithuania', 'slug' => 'lithuania', 'description' => 'The southernmost of the three Baltic states.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'lu', 'code' => 'LUX', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+352', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Luxembourg', 'slug' => 'luxembourg', 'description' => 'Petit grand-duché au cœur de l\'Europe occidentale.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Luxembourg', 'slug' => 'luxembourg', 'description' => 'A small grand duchy at the heart of Western Europe.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'lv', 'code' => 'LVA', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+371', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Lettonie', 'slug' => 'lettonie', 'description' => 'Pays balte bordant la mer Baltique.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Latvia', 'slug' => 'latvia', 'description' => 'A Baltic country on the Baltic Sea.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'ly', 'code' => 'LBY', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+218', 'currency_code' => 'LYD', 'currency_symbol' => 'ل.د',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Libye', 'slug' => 'libye', 'description' => 'Pays d\'Afrique du Nord bordant la Méditerranée.', 'currency_name' => 'Dinar libyen'],
                    ['lang_code' => 'en', 'name' => 'Libya', 'slug' => 'libya', 'description' => 'A North African country on the Mediterranean.', 'currency_name' => 'Libyan Dinar'],
                ],
            ],
            ['code_2' => 'ma', 'code' => 'MAR', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+212', 'currency_code' => 'MAD', 'currency_symbol' => 'د.م.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Maroc', 'slug' => 'maroc', 'description' => 'Pays d\'Afrique du Nord entre Atlantique et Méditerranée.', 'currency_name' => 'Dirham marocain'],
                    ['lang_code' => 'en', 'name' => 'Morocco', 'slug' => 'morocco', 'description' => 'A North African country between the Atlantic and the Mediterranean.', 'currency_name' => 'Moroccan Dirham'],
                ],
            ],
            ['code_2' => 'mc', 'code' => 'MCO', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+377', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Monaco', 'slug' => 'monaco', 'description' => 'Deuxième plus petit État du monde, sur la Côte d\'Azur.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Monaco', 'slug' => 'monaco', 'description' => 'The world\'s second smallest state, on the French Riviera.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'md', 'code' => 'MDA', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+373', 'currency_code' => 'MDL', 'currency_symbol' => 'L',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Moldavie', 'slug' => 'moldavie', 'description' => 'Pays enclavé d\'Europe de l\'Est.', 'currency_name' => 'Leu moldave'],
                    ['lang_code' => 'en', 'name' => 'Moldova', 'slug' => 'moldova', 'description' => 'A landlocked Eastern European country.', 'currency_name' => 'Moldovan Leu'],
                ],
            ],
            ['code_2' => 'me', 'code' => 'MNE', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+382', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Monténégro', 'slug' => 'montenegro', 'description' => 'Pays des Balkans à la côte adriatique montagneuse.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Montenegro', 'slug' => 'montenegro', 'description' => 'A Balkan country with a mountainous Adriatic coast.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'mf', 'code' => 'MAF', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+590', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Saint-Martin', 'slug' => 'saint-martin', 'description' => 'Collectivité française partageant l\'île avec Sint Maarten.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Saint Martin', 'slug' => 'saint-martin', 'description' => 'A French collectivity sharing the island with Sint Maarten.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'mg', 'code' => 'MDG', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+261', 'currency_code' => 'MGA', 'currency_symbol' => 'Ar',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Madagascar', 'slug' => 'madagascar', 'description' => 'Grande île de l\'océan Indien à la biodiversité unique.', 'currency_name' => 'Ariary malgache'],
                    ['lang_code' => 'en', 'name' => 'Madagascar', 'slug' => 'madagascar', 'description' => 'A large Indian Ocean island with unique biodiversity.', 'currency_name' => 'Malagasy Ariary'],
                ],
            ],
            ['code_2' => 'mh', 'code' => 'MHL', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+692', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Marshall', 'slug' => 'iles-marshall', 'description' => 'État insulaire du Pacifique composé d\'atolls coralliens.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Marshall Islands', 'slug' => 'marshall-islands', 'description' => 'A Pacific island nation made up of coral atolls.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'mk', 'code' => 'MKD', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+389', 'currency_code' => 'MKD', 'currency_symbol' => 'den',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Macédoine du Nord', 'slug' => 'macedoine-du-nord', 'description' => 'Pays enclavé des Balkans.', 'currency_name' => 'Denar macédonien'],
                    ['lang_code' => 'en', 'name' => 'North Macedonia', 'slug' => 'north-macedonia', 'description' => 'A landlocked Balkan country.', 'currency_name' => 'Macedonian Denar'],
                ],
            ],
            ['code_2' => 'ml', 'code' => 'MLI', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+223', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mali', 'slug' => 'mali', 'description' => 'Pays sahélien d\'Afrique de l\'Ouest, ancien empire prospère.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Mali', 'slug' => 'mali', 'description' => 'A Sahelian West African country, once a prosperous empire.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'mm', 'code' => 'MMR', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+95', 'currency_code' => 'MMK', 'currency_symbol' => 'Ks',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Myanmar', 'slug' => 'myanmar', 'description' => 'Pays d\'Asie du Sud-Est, anciennement appelé Birmanie.', 'currency_name' => 'Kyat birman'],
                    ['lang_code' => 'en', 'name' => 'Myanmar', 'slug' => 'myanmar', 'description' => 'A Southeast Asian country, formerly known as Burma.', 'currency_name' => 'Myanmar Kyat'],
                ],
            ],
            ['code_2' => 'mn', 'code' => 'MNG', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+976', 'currency_code' => 'MNT', 'currency_symbol' => '₮',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mongolie', 'slug' => 'mongolie', 'description' => 'Vaste pays enclavé aux steppes légendaires.', 'currency_name' => 'Tugrik mongol'],
                    ['lang_code' => 'en', 'name' => 'Mongolia', 'slug' => 'mongolia', 'description' => 'A vast landlocked country with legendary steppes.', 'currency_name' => 'Mongolian Tugrik'],
                ],
            ],
            ['code_2' => 'mo', 'code' => 'MAC', 'continent_code' => 'AS', 'type' => 'special_administrative_region', 'phone_code' => '+853', 'currency_code' => 'MOP', 'currency_symbol' => 'MOP$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Macao', 'slug' => 'macao', 'description' => 'Région administrative spéciale de la Chine, ancienne colonie portugaise.', 'currency_name' => 'Pataca macanaise'],
                    ['lang_code' => 'en', 'name' => 'Macao', 'slug' => 'macao', 'description' => 'A special administrative region of China, formerly a Portuguese colony.', 'currency_name' => 'Macanese Pataca'],
                ],
            ],
            ['code_2' => 'mp', 'code' => 'MNP', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+1670', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Mariannes du Nord', 'slug' => 'iles-mariannes-du-nord', 'description' => 'Commonwealth insulaire associé aux États-Unis.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Northern Mariana Islands', 'slug' => 'northern-mariana-islands', 'description' => 'An island commonwealth in political union with the United States.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'mq', 'code' => 'MTQ', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+596', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Martinique', 'slug' => 'martinique', 'description' => 'Île française des Caraïbes.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Martinique', 'slug' => 'martinique', 'description' => 'A French island in the Caribbean.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'mr', 'code' => 'MRT', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+222', 'currency_code' => 'MRU', 'currency_symbol' => 'UM',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mauritanie', 'slug' => 'mauritanie', 'description' => 'Vaste pays saharien d\'Afrique de l\'Ouest.', 'currency_name' => 'Ouguiya mauritanien'],
                    ['lang_code' => 'en', 'name' => 'Mauritania', 'slug' => 'mauritania', 'description' => 'A vast Saharan country in West Africa.', 'currency_name' => 'Mauritanian Ouguiya'],
                ],
            ],
            ['code_2' => 'ms', 'code' => 'MSR', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1664', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Montserrat', 'slug' => 'montserrat', 'description' => 'Territoire britannique d\'outre-mer des Caraïbes, marqué par le volcanisme.', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Montserrat', 'slug' => 'montserrat', 'description' => 'A British Overseas Territory in the Caribbean, shaped by volcanic activity.', 'currency_name' => 'East Caribbean Dollar'],
                ],
            ],
            ['code_2' => 'mt', 'code' => 'MLT', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+356', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Malte', 'slug' => 'malte', 'description' => 'Petit archipel méditerranéen membre de l\'Union européenne.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Malta', 'slug' => 'malta', 'description' => 'A small Mediterranean archipelago and EU member state.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'mu', 'code' => 'MUS', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+230', 'currency_code' => 'MUR', 'currency_symbol' => 'Rs',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Maurice', 'slug' => 'maurice', 'description' => 'Île paradisiaque de l\'océan Indien.', 'currency_name' => 'Roupie mauricienne'],
                    ['lang_code' => 'en', 'name' => 'Mauritius', 'slug' => 'mauritius', 'description' => 'A paradisiacal island in the Indian Ocean.', 'currency_name' => 'Mauritian Rupee'],
                ],
            ],
            ['code_2' => 'mv', 'code' => 'MDV', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+960', 'currency_code' => 'MVR', 'currency_symbol' => 'ރ.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Maldives', 'slug' => 'maldives', 'description' => 'Archipel d\'atolls coralliens de l\'océan Indien.', 'currency_name' => 'Rufiyaa maldivien'],
                    ['lang_code' => 'en', 'name' => 'Maldives', 'slug' => 'maldives', 'description' => 'An archipelago of coral atolls in the Indian Ocean.', 'currency_name' => 'Maldivian Rufiyaa'],
                ],
            ],
            ['code_2' => 'mw', 'code' => 'MWI', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+265', 'currency_code' => 'MWK', 'currency_symbol' => 'MK',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Malawi', 'slug' => 'malawi', 'description' => 'Pays d\'Afrique australe bordant le lac Malawi.', 'currency_name' => 'Kwacha malawite'],
                    ['lang_code' => 'en', 'name' => 'Malawi', 'slug' => 'malawi', 'description' => 'A Southern African country on Lake Malawi.', 'currency_name' => 'Malawian Kwacha'],
                ],
            ],
            ['code_2' => 'mx', 'code' => 'MEX', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+52', 'currency_code' => 'MXN', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mexique', 'slug' => 'mexique', 'description' => 'Pays d\'Amérique du Nord, berceau des civilisations aztèque et maya.', 'currency_name' => 'Peso mexicain'],
                    ['lang_code' => 'en', 'name' => 'Mexico', 'slug' => 'mexico', 'description' => 'A North American country, cradle of the Aztec and Maya civilizations.', 'currency_name' => 'Mexican Peso'],
                ],
            ],
            ['code_2' => 'my', 'code' => 'MYS', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+60', 'currency_code' => 'MYR', 'currency_symbol' => 'RM',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Malaisie', 'slug' => 'malaisie', 'description' => 'Pays d\'Asie du Sud-Est réparti entre péninsule et Bornéo.', 'currency_name' => 'Ringgit malaisien'],
                    ['lang_code' => 'en', 'name' => 'Malaysia', 'slug' => 'malaysia', 'description' => 'A Southeast Asian country split between the peninsula and Borneo.', 'currency_name' => 'Malaysian Ringgit'],
                ],
            ],
            ['code_2' => 'mz', 'code' => 'MOZ', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+258', 'currency_code' => 'MZN', 'currency_symbol' => 'MT',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mozambique', 'slug' => 'mozambique', 'description' => 'Pays d\'Afrique australe à la longue façade sur l\'océan Indien.', 'currency_name' => 'Metical mozambicain'],
                    ['lang_code' => 'en', 'name' => 'Mozambique', 'slug' => 'mozambique', 'description' => 'A Southern African country with a long Indian Ocean coastline.', 'currency_name' => 'Mozambican Metical'],
                ],
            ],
            ['code_2' => 'na', 'code' => 'NAM', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+264', 'currency_code' => 'NAD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Namibie', 'slug' => 'namibie', 'description' => 'Pays d\'Afrique australe abritant le désert du Namib.', 'currency_name' => 'Dollar namibien'],
                    ['lang_code' => 'en', 'name' => 'Namibia', 'slug' => 'namibia', 'description' => 'A Southern African country home to the Namib Desert.', 'currency_name' => 'Namibian Dollar'],
                ],
            ],
            ['code_2' => 'nc', 'code' => 'NCL', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+687', 'currency_code' => 'XPF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nouvelle-Calédonie', 'slug' => 'nouvelle-caledonie', 'description' => 'Collectivité française du Pacifique Sud.', 'currency_name' => 'Franc CFP'],
                    ['lang_code' => 'en', 'name' => 'New Caledonia', 'slug' => 'new-caledonia', 'description' => 'A French collectivity in the South Pacific.', 'currency_name' => 'CFP Franc'],
                ],
            ],
            ['code_2' => 'ne', 'code' => 'NER', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+227', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Niger', 'slug' => 'niger', 'description' => 'Vaste pays sahélien enclavé d\'Afrique de l\'Ouest.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Niger', 'slug' => 'niger', 'description' => 'A vast landlocked Sahelian country in West Africa.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'nf', 'code' => 'NFK', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+672', 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Île Norfolk', 'slug' => 'ile-norfolk', 'description' => 'Territoire australien isolé du Pacifique Sud.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Norfolk Island', 'slug' => 'norfolk-island', 'description' => 'A remote Australian territory in the South Pacific.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'ng', 'code' => 'NGA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+234', 'currency_code' => 'NGN', 'currency_symbol' => '₦',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nigeria', 'slug' => 'nigeria', 'description' => 'Pays le plus peuplé d\'Afrique.', 'currency_name' => 'Naira nigérian'],
                    ['lang_code' => 'en', 'name' => 'Nigeria', 'slug' => 'nigeria', 'description' => 'Africa\'s most populous country.', 'currency_name' => 'Nigerian Naira'],
                ],
            ],
            ['code_2' => 'ni', 'code' => 'NIC', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+505', 'currency_code' => 'NIO', 'currency_symbol' => 'C$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nicaragua', 'slug' => 'nicaragua', 'description' => 'Plus grand pays d\'Amérique centrale, riche en lacs et volcans.', 'currency_name' => 'Córdoba nicaraguayen'],
                    ['lang_code' => 'en', 'name' => 'Nicaragua', 'slug' => 'nicaragua', 'description' => 'The largest Central American country, rich in lakes and volcanoes.', 'currency_name' => 'Nicaraguan Córdoba'],
                ],
            ],
            ['code_2' => 'nl', 'code' => 'NLD', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+31', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Pays-Bas', 'slug' => 'pays-bas', 'description' => 'Pays d\'Europe occidentale réputé pour ses polders et ses tulipes.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Netherlands', 'slug' => 'netherlands', 'description' => 'A Western European country known for its polders and tulips.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'no', 'code' => 'NOR', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+47', 'currency_code' => 'NOK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Norvège', 'slug' => 'norvege', 'description' => 'Royaume scandinave célèbre pour ses fjords.', 'currency_name' => 'Couronne norvégienne'],
                    ['lang_code' => 'en', 'name' => 'Norway', 'slug' => 'norway', 'description' => 'A Scandinavian kingdom famous for its fjords.', 'currency_name' => 'Norwegian Krone'],
                ],
            ],
            ['code_2' => 'np', 'code' => 'NPL', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+977', 'currency_code' => 'NPR', 'currency_symbol' => 'Rs',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Népal', 'slug' => 'nepal', 'description' => 'Pays himalayen abritant l\'Everest.', 'currency_name' => 'Roupie népalaise'],
                    ['lang_code' => 'en', 'name' => 'Nepal', 'slug' => 'nepal', 'description' => 'A Himalayan country home to Mount Everest.', 'currency_name' => 'Nepalese Rupee'],
                ],
            ],
            ['code_2' => 'nr', 'code' => 'NRU', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+674', 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nauru', 'slug' => 'nauru', 'description' => 'Plus petite république insulaire du monde.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Nauru', 'slug' => 'nauru', 'description' => 'The world\'s smallest island republic.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'nu', 'code' => 'NIU', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+683', 'currency_code' => 'NZD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Niue', 'slug' => 'niue', 'description' => 'État insulaire librement associé à la Nouvelle-Zélande.', 'currency_name' => 'Dollar néo-zélandais'],
                    ['lang_code' => 'en', 'name' => 'Niue', 'slug' => 'niue', 'description' => 'An island state in free association with New Zealand.', 'currency_name' => 'New Zealand Dollar'],
                ]],

            // --- Lot 5 : NZ -> SY ---
            ['code_2' => 'nz', 'code' => 'NZL', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+64', 'currency_code' => 'NZD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nouvelle-Zélande', 'slug' => 'nouvelle-zelande', 'description' => 'Pays insulaire d\'Océanie réputé pour ses paysages spectaculaires.', 'currency_name' => 'Dollar néo-zélandais'],
                    ['lang_code' => 'en', 'name' => 'New Zealand', 'slug' => 'new-zealand', 'description' => 'An island country in Oceania known for its dramatic landscapes.', 'currency_name' => 'New Zealand Dollar'],
                ],
            ],
            ['code_2' => 'om', 'code' => 'OMN', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+968', 'currency_code' => 'OMR', 'currency_symbol' => 'ر.ع.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Oman', 'slug' => 'oman', 'description' => 'Sultanat du Moyen-Orient bordant la mer d\'Arabie.', 'currency_name' => 'Rial omanais'],
                    ['lang_code' => 'en', 'name' => 'Oman', 'slug' => 'oman', 'description' => 'A Middle Eastern sultanate on the Arabian Sea.', 'currency_name' => 'Omani Rial'],
                ],
            ],
            ['code_2' => 'pa', 'code' => 'PAN', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+507', 'currency_code' => 'PAB', 'currency_symbol' => 'B/.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Panama', 'slug' => 'panama', 'description' => 'Pays d\'Amérique centrale traversé par son célèbre canal.', 'currency_name' => 'Balboa panaméen'],
                    ['lang_code' => 'en', 'name' => 'Panama', 'slug' => 'panama', 'description' => 'A Central American country crossed by its famous canal.', 'currency_name' => 'Panamanian Balboa'],
                ],
            ],
            ['code_2' => 'pe', 'code' => 'PER', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+51', 'currency_code' => 'PEN', 'currency_symbol' => 'S/',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Pérou', 'slug' => 'perou', 'description' => 'Pays andin abritant le site du Machu Picchu.', 'currency_name' => 'Sol péruvien'],
                    ['lang_code' => 'en', 'name' => 'Peru', 'slug' => 'peru', 'description' => 'An Andean country home to the Machu Picchu site.', 'currency_name' => 'Peruvian Sol'],
                ],
            ],
            ['code_2' => 'pf', 'code' => 'PYF', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+689', 'currency_code' => 'XPF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Polynésie française', 'slug' => 'polynesie-francaise', 'description' => 'Collectivité française du Pacifique regroupant Tahiti et ses archipels.', 'currency_name' => 'Franc CFP'],
                    ['lang_code' => 'en', 'name' => 'French Polynesia', 'slug' => 'french-polynesia', 'description' => 'A French collectivity in the Pacific, including Tahiti and its archipelagos.', 'currency_name' => 'CFP Franc'],
                ],
            ],
            ['code_2' => 'pg', 'code' => 'PNG', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+675', 'currency_code' => 'PGK', 'currency_symbol' => 'K',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Papouasie-Nouvelle-Guinée', 'slug' => 'papouasie-nouvelle-guinee', 'description' => 'Pays d\'Océanie à la biodiversité et aux cultures exceptionnelles.', 'currency_name' => 'Kina papou'],
                    ['lang_code' => 'en', 'name' => 'Papua New Guinea', 'slug' => 'papua-new-guinea', 'description' => 'An Oceanian country with exceptional biodiversity and cultures.', 'currency_name' => 'Papua New Guinean Kina'],
                ],
            ],
            ['code_2' => 'ph', 'code' => 'PHL', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+63', 'currency_code' => 'PHP', 'currency_symbol' => '₱',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Philippines', 'slug' => 'philippines', 'description' => 'Archipel d\'Asie du Sud-Est composé de plus de 7 000 îles.', 'currency_name' => 'Peso philippin'],
                    ['lang_code' => 'en', 'name' => 'Philippines', 'slug' => 'philippines', 'description' => 'A Southeast Asian archipelago of more than 7,000 islands.', 'currency_name' => 'Philippine Peso'],
                ],
            ],
            ['code_2' => 'pk', 'code' => 'PAK', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+92', 'currency_code' => 'PKR', 'currency_symbol' => 'Rs',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Pakistan', 'slug' => 'pakistan', 'description' => 'Pays d\'Asie du Sud traversé par l\'Indus.', 'currency_name' => 'Roupie pakistanaise'],
                    ['lang_code' => 'en', 'name' => 'Pakistan', 'slug' => 'pakistan', 'description' => 'A South Asian country crossed by the Indus River.', 'currency_name' => 'Pakistani Rupee'],
                ],
            ],
            ['code_2' => 'pl', 'code' => 'POL', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+48', 'currency_code' => 'PLN', 'currency_symbol' => 'zł',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Pologne', 'slug' => 'pologne', 'description' => 'Pays d\'Europe centrale à la riche histoire mouvementée.', 'currency_name' => 'Zloty polonais'],
                    ['lang_code' => 'en', 'name' => 'Poland', 'slug' => 'poland', 'description' => 'A Central European country with a rich, turbulent history.', 'currency_name' => 'Polish Zloty'],
                ],
            ],
            ['code_2' => 'pm', 'code' => 'SPM', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+508', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Saint-Pierre-et-Miquelon', 'slug' => 'saint-pierre-et-miquelon', 'description' => 'Collectivité française au large du Canada.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Saint Pierre and Miquelon', 'slug' => 'saint-pierre-and-miquelon', 'description' => 'A French collectivity off the coast of Canada.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'pn', 'code' => 'PCN', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+64', 'currency_code' => 'NZD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Pitcairn', 'slug' => 'iles-pitcairn', 'description' => 'Territoire britannique isolé du Pacifique Sud, le moins peuplé du monde.', 'currency_name' => 'Dollar néo-zélandais'],
                    ['lang_code' => 'en', 'name' => 'Pitcairn Islands', 'slug' => 'pitcairn-islands', 'description' => 'A remote British territory in the South Pacific, the least populous in the world.', 'currency_name' => 'New Zealand Dollar'],
                ],
            ],
            ['code_2' => 'pr', 'code' => 'PRI', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1787', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Porto Rico', 'slug' => 'porto-rico', 'description' => 'Territoire non incorporé des États-Unis dans les Caraïbes.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Puerto Rico', 'slug' => 'puerto-rico', 'description' => 'An unincorporated U.S. territory in the Caribbean.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'ps', 'code' => 'PSE', 'continent_code' => 'AS', 'type' => 'disputed', 'phone_code' => '+970', 'currency_code' => 'ILS', 'currency_symbol' => '₪',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Palestine', 'slug' => 'palestine', 'description' => 'Territoire du Moyen-Orient au statut contesté, comprenant la Cisjordanie et Gaza.', 'currency_name' => 'Shekel israélien'],
                    ['lang_code' => 'en', 'name' => 'Palestine', 'slug' => 'palestine', 'description' => 'A Middle Eastern territory with disputed status, comprising the West Bank and Gaza.', 'currency_name' => 'Israeli New Shekel'],
                ],
            ],
            ['code_2' => 'pt', 'code' => 'PRT', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+351', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Portugal', 'slug' => 'portugal', 'description' => 'Pays d\'Europe du Sud sur la façade atlantique de la péninsule ibérique.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Portugal', 'slug' => 'portugal', 'description' => 'A Southern European country on the Atlantic coast of the Iberian Peninsula.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'pw', 'code' => 'PLW', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+680', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Palaos', 'slug' => 'palaos', 'description' => 'État insulaire du Pacifique réputé pour ses fonds marins.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Palau', 'slug' => 'palau', 'description' => 'A Pacific island nation renowned for its marine life.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'py', 'code' => 'PRY', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+595', 'currency_code' => 'PYG', 'currency_symbol' => '₲',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Paraguay', 'slug' => 'paraguay', 'description' => 'Pays enclavé d\'Amérique du Sud.', 'currency_name' => 'Guaraní paraguayen'],
                    ['lang_code' => 'en', 'name' => 'Paraguay', 'slug' => 'paraguay', 'description' => 'A landlocked South American country.', 'currency_name' => 'Paraguayan Guarani'],
                ],
            ],
            ['code_2' => 'qa', 'code' => 'QAT', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+974', 'currency_code' => 'QAR', 'currency_symbol' => 'ر.ق',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Qatar', 'slug' => 'qatar', 'description' => 'Émirat péninsulaire du golfe Persique.', 'currency_name' => 'Rial qatarien'],
                    ['lang_code' => 'en', 'name' => 'Qatar', 'slug' => 'qatar', 'description' => 'A peninsular emirate on the Persian Gulf.', 'currency_name' => 'Qatari Riyal'],
                ],
            ],
            ['code_2' => 're', 'code' => 'REU', 'continent_code' => 'AF', 'type' => 'territory', 'phone_code' => '+262', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'La Réunion', 'slug' => 'la-reunion', 'description' => 'Île française volcanique de l\'océan Indien.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Réunion', 'slug' => 'reunion', 'description' => 'A French volcanic island in the Indian Ocean.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'ro', 'code' => 'ROU', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+40', 'currency_code' => 'RON', 'currency_symbol' => 'lei',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Roumanie', 'slug' => 'roumanie', 'description' => 'Pays d\'Europe de l\'Est bordé par les Carpates.', 'currency_name' => 'Leu roumain'],
                    ['lang_code' => 'en', 'name' => 'Romania', 'slug' => 'romania', 'description' => 'An Eastern European country bordered by the Carpathians.', 'currency_name' => 'Romanian Leu'],
                ],
            ],
            ['code_2' => 'rs', 'code' => 'SRB', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+381', 'currency_code' => 'RSD', 'currency_symbol' => 'дин.',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Serbie', 'slug' => 'serbie', 'description' => 'Pays enclavé des Balkans.', 'currency_name' => 'Dinar serbe'],
                    ['lang_code' => 'en', 'name' => 'Serbia', 'slug' => 'serbia', 'description' => 'A landlocked Balkan country.', 'currency_name' => 'Serbian Dinar'],
                ],
            ],
            ['code_2' => 'ru', 'code' => 'RUS', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+7', 'currency_code' => 'RUB', 'currency_symbol' => '₽',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Russie', 'slug' => 'russie', 'description' => 'Plus vaste pays du monde, à cheval sur l\'Europe et l\'Asie.', 'currency_name' => 'Rouble russe'],
                    ['lang_code' => 'en', 'name' => 'Russia', 'slug' => 'russia', 'description' => 'The world\'s largest country, spanning Europe and Asia.', 'currency_name' => 'Russian Ruble'],
                ],
            ],
            ['code_2' => 'rw', 'code' => 'RWA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+250', 'currency_code' => 'RWF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Rwanda', 'slug' => 'rwanda', 'description' => 'Pays des Grands Lacs africains, surnommé "pays des mille collines".', 'currency_name' => 'Franc rwandais'],
                    ['lang_code' => 'en', 'name' => 'Rwanda', 'slug' => 'rwanda', 'description' => 'A Great Lakes African country, nicknamed "land of a thousand hills".', 'currency_name' => 'Rwandan Franc'],
                ],
            ],
            ['code_2' => 'sa', 'code' => 'SAU', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+966', 'currency_code' => 'SAR', 'currency_symbol' => '﷼',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Arabie saoudite', 'slug' => 'arabie-saoudite', 'description' => 'Royaume occupant l\'essentiel de la péninsule arabique.', 'currency_name' => 'Riyal saoudien'],
                    ['lang_code' => 'en', 'name' => 'Saudi Arabia', 'slug' => 'saudi-arabia', 'description' => 'A kingdom occupying most of the Arabian Peninsula.', 'currency_name' => 'Saudi Riyal'],
                ],
            ],
            ['code_2' => 'sb', 'code' => 'SLB', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+677', 'currency_code' => 'SBD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Salomon', 'slug' => 'iles-salomon', 'description' => 'Archipel du Pacifique Sud.', 'currency_name' => 'Dollar des îles Salomon'],
                    ['lang_code' => 'en', 'name' => 'Solomon Islands', 'slug' => 'solomon-islands', 'description' => 'A South Pacific archipelago.', 'currency_name' => 'Solomon Islands Dollar'],
                ],
            ],
            ['code_2' => 'sc', 'code' => 'SYC', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+248', 'currency_code' => 'SCR', 'currency_symbol' => 'Rs',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Seychelles', 'slug' => 'seychelles', 'description' => 'Archipel paradisiaque de l\'océan Indien.', 'currency_name' => 'Roupie seychelloise'],
                    ['lang_code' => 'en', 'name' => 'Seychelles', 'slug' => 'seychelles', 'description' => 'A paradisiacal archipelago in the Indian Ocean.', 'currency_name' => 'Seychellois Rupee'],
                ],
            ],
            ['code_2' => 'sd', 'code' => 'SDN', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+249', 'currency_code' => 'SDG', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Soudan', 'slug' => 'soudan', 'description' => 'Pays d\'Afrique du Nord-Est traversé par le Nil.', 'currency_name' => 'Livre soudanaise'],
                    ['lang_code' => 'en', 'name' => 'Sudan', 'slug' => 'sudan', 'description' => 'A Northeast African country crossed by the Nile.', 'currency_name' => 'Sudanese Pound'],
                ],
            ],
            ['code_2' => 'se', 'code' => 'SWE', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+46', 'currency_code' => 'SEK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Suède', 'slug' => 'suede', 'description' => 'Royaume scandinave, le plus vaste des pays nordiques.', 'currency_name' => 'Couronne suédoise'],
                    ['lang_code' => 'en', 'name' => 'Sweden', 'slug' => 'sweden', 'description' => 'A Scandinavian kingdom, the largest of the Nordic countries.', 'currency_name' => 'Swedish Krona'],
                ],
            ],
            ['code_2' => 'sg', 'code' => 'SGP', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+65', 'currency_code' => 'SGD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Singapour', 'slug' => 'singapour', 'description' => 'Cité-État insulaire d\'Asie du Sud-Est, hub financier mondial.', 'currency_name' => 'Dollar singapourien'],
                    ['lang_code' => 'en', 'name' => 'Singapore', 'slug' => 'singapore', 'description' => 'A Southeast Asian island city-state and global financial hub.', 'currency_name' => 'Singapore Dollar'],
                ],
            ],
            ['code_2' => 'sh', 'code' => 'SHN', 'continent_code' => 'AF', 'type' => 'territory', 'phone_code' => '+290', 'currency_code' => 'SHP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Sainte-Hélène, Ascension et Tristan da Cunha', 'slug' => 'sainte-helene-ascension-tristan-da-cunha', 'description' => 'Territoire britannique isolé de l\'Atlantique Sud.', 'currency_name' => 'Livre de Sainte-Hélène'],
                    ['lang_code' => 'en', 'name' => 'Saint Helena, Ascension and Tristan da Cunha', 'slug' => 'saint-helena-ascension-tristan-da-cunha', 'description' => 'A remote British territory in the South Atlantic.', 'currency_name' => 'Saint Helena Pound'],
                ],
            ],
            ['code_2' => 'si', 'code' => 'SVN', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+386', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Slovénie', 'slug' => 'slovenie', 'description' => 'Petit pays alpin des Balkans.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Slovenia', 'slug' => 'slovenia', 'description' => 'A small alpine Balkan country.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'sj', 'code' => 'SJM', 'continent_code' => 'EU', 'type' => 'territory', 'phone_code' => '+47', 'currency_code' => 'NOK', 'currency_symbol' => 'kr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Svalbard et Jan Mayen', 'slug' => 'svalbard-et-jan-mayen', 'description' => 'Archipels arctiques norvégiens.', 'currency_name' => 'Couronne norvégienne'],
                    ['lang_code' => 'en', 'name' => 'Svalbard and Jan Mayen', 'slug' => 'svalbard-and-jan-mayen', 'description' => 'Norwegian Arctic archipelagos.', 'currency_name' => 'Norwegian Krone'],
                ],
            ],
            ['code_2' => 'sk', 'code' => 'SVK', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+421', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Slovaquie', 'slug' => 'slovaquie', 'description' => 'Pays d\'Europe centrale, ancienne partie de la Tchécoslovaquie.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Slovakia', 'slug' => 'slovakia', 'description' => 'A Central European country, formerly part of Czechoslovakia.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'sl', 'code' => 'SLE', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+232', 'currency_code' => 'SLL', 'currency_symbol' => 'Le',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Sierra Leone', 'slug' => 'sierra-leone', 'description' => 'Pays d\'Afrique de l\'Ouest bordant l\'Atlantique.', 'currency_name' => 'Leone sierra-léonais'],
                    ['lang_code' => 'en', 'name' => 'Sierra Leone', 'slug' => 'sierra-leone', 'description' => 'A West African country on the Atlantic coast.', 'currency_name' => 'Sierra Leonean Leone'],
                ],
            ],
            ['code_2' => 'sm', 'code' => 'SMR', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+378', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Saint-Marin', 'slug' => 'saint-marin', 'description' => 'L\'une des plus anciennes républiques du monde, enclavée en Italie.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'San Marino', 'slug' => 'san-marino', 'description' => 'One of the world\'s oldest republics, enclaved within Italy.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'sn', 'code' => 'SEN', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+221', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Sénégal', 'slug' => 'senegal', 'description' => 'Pays d\'Afrique de l\'Ouest, point le plus occidental du continent.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Senegal', 'slug' => 'senegal', 'description' => 'A West African country, the continent\'s westernmost point.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'so', 'code' => 'SOM', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+252', 'currency_code' => 'SOS', 'currency_symbol' => 'Sh',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Somalie', 'slug' => 'somalie', 'description' => 'Pays de la Corne de l\'Afrique à la plus longue côte du continent.', 'currency_name' => 'Shilling somalien'],
                    ['lang_code' => 'en', 'name' => 'Somalia', 'slug' => 'somalia', 'description' => 'A Horn of Africa country with the continent\'s longest coastline.', 'currency_name' => 'Somali Shilling'],
                ],
            ],
            ['code_2' => 'sr', 'code' => 'SUR', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+597', 'currency_code' => 'SRD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Suriname', 'slug' => 'suriname', 'description' => 'Plus petit pays souverain d\'Amérique du Sud.', 'currency_name' => 'Dollar surinamais'],
                    ['lang_code' => 'en', 'name' => 'Suriname', 'slug' => 'suriname', 'description' => 'The smallest sovereign country in South America.', 'currency_name' => 'Surinamese Dollar'],
                ],
            ],
            ['code_2' => 'ss', 'code' => 'SSD', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+211', 'currency_code' => 'SSP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Soudan du Sud', 'slug' => 'soudan-du-sud', 'description' => 'Pays le plus récent du monde, enclavé en Afrique de l\'Est.', 'currency_name' => 'Livre sud-soudanaise'],
                    ['lang_code' => 'en', 'name' => 'South Sudan', 'slug' => 'south-sudan', 'description' => 'The world\'s newest country, a landlocked East African nation.', 'currency_name' => 'South Sudanese Pound'],
                ],
            ],
            ['code_2' => 'st', 'code' => 'STP', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+239', 'currency_code' => 'STN', 'currency_symbol' => 'Db',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Sao Tomé-et-Principe', 'slug' => 'sao-tome-et-principe', 'description' => 'Petit archipel du golfe de Guinée.', 'currency_name' => 'Dobra santoméen'],
                    ['lang_code' => 'en', 'name' => 'Sao Tome and Principe', 'slug' => 'sao-tome-and-principe', 'description' => 'A small archipelago in the Gulf of Guinea.', 'currency_name' => 'São Tomé and Príncipe Dobra'],
                ],
            ],
            ['code_2' => 'sv', 'code' => 'SLV', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+503', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Salvador', 'slug' => 'salvador', 'description' => 'Plus petit pays d\'Amérique centrale continentale.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'El Salvador', 'slug' => 'el-salvador', 'description' => 'The smallest country in mainland Central America.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'sx', 'code' => 'SXM', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1721', 'currency_code' => 'ANG', 'currency_symbol' => 'ƒ',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Saint-Martin (partie néerlandaise)', 'slug' => 'sint-maarten', 'description' => 'Pays constitutif du royaume des Pays-Bas, dans les Caraïbes.', 'currency_name' => 'Florin des Antilles néerlandaises'],
                    ['lang_code' => 'en', 'name' => 'Sint Maarten', 'slug' => 'sint-maarten', 'description' => 'A constituent country of the Kingdom of the Netherlands, in the Caribbean.', 'currency_name' => 'Netherlands Antillean Guilder'],
                ],
            ],
            ['code_2' => 'sy', 'code' => 'SYR', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+963', 'currency_code' => 'SYP', 'currency_symbol' => '£',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Syrie', 'slug' => 'syrie', 'description' => 'Pays du Levant au riche patrimoine historique.', 'currency_name' => 'Livre syrienne'],
                    ['lang_code' => 'en', 'name' => 'Syria', 'slug' => 'syria', 'description' => 'A Levantine country with a rich historical heritage.', 'currency_name' => 'Syrian Pound'],
                ]],

            // --- Lot 6 : SZ -> ZW ---
            ['code_2' => 'sz', 'code' => 'SWZ', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+268', 'currency_code' => 'SZL', 'currency_symbol' => 'L',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Eswatini', 'slug' => 'eswatini', 'description' => 'Royaume enclavé d\'Afrique australe, anciennement Swaziland.', 'currency_name' => 'Lilangeni swazi'],
                    ['lang_code' => 'en', 'name' => 'Eswatini', 'slug' => 'eswatini', 'description' => 'A landlocked Southern African kingdom, formerly Swaziland.', 'currency_name' => 'Swazi Lilangeni'],
                ],
            ],
            ['code_2' => 'tc', 'code' => 'TCA', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1649', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Turques-et-Caïques', 'slug' => 'iles-turques-et-caiques', 'description' => 'Territoire britannique d\'outre-mer des Caraïbes.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Turks and Caicos Islands', 'slug' => 'turks-and-caicos-islands', 'description' => 'A British Overseas Territory in the Caribbean.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'td', 'code' => 'TCD', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+235', 'currency_code' => 'XAF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tchad', 'slug' => 'tchad', 'description' => 'Vaste pays sahélien enclavé d\'Afrique centrale.', 'currency_name' => 'Franc CFA (CEMAC)'],
                    ['lang_code' => 'en', 'name' => 'Chad', 'slug' => 'chad', 'description' => 'A vast landlocked Sahelian country in Central Africa.', 'currency_name' => 'Central African CFA Franc'],
                ],
            ],
            ['code_2' => 'tf', 'code' => 'ATF', 'continent_code' => 'AN', 'type' => 'territory', 'phone_code' => null, 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Terres australes et antarctiques françaises', 'slug' => 'terres-australes-et-antarctiques-francaises', 'description' => 'Territoire français dispersé entre l\'océan Indien et l\'Antarctique.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'French Southern and Antarctic Lands', 'slug' => 'french-southern-and-antarctic-lands', 'description' => 'A French territory scattered between the Indian Ocean and Antarctica.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'tg', 'code' => 'TGO', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+228', 'currency_code' => 'XOF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Togo', 'slug' => 'togo', 'description' => 'Pays d\'Afrique de l\'Ouest en forme de longue bande.', 'currency_name' => 'Franc CFA (UEMOA)'],
                    ['lang_code' => 'en', 'name' => 'Togo', 'slug' => 'togo', 'description' => 'A West African country shaped as a long narrow strip.', 'currency_name' => 'West African CFA Franc'],
                ],
            ],
            ['code_2' => 'th', 'code' => 'THA', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+66', 'currency_code' => 'THB', 'currency_symbol' => '฿',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Thaïlande', 'slug' => 'thailande', 'description' => 'Pays d\'Asie du Sud-Est jamais colonisé.', 'currency_name' => 'Baht thaïlandais'],
                    ['lang_code' => 'en', 'name' => 'Thailand', 'slug' => 'thailand', 'description' => 'A Southeast Asian country never colonized.', 'currency_name' => 'Thai Baht'],
                ],
            ],
            ['code_2' => 'tj', 'code' => 'TJK', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+992', 'currency_code' => 'TJS', 'currency_symbol' => 'SM',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tadjikistan', 'slug' => 'tadjikistan', 'description' => 'Pays montagneux enclavé d\'Asie centrale.', 'currency_name' => 'Somoni tadjik'],
                    ['lang_code' => 'en', 'name' => 'Tajikistan', 'slug' => 'tajikistan', 'description' => 'A mountainous landlocked Central Asian country.', 'currency_name' => 'Tajikistani Somoni'],
                ],
            ],
            ['code_2' => 'tk', 'code' => 'TKL', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+690', 'currency_code' => 'NZD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tokelau', 'slug' => 'tokelau', 'description' => 'Territoire néo-zélandais composé de trois atolls du Pacifique.', 'currency_name' => 'Dollar néo-zélandais'],
                    ['lang_code' => 'en', 'name' => 'Tokelau', 'slug' => 'tokelau', 'description' => 'A New Zealand territory made up of three Pacific atolls.', 'currency_name' => 'New Zealand Dollar'],
                ],
            ],
            ['code_2' => 'tl', 'code' => 'TLS', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+670', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Timor oriental', 'slug' => 'timor-oriental', 'description' => 'Pays d\'Asie du Sud-Est, l\'un des plus jeunes États du monde.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'Timor-Leste', 'slug' => 'timor-leste', 'description' => 'A Southeast Asian country, one of the world\'s youngest states.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'tm', 'code' => 'TKM', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+993', 'currency_code' => 'TMT', 'currency_symbol' => 'm',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Turkménistan', 'slug' => 'turkmenistan', 'description' => 'Pays désertique d\'Asie centrale riche en gaz naturel.', 'currency_name' => 'Manat turkmène'],
                    ['lang_code' => 'en', 'name' => 'Turkmenistan', 'slug' => 'turkmenistan', 'description' => 'A desert Central Asian country rich in natural gas.', 'currency_name' => 'Turkmenistani Manat'],
                ],
            ],
            ['code_2' => 'tn', 'code' => 'TUN', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+216', 'currency_code' => 'TND', 'currency_symbol' => 'د.ت',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tunisie', 'slug' => 'tunisie', 'description' => 'Pays d\'Afrique du Nord berceau du Printemps arabe.', 'currency_name' => 'Dinar tunisien'],
                    ['lang_code' => 'en', 'name' => 'Tunisia', 'slug' => 'tunisia', 'description' => 'A North African country, birthplace of the Arab Spring.', 'currency_name' => 'Tunisian Dinar'],
                ],
            ],
            ['code_2' => 'to', 'code' => 'TON', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+676', 'currency_code' => 'TOP', 'currency_symbol' => 'T$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tonga', 'slug' => 'tonga', 'description' => 'Royaume polynésien composé de plus de 170 îles.', 'currency_name' => 'Pa\'anga tongien'],
                    ['lang_code' => 'en', 'name' => 'Tonga', 'slug' => 'tonga', 'description' => 'A Polynesian kingdom made up of more than 170 islands.', 'currency_name' => 'Tongan Paʻanga'],
                ],
            ],
            ['code_2' => 'tr', 'code' => 'TUR', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+90', 'currency_code' => 'TRY', 'currency_symbol' => '₺',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Turquie', 'slug' => 'turquie', 'description' => 'Pays transcontinental à cheval sur l\'Europe et l\'Asie.', 'currency_name' => 'Livre turque'],
                    ['lang_code' => 'en', 'name' => 'Turkey', 'slug' => 'turkey', 'description' => 'A transcontinental country spanning Europe and Asia.', 'currency_name' => 'Turkish Lira'],
                ],
            ],
            ['code_2' => 'tt', 'code' => 'TTO', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1868', 'currency_code' => 'TTD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Trinité-et-Tobago', 'slug' => 'trinite-et-tobago', 'description' => 'État insulaire des Caraïbes, berceau du calypso et du steelpan.', 'currency_name' => 'Dollar trinidadien'],
                    ['lang_code' => 'en', 'name' => 'Trinidad and Tobago', 'slug' => 'trinidad-and-tobago', 'description' => 'A Caribbean island nation, birthplace of calypso and the steelpan.', 'currency_name' => 'Trinidad and Tobago Dollar'],
                ],
            ],
            ['code_2' => 'tv', 'code' => 'TUV', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+688', 'currency_code' => 'AUD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tuvalu', 'slug' => 'tuvalu', 'description' => 'Micro-État insulaire du Pacifique menacé par la montée des eaux.', 'currency_name' => 'Dollar australien'],
                    ['lang_code' => 'en', 'name' => 'Tuvalu', 'slug' => 'tuvalu', 'description' => 'A tiny Pacific island nation threatened by rising sea levels.', 'currency_name' => 'Australian Dollar'],
                ],
            ],
            ['code_2' => 'tw', 'code' => 'TWN', 'continent_code' => 'AS', 'type' => 'disputed', 'phone_code' => '+886', 'currency_code' => 'TWD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Taïwan', 'slug' => 'taiwan', 'description' => 'Île d\'Asie de l\'Est au statut politique contesté.', 'currency_name' => 'Dollar taïwanais'],
                    ['lang_code' => 'en', 'name' => 'Taiwan', 'slug' => 'taiwan', 'description' => 'An East Asian island with a disputed political status.', 'currency_name' => 'New Taiwan Dollar'],
                ],
            ],
            ['code_2' => 'tz', 'code' => 'TZA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+255', 'currency_code' => 'TZS', 'currency_symbol' => 'Sh',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Tanzanie', 'slug' => 'tanzanie', 'description' => 'Pays d\'Afrique de l\'Est abritant le Kilimandjaro.', 'currency_name' => 'Shilling tanzanien'],
                    ['lang_code' => 'en', 'name' => 'Tanzania', 'slug' => 'tanzania', 'description' => 'An East African country home to Mount Kilimanjaro.', 'currency_name' => 'Tanzanian Shilling'],
                ],
            ],
            ['code_2' => 'ua', 'code' => 'UKR', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+380', 'currency_code' => 'UAH', 'currency_symbol' => '₴',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Ukraine', 'slug' => 'ukraine', 'description' => 'Pays d\'Europe de l\'Est, l\'un des plus vastes du continent.', 'currency_name' => 'Hryvnia ukrainienne'],
                    ['lang_code' => 'en', 'name' => 'Ukraine', 'slug' => 'ukraine', 'description' => 'An Eastern European country, one of the largest on the continent.', 'currency_name' => 'Ukrainian Hryvnia'],
                ],
            ],
            ['code_2' => 'ug', 'code' => 'UGA', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+256', 'currency_code' => 'UGX', 'currency_symbol' => 'Sh',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Ouganda', 'slug' => 'ouganda', 'description' => 'Pays enclavé d\'Afrique de l\'Est, riche en lacs et volcans.', 'currency_name' => 'Shilling ougandais'],
                    ['lang_code' => 'en', 'name' => 'Uganda', 'slug' => 'uganda', 'description' => 'A landlocked East African country, rich in lakes and volcanoes.', 'currency_name' => 'Ugandan Shilling'],
                ],
            ],
            ['code_2' => 'um', 'code' => 'UMI', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => null, 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles mineures éloignées des États-Unis', 'slug' => 'iles-mineures-eloignees-des-etats-unis', 'description' => 'Ensemble d\'îles américaines dispersées et pour la plupart inhabitées.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'United States Minor Outlying Islands', 'slug' => 'united-states-minor-outlying-islands', 'description' => 'A group of scattered, mostly uninhabited U.S. islands.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'us', 'code' => 'USA', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'États-Unis', 'slug' => 'etats-unis', 'description' => 'Fédération d\'Amérique du Nord, première puissance économique mondiale.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'United States', 'slug' => 'united-states', 'description' => 'A North American federation, the world\'s largest economy.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'uy', 'code' => 'URY', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+598', 'currency_code' => 'UYU', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Uruguay', 'slug' => 'uruguay', 'description' => 'Pays d\'Amérique du Sud entre Argentine et Brésil.', 'currency_name' => 'Peso uruguayen'],
                    ['lang_code' => 'en', 'name' => 'Uruguay', 'slug' => 'uruguay', 'description' => 'A South American country between Argentina and Brazil.', 'currency_name' => 'Uruguayan Peso'],
                ],
            ],
            ['code_2' => 'uz', 'code' => 'UZB', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+998', 'currency_code' => 'UZS', 'currency_symbol' => 'с',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Ouzbékistan', 'slug' => 'ouzbekistan', 'description' => 'Pays d\'Asie centrale sur l\'ancienne route de la soie.', 'currency_name' => 'Som ouzbek'],
                    ['lang_code' => 'en', 'name' => 'Uzbekistan', 'slug' => 'uzbekistan', 'description' => 'A Central Asian country on the historic Silk Road.', 'currency_name' => 'Uzbekistani Som'],
                ],
            ],
            ['code_2' => 'va', 'code' => 'VAT', 'continent_code' => 'EU', 'type' => 'country', 'phone_code' => '+379', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Vatican', 'slug' => 'vatican', 'description' => 'Plus petit État souverain du monde, siège de l\'Église catholique.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Vatican City', 'slug' => 'vatican-city', 'description' => 'The world\'s smallest sovereign state, seat of the Catholic Church.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'vc', 'code' => 'VCT', 'continent_code' => 'NA', 'type' => 'country', 'phone_code' => '+1784', 'currency_code' => 'XCD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Saint-Vincent-et-les-Grenadines', 'slug' => 'saint-vincent-et-les-grenadines', 'description' => 'État insulaire des Caraïbes orientales.', 'currency_name' => 'Dollar des Caraïbes orientales'],
                    ['lang_code' => 'en', 'name' => 'Saint Vincent and the Grenadines', 'slug' => 'saint-vincent-and-the-grenadines', 'description' => 'An island nation in the eastern Caribbean.', 'currency_name' => 'East Caribbean Dollar'],
                ],
            ],
            ['code_2' => 've', 'code' => 'VEN', 'continent_code' => 'SA', 'type' => 'country', 'phone_code' => '+58', 'currency_code' => 'VES', 'currency_symbol' => 'Bs.S',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Venezuela', 'slug' => 'venezuela', 'description' => 'Pays d\'Amérique du Sud aux plus grandes réserves de pétrole prouvées.', 'currency_name' => 'Bolívar souverain'],
                    ['lang_code' => 'en', 'name' => 'Venezuela', 'slug' => 'venezuela', 'description' => 'A South American country with the world\'s largest proven oil reserves.', 'currency_name' => 'Venezuelan Bolívar'],
                ],
            ],
            ['code_2' => 'vg', 'code' => 'VGB', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1284', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Vierges britanniques', 'slug' => 'iles-vierges-britanniques', 'description' => 'Territoire britannique d\'outre-mer des Caraïbes.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'British Virgin Islands', 'slug' => 'british-virgin-islands', 'description' => 'A British Overseas Territory in the Caribbean.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'vi', 'code' => 'VIR', 'continent_code' => 'NA', 'type' => 'territory', 'phone_code' => '+1340', 'currency_code' => 'USD', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Îles Vierges des États-Unis', 'slug' => 'iles-vierges-des-etats-unis', 'description' => 'Territoire non incorporé des États-Unis dans les Caraïbes.', 'currency_name' => 'Dollar américain'],
                    ['lang_code' => 'en', 'name' => 'United States Virgin Islands', 'slug' => 'united-states-virgin-islands', 'description' => 'An unincorporated U.S. territory in the Caribbean.', 'currency_name' => 'US Dollar'],
                ],
            ],
            ['code_2' => 'vn', 'code' => 'VNM', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+84', 'currency_code' => 'VND', 'currency_symbol' => '₫',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Viêt Nam', 'slug' => 'viet-nam', 'description' => 'Pays d\'Asie du Sud-Est en forme de S le long de la mer de Chine méridionale.', 'currency_name' => 'Dông vietnamien'],
                    ['lang_code' => 'en', 'name' => 'Vietnam', 'slug' => 'vietnam', 'description' => 'An S-shaped Southeast Asian country along the South China Sea.', 'currency_name' => 'Vietnamese Đồng'],
                ],
            ],
            ['code_2' => 'vu', 'code' => 'VUT', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+678', 'currency_code' => 'VUV', 'currency_symbol' => 'VT',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Vanuatu', 'slug' => 'vanuatu', 'description' => 'Archipel volcanique du Pacifique Sud.', 'currency_name' => 'Vatu vanuatuan'],
                    ['lang_code' => 'en', 'name' => 'Vanuatu', 'slug' => 'vanuatu', 'description' => 'A volcanic archipelago in the South Pacific.', 'currency_name' => 'Vanuatu Vatu'],
                ],
            ],
            ['code_2' => 'wf', 'code' => 'WLF', 'continent_code' => 'OC', 'type' => 'territory', 'phone_code' => '+681', 'currency_code' => 'XPF', 'currency_symbol' => 'Fr',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Wallis-et-Futuna', 'slug' => 'wallis-et-futuna', 'description' => 'Collectivité française du Pacifique composée de deux archipels.', 'currency_name' => 'Franc CFP'],
                    ['lang_code' => 'en', 'name' => 'Wallis and Futuna', 'slug' => 'wallis-and-futuna', 'description' => 'A French collectivity in the Pacific made up of two archipelagos.', 'currency_name' => 'CFP Franc'],
                ],
            ],
            ['code_2' => 'ws', 'code' => 'WSM', 'continent_code' => 'OC', 'type' => 'country', 'phone_code' => '+685', 'currency_code' => 'WST', 'currency_symbol' => 'T',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Samoa', 'slug' => 'samoa', 'description' => 'État polynésien du Pacifique Sud.', 'currency_name' => 'Tala samoan'],
                    ['lang_code' => 'en', 'name' => 'Samoa', 'slug' => 'samoa', 'description' => 'A Polynesian state in the South Pacific.', 'currency_name' => 'Samoan Tala'],
                ],
            ],
            ['code_2' => 'xk', 'code' => 'XKX', 'continent_code' => 'EU', 'type' => 'disputed', 'phone_code' => '+383', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Kosovo', 'slug' => 'kosovo', 'description' => 'Territoire des Balkans à l\'indépendance partiellement reconnue.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Kosovo', 'slug' => 'kosovo', 'description' => 'A Balkan territory with partially recognized independence.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'ye', 'code' => 'YEM', 'continent_code' => 'AS', 'type' => 'country', 'phone_code' => '+967', 'currency_code' => 'YER', 'currency_symbol' => '﷼',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Yémen', 'slug' => 'yemen', 'description' => 'Pays du sud de la péninsule arabique.', 'currency_name' => 'Rial yéménite'],
                    ['lang_code' => 'en', 'name' => 'Yemen', 'slug' => 'yemen', 'description' => 'A country in the southern Arabian Peninsula.', 'currency_name' => 'Yemeni Rial'],
                ],
            ],
            ['code_2' => 'yt', 'code' => 'MYT', 'continent_code' => 'AF', 'type' => 'territory', 'phone_code' => '+262', 'currency_code' => 'EUR', 'currency_symbol' => '€',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mayotte', 'slug' => 'mayotte', 'description' => 'Département français d\'outre-mer de l\'océan Indien.', 'currency_name' => 'Euro'],
                    ['lang_code' => 'en', 'name' => 'Mayotte', 'slug' => 'mayotte', 'description' => 'A French overseas department in the Indian Ocean.', 'currency_name' => 'Euro'],
                ],
            ],
            ['code_2' => 'za', 'code' => 'ZAF', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+27', 'currency_code' => 'ZAR', 'currency_symbol' => 'R',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Afrique du Sud', 'slug' => 'afrique-du-sud', 'description' => 'Pays d\'Afrique australe, surnommé "la nation arc-en-ciel".', 'currency_name' => 'Rand sud-africain'],
                    ['lang_code' => 'en', 'name' => 'South Africa', 'slug' => 'south-africa', 'description' => 'A Southern African country, nicknamed "the rainbow nation".', 'currency_name' => 'South African Rand'],
                ],
            ],
            ['code_2' => 'zm', 'code' => 'ZMB', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+260', 'currency_code' => 'ZMW', 'currency_symbol' => 'ZK',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zambie', 'slug' => 'zambie', 'description' => 'Pays enclavé d\'Afrique australe abritant les chutes Victoria.', 'currency_name' => 'Kwacha zambien'],
                    ['lang_code' => 'en', 'name' => 'Zambia', 'slug' => 'zambia', 'description' => 'A landlocked Southern African country home to Victoria Falls.', 'currency_name' => 'Zambian Kwacha'],
                ],
            ],
            ['code_2' => 'zw', 'code' => 'ZWE', 'continent_code' => 'AF', 'type' => 'country', 'phone_code' => '+263', 'currency_code' => 'ZWL', 'currency_symbol' => '$',
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Zimbabwe', 'slug' => 'zimbabwe', 'description' => 'Pays enclavé d\'Afrique australe abritant les ruines du Grand Zimbabwe.', 'currency_name' => 'Dollar zimbabwéen'],
                    ['lang_code' => 'en', 'name' => 'Zimbabwe', 'slug' => 'zimbabwe', 'description' => 'A landlocked Southern African country home to the Great Zimbabwe ruins.', 'currency_name' => 'Zimbabwean Dollar'],
                ]],
        ];

        // Nombre maximum de pays insérés en local, piloté par le setting "geo.max_countries_per_seed".
        $maxCountriesPerSeed = (int) (getSettingValue('max_countries_per_seed', app()->getLocale()));

        // Insérer les pays et leurs traductions dans la base de données
        foreach ($countries as $index => $data) {

            // Si en environnement local on ne seeder que les 25 premiers pays (car ils sont nombreux
            // et cela peut ralentir le processus de seeding)
            if (app()->environment('local') && $index >= $maxCountriesPerSeed) {
                $this->command->info('Environnement local détecté, saut du pays : '.$data['translations'][0]['name'].' (code: '.Str::lower((string) $data['code']).")\n");

                continue; // Passer à l'itération suivante sans insérer le pays
            } else {
                $this->command->info('Insertion du pays : '.$data['translations'][0]['name'].' (code: '.Str::lower((string) $data['code']).")\n");
            }

            if (app()->environment('production')) {
                $this->command->info('Environnement de production détecté, insertion du pays : '.$data['translations'][0]['name'].' (code: '.Str::lower((string) $data['code']).")\n");
            }

            $translations = $data['translations'];

            $continent = GeoContinent::where('code', $data['continent_code'])->first();
            if (! $continent) {
                $this->command->error('Continent non trouvé pour le code : '.$data['continent_code']."\n");

                continue;
            }

            $country = GeoCountry::updateOrCreate(
                ['code_2' => Str::upper($data['code_2'])], // Str::upper() pour s'assurer que le code est en majuscules
                [
                    'continent_id' => $continent->id,
                    'type' => $data['type'],
                    'code' => $data['code'],
                    'phone_code' => $data['phone_code'],
                    'currency_code' => $data['currency_code'] ?? null,
                    'currency_symbol' => $data['currency_symbol'] ?? null,
                    'file_path' => "others/all/geo/countries/images/{$data['code_2']}.png",
                    'sort_order' => $index + 1,
                    'status' => 'active',
                ]
            );

            foreach ($translations as $translation) {
                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();
                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                GeoCountryTranslation::updateOrCreate(
                    ['country_id' => $country->id, 'lang_id' => $lang->id],
                    [
                        'name' => $translation['name'],
                        'slug' => $translation['slug'],
                        'description' => $translation['description'],
                        'currency_name' => $translation['currency_name'] ?? null,
                    ]
                );
            }
        }

        $this->resolveSovereignty();
    }

    /**
     * Résout sovereign_id pour les territoires listés dans $sovereigntyOverrides.
     * Sans effet tant que le pays de rattachement n'existe pas encore en base
     * (sera repris au lot suivant).
     */
    private function resolveSovereignty(): void
    {
        foreach ($this->sovereigntyOverrides as $childCode => $sovereignCode) {
            $child = GeoCountry::where('code_2', $childCode)->first();
            $sovereign = GeoCountry::where('code_2', $sovereignCode)->first();

            if ($child && $sovereign) {
                $child->update(['sovereign_id' => $sovereign->id]);
            }
        }
    }
}
