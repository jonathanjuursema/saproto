<?php

namespace App\Http\Controllers;

use App\Libraries\PDF_TOC;
use App\Models\Codex;
use App\Models\CodexSongCategory;
use App\Models\CodexTextType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

define('FPDF_FONTPATH', resource_path('fonts/'));

class CodexController extends Controller
{
    protected string $table = 'codex_codices';

    public function index()
    {
        $codices = Codex::query()->orderBy('name')->get();
        $textTypes = CodexTextType::with('texts')->withCount('texts')->get();
        $songTypes = CodexSongCategory::query()->orderBy('name')->with('songs')->withCount('songs')->get();

        return view('codex.index', ['codices' => $codices, 'textTypes' => $textTypes, 'songTypes' => $songTypes]);
    }

    public function create()
    {
        $textTypes = CodexTextType::with('texts')->withCount('texts')->get();
        $songTypes = CodexSongCategory::query()->orderBy('name')->with('songs')->withCount('songs')->get();

        return view('codex.codex-edit', ['codex' => null, 'textTypes' => $textTypes, 'songTypes' => $songTypes, 'mySongs' => [], 'myTexts' => [], 'myTextTypes' => []]);
    }

    public function store(Request $request)
    {
        $codex = new Codex;
        $this->saveCodex($codex, $request);

        return Redirect::route('codex.index');
    }

    public function edit(Codex $codex)
    {
        $textTypes = CodexTextType::with('texts')->withCount('texts')->get();
        $songTypes = CodexSongCategory::query()->orderBy('name')->with('songs')->withCount('songs')->get();
        $mySongs = $codex->songs->pluck('id')->toArray();
        $myTexts = $codex->texts->pluck('id')->toArray();

        return view('codex.codex-edit', ['codex' => $codex, 'textTypes' => $textTypes, 'songTypes' => $songTypes, 'mySongs' => $mySongs, 'myTextTypes' => $myTexts]);
    }

    public function update(Request $request, Codex $codex)
    {
        $this->saveCodex($codex, $request);

        return Redirect::route('codex.index');
    }

    public function destroy(Codex $codex)
    {
        $codex->songs()->detach();
        $codex->texts()->detach();
        $codex->delete();

        return Redirect::route('codex.index');
    }

