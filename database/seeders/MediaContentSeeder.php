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
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0775, true)) {
            $this->note("WARNING: cannot create {$uploadDir} — posters will not be saved.");
            $this->note('Fix ownership (chown -R www-data:www-data public/uploads) and re-run: php artisan db:seed --class=' . static::class);
        } elseif (!is_writable($uploadDir)) {
            $this->note("WARNING: {$uploadDir} is not writable — posters will not be saved.");
            $this->note('Fix ownership (chown -R www-data:www-data public/uploads) and re-run: php artisan db:seed --class=' . static::class);
        }

        // Step 0 — remove duplicate featured_content rows (same title can end
        // up twice on installs seeded before upsert-by-title was introduced,
        // which shows doubled posters on the homepage sliders).
        $duplicates = DB::table('featured_content')
            ->select('title', DB::raw('MIN(id) as keep_id'))
            ->groupBy('title')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        foreach ($duplicates as $dup) {
            DB::table('featured_content')
                ->where('title', $dup->title)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
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
        $downloaded = 0;
        $alreadyPresent = 0;
        $failed = 0;

        $allItems = array_merge($this->movies(), $this->tvShows());
        foreach ($allItems as $data) {
            // Skip if a valid poster is already stored for this title
            $existing = MediaContent::where('title', $data['title'])->value('thumbnail');
            if ($existing && $existing !== 'uploads/no-image.png') {
                $existingPath = public_path($existing);
                if (file_exists($existingPath) && filesize($existingPath) > 5000) {
                    $alreadyPresent++;
                    continue;
                }
            }

            $poster = $this->resolvePoster($data['_poster_url'], $uploadDir, $data['title']);

            if ($poster['path'] !== 'uploads/no-image.png') {
                MediaContent::where('title', $data['title'])->update(['thumbnail' => $poster['path']]);
                DB::table('featured_content')->where('title', $data['title'])->update(['thumbnail' => $poster['path']]);
                $downloaded++;
                $this->note("  ✓ Poster downloaded: {$data['title']}");
            } else {
                $failed++;
            }
        }

        $this->note("Posters: {$downloaded} downloaded, {$alreadyPresent} already present, {$failed} failed.");
        if ($failed > 0) {
            $this->note('Some posters failed — the server may block outbound access to image.tmdb.org,');
            $this->note('or public/uploads/ is not writable by the PHP user. Fix and re-run with:');
            $this->note('  php artisan db:seed --class=' . static::class);
        }
    }

    /**
     * Resolve a poster for the given title. Posters ship with the codebase in
     * database/seeders/posters/ so a fresh install never depends on outbound
     * network access; downloading from the CDN is only a fallback for titles
     * without a bundled file.
     */
    private function resolvePoster(string $url, string $dir, string $title): array
    {
        $filename = Str::random(40) . '.jpg';
        $localPath = $dir . '/' . $filename;
        $relativePath = 'uploads/2026/May/' . $filename;

        $bundled = database_path('seeders/posters/' . Str::slug($title) . '.jpg');
        if (is_file($bundled) && filesize($bundled) > 5000) {
            if (@copy($bundled, $localPath)) {
                return $this->registerMedia($title, $filename, $localPath, $relativePath);
            }
            $this->note("  ✗ {$title}: cannot write {$localPath} — check public/uploads permissions");
            return ['path' => 'uploads/no-image.png', 'size' => 0];
        }

        return $this->downloadPoster($url, $localPath, $relativePath, $filename, $title);
    }

    private function downloadPoster(string $url, string $localPath, string $relativePath, string $filename, string $title): array
    {
        usleep(300000); // 0.3s pause to avoid CDN rate-limiting

        // Some servers cannot reach image.tmdb.org directly (filtered outbound
        // traffic, regional blocks). Retry through the wsrv.nl image proxy.
        $candidates = [
            $url,
            'https://wsrv.nl/?url=' . urlencode($url),
        ];

        foreach ($candidates as $candidate) {
            $error = null;
            $image = $this->fetchImage($candidate, $error);
            if ($image === null) {
                $this->note("  ✗ {$title}: {$error} ({$candidate})");
                continue;
            }

            if (@file_put_contents($localPath, $image) === false) {
                $this->note("  ✗ {$title}: cannot write {$localPath} — check public/uploads permissions");
                return ['path' => 'uploads/no-image.png', 'size' => 0];
            }

            return $this->registerMedia($title, $filename, $localPath, $relativePath);
        }

        return ['path' => 'uploads/no-image.png', 'size' => 0];
    }

    /**
     * Register the saved poster in the media table so it appears in Media Manager.
     */
    private function registerMedia(string $title, string $filename, string $localPath, string $relativePath): array
    {
        $size = filesize($localPath);

        if (!Media::where('path', $relativePath)->exists()) {
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

    /**
     * Fetch an image over HTTP. Returns the raw bytes, or null with $error
     * set to the reason (curl error, HTTP status, or invalid image body).
     */
    private function fetchImage(string $url, ?string &$error = null): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            // Broken IPv6 on many VPSes makes curl hang until timeout — force IPv4.
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36',
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_FORBID_REUSE   => true,
        ]);
        $image = curl_exec($ch);
        $curlError = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($image === false || $image === '') {
            $error = $curlError !== '' ? $curlError : "empty response (HTTP {$status})";
            return null;
        }
        if ($status >= 400) {
            $error = "HTTP {$status}";
            return null;
        }
        // Filtered networks often return an HTML block page instead of the image.
        if (strlen($image) < 5000 || @getimagesizefromstring($image) === false) {
            $error = 'response is not a valid image (blocked or error page?)';
            return null;
        }

        return $image;
    }

    /**
     * Print progress to the console when run via `db:seed`; stay silent when
     * the seeder is invoked programmatically without a command instance.
     */
    private function note(string $message): void
    {
        $this->command?->line($message);
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/xDGbZ0JJ3mYaGKy4Nzd9Kph6M9L.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/iADOJ8Zymht2JPMoy3R7xceZprc.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/aLVkiINlIeCkcZIzb7XHzPYgO6L.jpg',
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
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'title'          => 'Oppenheimer',
                'subtitle'       => 'The world forever changes.',
                'description'    => 'The story of J. Robert Oppenheimer and his role in the development of the atomic bomb during World War II.',
                'type'           => 'movie',
                'genre'          => 'Drama, History, Thriller',
                'release_year'   => 2023,
                'cast'           => 'Cillian Murphy, Emily Blunt, Robert Downey Jr.',
                'rating'         => 8.1,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'uYPbbksJxIg',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 11,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg',
                'title'          => 'The Batman',
                'subtitle'       => 'Unmask the truth.',
                'description'    => 'Batman ventures into Gotham City\'s underworld when a sadistic killer leaves behind a trail of cryptic clues.',
                'type'           => 'movie',
                'genre'          => 'Action, Crime, Drama',
                'release_year'   => 2022,
                'cast'           => 'Robert Pattinson, Zoë Kravitz, Paul Dano',
                'rating'         => 7.7,
                'badge_text'     => null,
                'trailer_url'    => 'mqqft2x_Aa4',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 12,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/62HCnUTziyWcpDaBO2i1DX17ljH.jpg',
                'title'          => 'Top Gun: Maverick',
                'subtitle'       => 'Feel the need... The need for speed.',
                'description'    => 'After thirty years, Maverick trains a group of Top Gun graduates for a specialized mission the likes of which no living pilot has ever seen.',
                'type'           => 'movie',
                'genre'          => 'Action, Drama',
                'release_year'   => 2022,
                'cast'           => 'Tom Cruise, Miles Teller, Jennifer Connelly',
                'rating'         => 8.2,
                'badge_text'     => 'HOT',
                'trailer_url'    => 'giXco2jaZ_4',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 13,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg',
                'title'          => 'Avatar: The Way of Water',
                'subtitle'       => 'Return to Pandora.',
                'description'    => 'Jake Sully lives with his newfound family formed on the extrasolar moon Pandora — until a familiar threat returns.',
                'type'           => 'movie',
                'genre'          => 'Sci-Fi, Adventure, Action',
                'release_year'   => 2022,
                'cast'           => 'Sam Worthington, Zoe Saldaña, Sigourney Weaver',
                'rating'         => 7.6,
                'badge_text'     => null,
                'trailer_url'    => 'd9MyW72ELq0',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 14,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/vZloFAK7NmvMGKE7VkF5UHaz0I.jpg',
                'title'          => 'John Wick: Chapter 4',
                'subtitle'       => 'No way back, one way out.',
                'description'    => 'John Wick uncovers a path to defeating The High Table, but must face a new enemy with powerful alliances across the globe.',
                'type'           => 'movie',
                'genre'          => 'Action, Thriller, Crime',
                'release_year'   => 2023,
                'cast'           => 'Keanu Reeves, Donnie Yen, Bill Skarsgård',
                'rating'         => 7.7,
                'badge_text'     => null,
                'trailer_url'    => 'qEVUtrk8_B4',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 15,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/8Vt6mWEReuy4Of61Lnj5Xj704m8.jpg',
                'title'          => 'Spider-Man: Across the Spider-Verse',
                'subtitle'       => 'It\'s how you wear the mask that matters.',
                'description'    => 'Miles Morales catapults across the Multiverse, where he encounters a team of Spider-People charged with protecting its very existence.',
                'type'           => 'movie',
                'genre'          => 'Animation, Action, Adventure',
                'release_year'   => 2023,
                'cast'           => 'Shameik Moore, Hailee Steinfeld, Oscar Isaac',
                'rating'         => 8.5,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'cqGjhVJWtEg',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 16,
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/lZwxqNWnT1dRl76ry6BM0FPqCUg.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/jKonm6Q3vw51Ytd4y7bJ70xJT7l.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/ugOtb03Y710JkDaq2ojT0z2nvXq.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/sXZhtWLo3fecavpDuOyJiayjt32.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/m93DIqlwcHWiepTl6WXPiOlw4E9.jpg',
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
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/cOKXV0FalCYixNmZYCfHXgyQ0VX.jpg',
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
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/uKvVjHNqB5VmOrdxqAt2F7J78ED.jpg',
                'title'          => 'The Last of Us',
                'subtitle'       => 'When you\'re lost in the darkness, look for the light.',
                'description'    => 'Twenty years after a fungal outbreak ravages the planet, survivors Joel and Ellie embark on a brutal journey across America.',
                'type'           => 'tv_show',
                'genre'          => 'Drama, Sci-Fi, Action',
                'release_year'   => 2023,
                'seasons'        => 2,
                'episodes'       => 16,
                'cast'           => 'Pedro Pascal, Bella Ramsey, Anna Torv',
                'rating'         => 8.7,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'uLtkt8BonwM',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 21,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/9PFonBhy4cQy7Jz20NpMygczOkv.jpg',
                'title'          => 'Wednesday',
                'subtitle'       => 'Smart, sarcastic and a little dead inside.',
                'description'    => 'Wednesday Addams investigates a monstrous mystery at Nevermore Academy while mastering her emerging psychic ability.',
                'type'           => 'tv_show',
                'genre'          => 'Comedy, Fantasy, Mystery',
                'release_year'   => 2022,
                'seasons'        => 2,
                'episodes'       => 16,
                'cast'           => 'Jenna Ortega, Emma Myers, Catherine Zeta-Jones',
                'rating'         => 8.1,
                'badge_text'     => 'HOT',
                'trailer_url'    => 'Di310WS8zLk',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 22,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg',
                'title'          => 'Stranger Things',
                'subtitle'       => 'Every ending has a beginning.',
                'description'    => 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments, terrifying supernatural forces and one strange little girl.',
                'type'           => 'tv_show',
                'genre'          => 'Sci-Fi, Horror, Drama',
                'release_year'   => 2022,
                'seasons'        => 4,
                'episodes'       => 34,
                'cast'           => 'Millie Bobby Brown, Finn Wolfhard, Winona Ryder',
                'rating'         => 8.6,
                'badge_text'     => null,
                'trailer_url'    => 'b9EkMc79ZSU',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 23,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg',
                'title'          => 'Breaking Bad',
                'subtitle'       => 'All bad things must come to an end.',
                'description'    => 'A chemistry teacher diagnosed with cancer teams up with a former student to secure his family\'s future by making and selling methamphetamine.',
                'type'           => 'tv_show',
                'genre'          => 'Crime, Drama, Thriller',
                'release_year'   => 2013,
                'seasons'        => 5,
                'episodes'       => 62,
                'cast'           => 'Bryan Cranston, Aaron Paul, Anna Gunn',
                'rating'         => 9.5,
                'badge_text'     => 'TOP',
                'trailer_url'    => 'HhesaQXLuRY',
                'is_active'      => true,
                'featured_on_home' => true,
                'sort_order'     => 24,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/1XS1oqL89opfnbLl8WnZY1O1uJx.jpg',
                'title'          => 'Game of Thrones',
                'subtitle'       => 'Winter is coming.',
                'description'    => 'Nine noble families fight for control over the lands of Westeros, while an ancient enemy returns after being dormant for millennia.',
                'type'           => 'tv_show',
                'genre'          => 'Fantasy, Drama, Action',
                'release_year'   => 2019,
                'seasons'        => 8,
                'episodes'       => 73,
                'cast'           => 'Emilia Clarke, Kit Harington, Peter Dinklage',
                'rating'         => 9.2,
                'badge_text'     => null,
                'trailer_url'    => 'KPLWWIOCOOQ',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 25,
            ],
            [
                '_poster_url'    => 'https://image.tmdb.org/t/p/w500/vUUqzWa2LnHIVqkaKVlVGkVcZIW.jpg',
                'title'          => 'Peaky Blinders',
                'subtitle'       => 'By order of the Peaky Blinders.',
                'description'    => 'A gangster family epic set in 1900s England, centering on a gang who sew razor blades in the peaks of their caps.',
                'type'           => 'tv_show',
                'genre'          => 'Crime, Drama',
                'release_year'   => 2022,
                'seasons'        => 6,
                'episodes'       => 36,
                'cast'           => 'Cillian Murphy, Paul Anderson, Sophie Rundle',
                'rating'         => 8.8,
                'badge_text'     => null,
                'trailer_url'    => 'oVzVdvGIC7U',
                'is_active'      => true,
                'featured_on_home' => false,
                'sort_order'     => 26,
            ],
        ];
    }
}
