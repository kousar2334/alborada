<?php

namespace Database\Seeders;

use App\Models\FeaturedContent;
use App\Models\Media;
use App\Models\MediaContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MediaContentSeeder extends Seeder
{
    public function run(): void
    {
        $uploadDir = public_path('uploads/2026/May');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Step 1 — insert/update all DB records first (no image downloads yet)
        foreach ($this->movies() as $data) {
            $posterUrl = $data['_poster_url'];
            unset($data['_poster_url']);

            MediaContent::updateOrCreate(['title' => $data['title']], $data);

            $this->upsertFeaturedContent($data['title'], [
                'subtitle'     => $data['subtitle'] ?? null,
                'thumbnail'    => $data['thumbnail'] ?? 'uploads/no-image.png',
                'trailer_url'  => $data['trailer_url'] ?? null,
                'type'         => 'movie',
                'genre'        => $data['genre'] ?? null,
                'release_year' => $data['release_year'] ?? null,
                'rating'       => $data['rating'] ?? null,
                'badge_text'   => $data['badge_text'] ?? null,
                'is_active'    => 1,
                'sort_order'   => $data['sort_order'],
            ]);

            // Store poster URL alongside title for step 2
            $data['_poster_url'] = $posterUrl;
        }

        foreach ($this->tvShows() as $data) {
            $posterUrl = $data['_poster_url'];
            unset($data['_poster_url']);

            MediaContent::updateOrCreate(['title' => $data['title']], $data);

            $this->upsertFeaturedContent($data['title'], [
                'subtitle'     => $data['subtitle'] ?? null,
                'thumbnail'    => $data['thumbnail'] ?? 'uploads/no-image.png',
                'trailer_url'  => $data['trailer_url'] ?? null,
                'type'         => 'series',
                'genre'        => $data['genre'] ?? null,
                'release_year' => $data['release_year'] ?? null,
                'rating'       => $data['rating'] ?? null,
                'badge_text'   => $data['badge_text'] ?? null,
                'is_active'    => 1,
                'sort_order'   => $data['sort_order'],
            ]);

            $data['_poster_url'] = $posterUrl;
        }

        // Step 2 — download posters and update thumbnails (failures are non-fatal)
        $allItems = array_merge($this->movies(), $this->tvShows());
        foreach ($allItems as $data) {
            $poster = $this->downloadPoster($data['_poster_url'], $uploadDir, $data['title']);

            if ($poster['path'] !== 'uploads/no-image.png') {
                MediaContent::where('title', $data['title'])->update(['thumbnail' => $poster['path']]);
                DB::table('featured_content')->where('title', $data['title'])->update(['thumbnail' => $poster['path']]);
            }
        }
    }

    private function downloadPoster(string $url, string $dir, string $title): array
    {
        $filename = Str::random(40) . '.jpg';
        $localPath = $dir . '/' . $filename;
        $relativePath = 'uploads/2026/May/' . $filename;

        $ctx = stream_context_create([
            'http' => [
                'timeout'       => 15,
                'user_agent'    => 'Mozilla/5.0 (compatible; AlboradaSeeder/1.0)',
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $image = @file_get_contents($url, false, $ctx);

        if ($image && strlen($image) > 1000) {
            file_put_contents($localPath, $image);
            $size = filesize($localPath);

            // Register in media table so it appears in Media Manager
            $existing = Media::where('path', $relativePath)->first();
            if (!$existing) {
                $media = new Media();
                $media->uuid       = (string) Str::uuid();
                $media->title      = $title;
                $media->file_name  = $filename;
                $media->alt        = $title;
                $media->mime_type  = 'jpg';
                $media->size       = $size;
                $media->path       = $relativePath;
                $media->disk       = 'public';
                $media->save();
            }

            return ['path' => $relativePath, 'size' => $size];
        }

        return ['path' => 'uploads/no-image.png', 'size' => 0];
    }

    private function upsertFeaturedContent(string $title, array $fields): void
    {
        $now = now()->toDateTimeString();
        $existing = DB::table('featured_content')->where('title', $title)->first();

        if ($existing) {
            DB::table('featured_content')
                ->where('title', $title)
                ->update(array_merge($fields, ['updated_at' => $now]));
        } else {
            DB::table('featured_content')
                ->insert(array_merge(['title' => $title], $fields, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
        }
    }

    private function movies(): array
    {
        return [
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'title'          => 'Dune: Part Two',
                'subtitle'       => 'Long live the fighters',
                'description'    => 'Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.',
                'type'           => 'movie',
                'genre'          => 'Sci-Fi, Adventure',
                'release_year'   => 2024,
                'cast'           => 'Timothée Chalamet, Zendaya, Rebecca Ferguson',
                'rating'         => 8.4,
                'badge_text'     => 'NEW',
                'trailer_url'    => 'Way_i-RxzIWw',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 1,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'title'          => 'Deadpool & Wolverine',
                'subtitle'       => 'Marvel\'s best friends.',
                'description'    => 'Deadpool is offered a place in the Marvel Cinematic Universe but instead recruits a variant of Wolverine to help save his world.',
                'type'           => 'movie',
                'genre'          => 'Action, Comedy, Superhero',
                'release_year'   => 2024,
                'cast'           => 'Ryan Reynolds, Hugh Jackman, Emma Corrin',
                'rating'         => 7.8,
                'badge_text'     => 'HOT',
                'trailer_url'    => '73_1biulkYc',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 2,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
                'title'          => 'Inside Out 2',
                'subtitle'       => 'Make room for new emotions.',
                'description'    => 'Riley enters adolescence and Joy, Sadness, Anger, Fear, and Disgust must make room for new emotions.',
                'type'           => 'movie',
                'genre'          => 'Animation, Family',
                'release_year'   => 2024,
                'cast'           => 'Amy Poehler, Maya Hawke, Kensington Tallman',
                'rating'         => 7.6,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'LEjhY15eCx0',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 3,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/b33nnKl1GSFbao4l3fZDDqsMx0F.jpg',
                'title'          => 'Alien: Romulus',
                'subtitle'       => 'Do you want to live forever?',
                'description'    => 'A group of young space colonizers come face to face with the most terrifying life form in the universe.',
                'type'           => 'movie',
                'genre'          => 'Horror, Sci-Fi, Thriller',
                'release_year'   => 2024,
                'cast'           => 'Cailee Spaeny, David Jonsson, Archie Renaux',
                'rating'         => 7.4,
                'badge_text'     => null,
                'trailer_url'    => 'aPoC-1s_q4I',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 4,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/pjnD08FlMAIXsfOLKQbvjc8iu4M.jpg',
                'title'          => 'Gladiator II',
                'subtitle'       => 'If you don\'t fight, you die.',
                'description'    => 'Years after witnessing the death of the revered hero Maximus, Lucius is forced to enter the Colosseum after his home is conquered by the tyrannical Emperors.',
                'type'           => 'movie',
                'genre'          => 'Action, Adventure, Drama',
                'release_year'   => 2024,
                'cast'           => 'Paul Mescal, Denzel Washington, Pedro Pascal',
                'rating'         => 7.1,
                'badge_text'     => null,
                'trailer_url'    => 'DGXILKm-5IM',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 5,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwLJV.jpg',
                'title'          => 'Wicked',
                'subtitle'       => 'The untold story of the witches of Oz.',
                'description'    => 'The story of Elphaba and Glinda, friends whose lives are changed forever after their initial encounter in the land of Oz.',
                'type'           => 'movie',
                'genre'          => 'Fantasy, Musical, Drama',
                'release_year'   => 2024,
                'cast'           => 'Ariana Grande, Cynthia Erivo, Jonathan Bailey',
                'rating'         => 7.8,
                'badge_text'     => 'HOT',
                'trailer_url'    => '6COmYeLsz4c',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 6,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/j3Z3XktmWB1VhsS8iXNCwkmzIN3.jpg',
                'title'          => 'Furiosa: A Mad Max Saga',
                'subtitle'       => 'The origin of the most iconic warrior.',
                'description'    => 'The origin story of the renegade warrior Furiosa before her encounter and clash with the Immortan Joe.',
                'type'           => 'movie',
                'genre'          => 'Action, Thriller, Sci-Fi',
                'release_year'   => 2024,
                'cast'           => 'Anya Taylor-Joy, Chris Hemsworth, Tom Burke',
                'rating'         => 7.8,
                'badge_text'     => null,
                'trailer_url'    => 'XJMuhwVlca4',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 7,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/tElnmtQ6yz1PjN1kePNl8yMSb59.jpg',
                'title'          => 'Kingdom of the Planet of the Apes',
                'subtitle'       => 'Many generations after Caesar\'s reign.',
                'description'    => 'Many years after the reign of Caesar, a young ape goes on a journey that will lead him to question everything he was taught.',
                'type'           => 'movie',
                'genre'          => 'Sci-Fi, Action, Adventure',
                'release_year'   => 2024,
                'cast'           => 'Owen Teague, Freya Allan, Kevin Durand',
                'rating'         => 6.9,
                'badge_text'     => null,
                'trailer_url'    => 'XpwoY-fOC7o',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 8,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/scdBSEEm3jwxnOxMR4rFmKDyiRt.jpg',
                'title'          => 'Moana 2',
                'subtitle'       => 'A new voyage.',
                'description'    => 'Moana and her crew embark on a far-flung voyage into the unknown seas of Oceania, journeying to the forgotten island kingdom of Motufetu.',
                'type'           => 'movie',
                'genre'          => 'Animation, Adventure, Family',
                'release_year'   => 2024,
                'cast'           => 'Auli\'i Cravalho, Dwayne Johnson, Alan Tudyk',
                'rating'         => 6.8,
                'badge_text'     => 'NEW',
                'trailer_url'    => 'OGdXfs84kgA',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 9,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/hU42CRk14JuPEdqZG3AWmagiPAP.jpg',
                'title'          => 'The Substance',
                'subtitle'       => 'Have you tried The Substance?',
                'description'    => 'A fading celebrity uses a black market drug to create a younger, alternate version of herself.',
                'type'           => 'movie',
                'genre'          => 'Horror, Sci-Fi, Thriller',
                'release_year'   => 2024,
                'cast'           => 'Demi Moore, Margaret Qualley, Dennis Quaid',
                'rating'         => 7.3,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'lN5dL5p2OMk',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 10,
            ],
        ];
    }

    private function tvShows(): array
    {
        return [
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/z2yahl2uefxDCl0nogcRBstwruJ.jpg',
                'title'          => 'Shogun',
                'subtitle'       => 'Power is not given. It is taken.',
                'description'    => 'A dangerous, ambitious Englishman arrives in Japan and becomes entangled in a struggle for control of the country.',
                'type'           => 'tv_show',
                'genre'          => 'Drama, Historical, Action',
                'release_year'   => 2024,
                'seasons'        => 1,
                'episodes'       => 10,
                'cast'           => 'Hiroyuki Sanada, Cosmo Jarvis, Anna Sawai',
                'rating'         => 8.9,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'haViVMFWpFI',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 11,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/rqbCbjB19amtOtFQbb3K2lgm2zv.jpg',
                'title'          => 'Fallout',
                'subtitle'       => 'The end. A new beginning.',
                'description'    => 'In a future, post-apocalyptic Los Angeles, a sheltered woman ventures out into the wasteland and meets various inhabitants.',
                'type'           => 'tv_show',
                'genre'          => 'Sci-Fi, Action, Adventure',
                'release_year'   => 2024,
                'seasons'        => 1,
                'episodes'       => 8,
                'cast'           => 'Ella Purnell, Aaron Moten, Walton Goggins',
                'rating'         => 8.5,
                'badge_text'     => 'HOT',
                'trailer_url'    => '4nPHGHqNiYE',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 12,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/t5vTYfa3JQEKSAeDSjRLVnFHJdH.jpg',
                'title'          => 'House of the Dragon Season 2',
                'subtitle'       => 'Fire and blood.',
                'description'    => 'The Targaryen civil war — the Dance of the Dragons — reaches its most violent phase as each side suffers heavy losses.',
                'type'           => 'tv_show',
                'genre'          => 'Fantasy, Drama, Action',
                'release_year'   => 2024,
                'seasons'        => 2,
                'episodes'       => 16,
                'cast'           => 'Matt Smith, Emma D\'Arcy, Olivia Cooke',
                'rating'         => 8.2,
                'badge_text'     => 'NEW',
                'trailer_url'    => 'DotnJ7tTA34',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 13,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/sKCr78MXSLixwmZ8DyJLrpMsd15.jpg',
                'title'          => 'The Bear',
                'subtitle'       => 'Every second counts.',
                'description'    => 'A young chef from the fine-dining world comes home to Chicago to run his family\'s sandwich shop.',
                'type'           => 'tv_show',
                'genre'          => 'Drama, Comedy',
                'release_year'   => 2024,
                'seasons'        => 3,
                'episodes'       => 28,
                'cast'           => 'Jeremy Allen White, Ebon Moss-Bachrach, Ayo Edebiri',
                'rating'         => 8.7,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'qKjGJZzDr8s',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 14,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/dKPtGhA2l2DZPVS9KeXpUMgTqMk.jpg',
                'title'          => 'The Penguin',
                'subtitle'       => 'Gotham\'s new crime boss.',
                'description'    => 'The story of Oz Cobb, also known as the Penguin, and his rise to power in Gotham\'s criminal underworld.',
                'type'           => 'tv_show',
                'genre'          => 'Crime, Drama, Action',
                'release_year'   => 2024,
                'seasons'        => 1,
                'episodes'       => 8,
                'cast'           => 'Colin Farrell, Cristin Milioti, Rhenzy Feliz',
                'rating'         => 8.5,
                'badge_text'     => 'HOT',
                'trailer_url'    => 'u83JLnXzCWA',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 15,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/ypkCPKLaS0hOeFBMVeBrpiL5FpI.jpg',
                'title'          => 'Agatha All Along',
                'subtitle'       => 'The Witches\' Road begins.',
                'description'    => 'Agatha Harkness sets down the Witches Road, a magical gauntlet of trials that she and her coven members must overcome.',
                'type'           => 'tv_show',
                'genre'          => 'Superhero, Fantasy, Comedy',
                'release_year'   => 2024,
                'seasons'        => 1,
                'episodes'       => 9,
                'cast'           => 'Kathryn Hahn, Joe Locke, Aubrey Plaza',
                'rating'         => 7.5,
                'badge_text'     => 'NEW',
                'trailer_url'    => '5EvZTgsyDaQ',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 16,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/rl0B7PoSKiA2vYYb3MnTHf4LFKK.jpg',
                'title'          => 'Squid Game Season 2',
                'subtitle'       => 'The game is back.',
                'description'    => 'Player 456 returns to the deadly game, this time with a new purpose — to find the man behind the curtain.',
                'type'           => 'tv_show',
                'genre'          => 'Thriller, Drama, Action',
                'release_year'   => 2024,
                'seasons'        => 2,
                'episodes'       => 13,
                'cast'           => 'Lee Jung-jae, Lee Byung-hun, Wi Ha-jun',
                'rating'         => 7.7,
                'badge_text'     => 'HOT',
                'trailer_url'    => 'oqxAJRvndBk',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 17,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/6TPZSJ06OEXeelx1U1VIAt0j9Ry.jpg',
                'title'          => 'Severance Season 2',
                'subtitle'       => 'Your work life and personal life are completely separate.',
                'description'    => 'Mark leads a team of office workers whose memories have been surgically divided between their work and personal lives.',
                'type'           => 'tv_show',
                'genre'          => 'Drama, Thriller, Sci-Fi',
                'release_year'   => 2025,
                'seasons'        => 2,
                'episodes'       => 20,
                'cast'           => 'Adam Scott, Britt Lower, Zach Cherry',
                'rating'         => 9.0,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'xEQP4VVuyrY',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 18,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/iFlC5gHMFjKBdoZDcDRzlEHPF4x.jpg',
                'title'          => 'The Lord of the Rings: The Rings of Power Season 2',
                'subtitle'       => 'The dark lord rises.',
                'description'    => 'The second age of Middle-earth continues as Sauron rises to power and forges his rings of power.',
                'type'           => 'tv_show',
                'genre'          => 'Fantasy, Adventure, Drama',
                'release_year'   => 2024,
                'seasons'        => 2,
                'episodes'       => 16,
                'cast'           => 'Morfydd Clark, Robert Aramayo, Charlie Vickers',
                'rating'         => 7.3,
                'badge_text'     => null,
                'trailer_url'    => 'TK-GDmGGTao',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 19,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/aE3yAGBrEOjqiZ4pRhLHdMnJkJe.jpg',
                'title'          => 'The Diplomat',
                'subtitle'       => 'No one said diplomacy was easy.',
                'description'    => 'A career diplomat lands in a high-profile job she is unsuited for in temperament but perfect for in expertise: US Ambassador to the UK.',
                'type'           => 'tv_show',
                'genre'          => 'Drama, Thriller, Political',
                'release_year'   => 2024,
                'seasons'        => 2,
                'episodes'       => 16,
                'cast'           => 'Keri Russell, Rufus Sewell, David Gyasi',
                'rating'         => 8.1,
                'badge_text'     => 'NEW',
                'trailer_url'    => 'C8f7lF1_bsg',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 20,
            ],
        ];
    }
}