    private function saveCodex(Codex $codex, Request $request): void
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'export' => 'required|string|max:255',
            'description' => 'required|string',
            'songids' => 'nullable|array',
            'songids.*' => 'integer',
            'textids' => 'nullable|array',
            'textids.*' => 'integer',
        ]);

        $codex->name = $validated['name'];
        $codex->export = $validated['export'];
        $codex->description = $validated['description'];
        $codex->save();
        $codex->songs()->sync($validated['songids'] ?? []);
        $codex->texts()->sync($validated['textids'] ?? []);
    }

    public function show(Codex $codex)
    {
        $categories = CodexSongCategory::query()->whereHas('songs', function ($q) use ($codex) {
            $q->whereHas('codices', function ($q) use ($codex) {
                $q->where('codex', $codex->id);
            });
        })->with(['songs' => function ($query) use ($codex) {
            $query->whereHas('codices', function ($query) use ($codex) {
                $query->where('codex_codices.id', $codex->id);
            })->orderBy('title');
        }])->orderBy('id')->get();

        $textCategories = CodexTextType::query()->whereHas('texts', function ($q) use ($codex) {
            $q->whereHas('codices', function ($q) use ($codex) {
                $q->where('codex_codices.id', $codex->id);
            });
        })->with(['texts' => function ($query) use ($codex) {
            $query->whereHas('codices', function ($query) use ($codex) {
                $query->where('codex_codices.id', $codex->id);
            });
        }])->orderBy('type')->get();
        if (count($categories) == 0 || count($textCategories) == 0) {
            Session::flash('flash_message', 'You need to add at least one song and one text to the codex first!');

            return Redirect::route('codex.index');
        }

        $A6 = [105, 148];

        $pdf = new PDF_TOC('P', 'mm', $A6);

        $pdf->setMargins(10, 10, 10); // left, top and right margins, 1cm
        $pdf->setAutoPageBreak(true, 10); // bottom margin, 1cm

        $pdf->AddFont('minion', '', 'minion.ttf', true);
        $pdf->AddFont('minion', 'B', 'minionb.ttf', true);
        $pdf->AddFont('minion', 'I', 'minioni.ttf', true);
        $pdf->AddFont('old');

        $textSize = 8;
        $headingSize = 12;
        $textHeight = 9 * 0.352778;
        $bulletListIndent = 7;
        $tocPage = 0;

        $pdf->AddPage(); // frontPage
        $pdf->SetFont('old', '', 52);
        $pdf->Image(public_path('images/logo/codex_logo.png'), 10, 10, 85, 48);
        $pdf->Ln(50);

        $codex_name = str_replace('’', "'", $codex->name);
        $codex_name = str_replace('‘', "'", $codex_name);

        $pdf->MultiCell(0, 22, $codex_name, 0, 'C');
        $pdf->SetAlpha(0.1);
        $pdf->Image(public_path('images/logo/codex_logo.png'), -100, 47, 210);
        $pdf->SetAlpha(1);
        $pdf->AddPage(); // empty page

        foreach ($textCategories as $category) {
            foreach ($category->texts as $text) {
                $pdf->AddPage();
                $pdf->startPageNums();

                $pdf->SetFont('minion', 'B', $textSize);

                $pdf->TOC_Entry($category->type, 0);
                $pdf->MultiCell(0, $textHeight, $category->type, 0, 'L');
                $pdf->SetFont('minion', '', $textSize);

                $textArray = explode(PHP_EOL, $text->text);
                $list = false;
                $count = 0;

                foreach ($textArray as $textValue) {
                    if ($list || preg_match('/(\d+)\./', $textValue)) {
                        $textValue = str_replace('1.', '', $textValue);
                        $list = true;
                        if (preg_match('/(\d+)\./', $textValue) === 0 || preg_match('/(\d+)\./', $textValue) === false) {
                            $list = false;
                        }

                        $count += 1;
                        $pdf->Cell($bulletListIndent, $textHeight, $count.'.');
                    } else {
                        $count = 0;
                    }

                    if (str_contains($textValue, '**')) {
                        $textValue = str_replace('**', '', $textValue);
                        $pdf->SetFont('minion', 'B', $textSize);
                        $toc_text = substr($textValue, 0, -1);
                        $pdf->TOC_Entry($toc_text, 1);
                    } elseif (str_contains($textValue, '_') || str_contains($textValue, '*')) {
                        $textValue = str_replace('_', '', $textValue);
                        $textValue = str_replace('*', '', $textValue);
                        $pdf->SetFont('minion', 'I', $textSize);
                    }

                    $pdf->MultiCell(0, $textHeight, $textValue, 0, 'L');
                    $pdf->SetFont('minion', '', $textSize);
                }

                $tocPage = $pdf->PageNo() + 1;
            }
        }

        foreach ($categories as $category) {
            $pdf->AddPage();
            $pdf->SetFont('minion', 'B', $headingSize);
            $pdf->MultiCell(0, $textHeight, $category->name, 0, 'L');
            $pdf->SetFont('minion', '', $headingSize);
            $pdf->TOC_Entry($category->name, 0);

            foreach ($category->songs as $song) {
                $pdf->SetFont('minion', 'B', $textSize);
                $pdf->MultiCell(0, $textHeight, $song->title, 0, 'L');
                $pdf->SetFont('minion', '', $textSize);
                $link = $pdf->AddLink();
                $pdf->SetLink($link, -1, -1);
                $pdf->TOC_Entry($song->title, 1);
                $lyricsArray = explode(PHP_EOL, $song->lyrics);
                $print = true;
                $counter = count($lyricsArray);
                for ($index = 0; $index < $counter; $index++) {
                    $text = $lyricsArray[$index];
                    $text = str_replace('\\', '', $text);
                    if (str_contains($text, '**')) {
                        $text = str_replace('**', '', $text);
                        $pdf->SetFont('minion', 'B', $textSize);
                    } elseif (str_contains($text, '*') || str_contains($text, '_')) {
                        $text = str_replace('*', '', $text);
                        $text = str_replace('_', '', $text);
                        $pdf->SetFont('minion', 'I', $textSize);
                    } elseif (str_contains($text, ':')) {
                        $subString1 = substr($text, 0, strpos($text, ':'));
                        $subString2 = substr($text, strpos($text, ':'));
                        $pdf->SetFont('minion', 'I', $textSize);
                        $pdf->Cell($pdf->GetStringWidth($subString1), $textHeight, $subString1);
                        $pdf->SetFont('minion', '', $textSize);
                        $pdf->Cell($pdf->GetStringWidth($subString2), $textHeight, $subString2, 0, 1);
                        $print = false;
                    }

                    if ($print) {
                        $pdf->MultiCell(0, $textHeight, $text, 0, 'L');
                        $pdf->SetFont('minion', '', $textSize);
                    }

                    $print = true;
                }

                $pdf->Ln($textHeight);
            }
        }

        $pdf->AddPage(); // TOC, possibly empty pages, and notes page
        $pdf->stopPageNums();
        $pdf->insertTOC($tocPage, 9, 6, 'minion');

        $pagesNeeded = (4 - (($pdf->PageNo() + 1) % 4)) % 4;
        if ($pagesNeeded > 0) {
            for ($index = $pagesNeeded; $index > 0; $index--) {
                $pdf->AddPage();
            }
        }

        $pdf->setY(10);
        $pdf->SetFont('minion', 'B', $textSize);
        $pdf->MultiCell(0, $textHeight, 'Notes:', 0, 'C');

        $pdf->AddPage(); // backcover page
        $pdf->SetAlpha(0.1);
        $pdf->Image(public_path('images/logo/codex_logo.png'), -5, 47, 210);
        $pdf->SetAlpha(1);

        $pdf->Output();

        return null;
    }
}
