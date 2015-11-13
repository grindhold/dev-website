<?php namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use OParl\Spec\LiveVersionRepository;

class SpecificationController extends Controller
{
    /**
     * Show the specification's live copy
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LiveVersionRepository $livecopy)
    {
        $title = 'Spezifikation';

        return view('specification.index', compact('livecopy', 'title'));
    }

    public function imageIndex()
    {
        abort(404);
    }

    public function image(Filesystem $fs, $image)
    {
        return $fs->get(LiveVersionRepository::getImagesPath($image));
    }

    public function raw(LiveVersionRepository $livecopy)
    {
        return response($livecopy->getRaw(), 200, ['Content-type' => 'text/plain']);
    }
}
