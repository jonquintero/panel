<?php

namespace App\Http\Controllers;

use App\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function index()
    {
        $title = "Listado de profesiones";
        return view('professions.index', [
            'title' => $title,
           'professions' => Profession::query()
                ->withCount('profiles')
               ->orderBy('title')
               ->get(),
        ]);
    }

    public function destroy(Profession $profession)
    {
        abort_if($profession->profiles()->exists(), 400, 'Cannot delete a profession linked to a profile. ');
        $profession->delete();

        return redirect('profesiones');
    }
}
