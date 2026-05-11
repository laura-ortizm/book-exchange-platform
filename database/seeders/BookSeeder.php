<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $chris  = User::where('username', 'chrisvega')->first();
        $laura  = User::where('username', 'lauraortiz')->first();
        $pablo  = User::where('username', 'pablosoriano')->first();

        $fiction  = Category::where('slug', 'fiction')->first();
        $scifi    = Category::where('slug', 'science-fiction')->first();
        $fantasy  = Category::where('slug', 'fantasy')->first();
        $mystery  = Category::where('slug', 'mystery')->first();
        $nonfic   = Category::where('slug', 'non-fiction')->first();
        $academic = Category::where('slug', 'academic')->first();

        $books = [
            // chrisvega's books
            [
                'user_id'     => $chris->id,
                'title'       => 'Sátántangó',
                'author'      => 'László Krasznahorkai',
                'isbn'        => '9788416748679',
                'description' => 'A bleak and hypnotic novel about the collapse of a collective farm in rural Hungary.',
                'condition'   => 'good',
                'category_id' => $fiction->id,
                'cover_image' => 'covers/satantango.jpeg',
            ],
            [
                'user_id'     => $chris->id,
                'title'       => 'Isaiah Has Come',
                'author'      => 'László Krasznahorkai',
                'isbn'        => '9788492649044',
                'description' => 'A visionary novella in which a mysterious figure arrives and upends a community.',
                'condition'   => 'good',
                'category_id' => $fiction->id,
                'cover_image' => 'covers/isaiah.jpeg',
            ],
            [
                'user_id'     => $chris->id,
                'title'       => 'Stranger in a Strange Land',
                'author'      => 'Robert A. Heinlein',
                'isbn'        => '9780441788385',
                'description' => 'A human raised by Martians returns to Earth and challenges its culture and values.',
                'condition'   => 'fair',
                'category_id' => $scifi->id,
                'cover_image' => 'covers/stranger.jpeg',
            ],
            [
                'user_id'     => $chris->id,
                'title'       => 'A Happy Death',
                'author'      => 'Albert Camus',
                'isbn'        => '9788466354967',
                'description' => 'Camus\'s early novel exploring whether it is possible to die happily.',
                'condition'   => 'good',
                'category_id' => $fiction->id,
                'cover_image' => 'covers/happydeath.jpeg',
            ],
            [
                'user_id'     => $chris->id,
                'title'       => 'Sputnik Sweetheart',
                'author'      => 'Haruki Murakami',
                'isbn'        => '9788483102169',
                'description' => 'A quiet love triangle that dissolves into mystery and longing.',
                'condition'   => 'fair',
                'category_id' => $fiction->id,
                'cover_image' => 'covers/sputnik.jpeg',
            ],

            // lauraortiz's books
            [
                'user_id'     => $laura->id,
                'title'       => 'The Hobbit',
                'author'      => 'J.R.R. Tolkien',
                'isbn'        => '9780547928227',
                'description' => 'A fantasy adventure following Bilbo Baggins in Middle-earth.',
                'condition'   => 'good',
                'category_id' => $fantasy->id,
            ],
            [
                'user_id'     => $laura->id,
                'title'       => 'Gone Girl',
                'author'      => 'Gillian Flynn',
                'isbn'        => '9780307588371',
                'description' => 'A psychological thriller about a marriage gone terribly wrong.',
                'condition'   => 'fair',
                'category_id' => $mystery->id,
            ],
            [
                'user_id'     => $laura->id,
                'title'       => 'Neuromancer',
                'author'      => 'William Gibson',
                'isbn'        => '9780441569595',
                'description' => 'The novel that defined the cyberpunk genre.',
                'condition'   => 'poor',
                'category_id' => $scifi->id,
            ],

            [
                'user_id'     => $laura->id,
                'title'       => 'Pride and Prejudice',
                'author'      => 'Jane Austen',
                'isbn'        => '9780141439518',
                'description' => 'A witty romantic novel set in early 19th-century England.',
                'condition'   => 'good',
                'category_id' => $fiction->id,
            ],
            [
                'user_id'     => $laura->id,
                'title'       => 'The Girl with the Dragon Tattoo',
                'author'      => 'Stieg Larsson',
                'isbn'        => '9780307949486',
                'description' => 'A gripping mystery involving a decades-old disappearance in Sweden.',
                'condition'   => 'fair',
                'category_id' => $mystery->id,
            ],

            // pablosoriano's books
            [
                'user_id'     => $pablo->id,
                'title'       => '1984',
                'author'      => 'George Orwell',
                'isbn'        => '9780451524935',
                'description' => 'A dystopian novel about totalitarianism and surveillance.',
                'condition'   => 'good',
                'category_id' => $fiction->id,
            ],
            [
                'user_id'     => $pablo->id,
                'title'       => 'The Name of the Wind',
                'author'      => 'Patrick Rothfuss',
                'isbn'        => '9780756404741',
                'description' => 'First book of the Kingkiller Chronicle fantasy series.',
                'condition'   => 'good',
                'category_id' => $fantasy->id,
            ],
            [
                'user_id'     => $pablo->id,
                'title'       => 'Clean Code',
                'author'      => 'Robert C. Martin',
                'isbn'        => '9780132350884',
                'description' => 'A handbook of agile software craftsmanship.',
                'condition'   => 'fair',
                'category_id' => $academic->id,
            ],
            [
                'user_id'     => $pablo->id,
                'title'       => 'The Pragmatic Programmer',
                'author'      => 'Andrew Hunt & David Thomas',
                'isbn'        => '9780135957059',
                'description' => 'Classic advice for software developers on writing better code.',
                'condition'   => 'good',
                'category_id' => $academic->id,
            ],
            [
                'user_id'     => $pablo->id,
                'title'       => 'Ender\'s Game',
                'author'      => 'Orson Scott Card',
                'isbn'        => '9780812550702',
                'description' => 'A young prodigy is trained to lead Earth\'s defense against an alien threat.',
                'condition'   => 'good',
                'category_id' => $scifi->id,
            ],
        ];

        foreach ($books as $data) {
            Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                array_merge($data, ['status' => 'available'])
            );
        }
    }
}
