<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Models\StorageEntry;

class FileController extends Controller
{
    /**
     * @param  int  $id
     * @param  string  $hash
     * @return Response
     */
    public function get($id, $hash)
    {
        /** @var StorageEntry $entry */
        $entry = StorageEntry::findOrFail($id);

        if ($hash != $entry->hash) {
            abort(404);
        }

        $file = Storage::disk('local')->get($entry->filename);

        $response = new Response($file, 200);
        $response->header('Content-Type', $entry->mime);
        $response->header('Cache-Control', 'max-age=86400, public');
        $response->header('Content-Disposition', sprintf('attachment; filename="%s"', $entry->original_filename));

        return $response;
    }

    /**
     * @param  int  $w
     * @param  int  $h
     * @return Image
     */
    public static function makeImage(StorageEntry $entry, $w, $h)
    {
        $storage = config('filesystems.disks');

        $opts = [
            'w' => $w,
            'h' => $h,
        ];

        ini_set('memory_limit', '512M');
        /* @phpstan-ignore-next-line */
        return Image::cache(function ($image) use ($storage, $entry, $opts) {
            if ($opts['w'] && $opts['h']) {
                $image->make($storage['local']['root'].'/'.$entry->filename)->fit($opts['w'], $opts['h'], function ($constraint) {
                    $constraint->upsize();
                });
            } elseif ($opts['w'] || $opts['h']) {
                $image->make($storage['local']['root'].'/'.$entry->filename)->resize($opts['w'], $opts['h'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                $image->make($storage['local']['root'].'/'.$entry->filename);
            }
        }, 87600);
    }

    /**
     * @param  int  $id
     * @param  string  $hash
     * @return Response
     */
    public function getImage($id, $hash, Request $request)
    {
        /** @var StorageEntry $entry */
        $entry = StorageEntry::findOrFail($id);

        if ($hash != $entry->hash) {
            abort(404);
        }

        $response = new Response($this->makeImage(
            $entry,
            ($request->has('w') ? $request->input('w') : null),
            ($request->has('h') ? $request->input('h') : null)
        ), 200);
        $response->header('Content-Type', $entry->mime);
        $response->header('Cache-Control', 'max-age=86400, public');
        $response->header('Content-Disposition', sprintf('filename="%s"', $entry->original_filename));

        return $response;
    }

    /**
     * @param  string  $printer
     * @param  string  $url
     * @param  int  $copies
     * @return string
     */
    public static function requestPrint($printer, $url, $copies = 1)
    {
        if ($printer == 'document') {
            return 'You cannot do this at the moment. Please use the network printer.';
        }

        $payload = base64_encode(json_encode((object) [
            'secret' => config('app-proto.printer-secret'),
            'url' => $url,
            'printer' => $printer,
            'copies' => $copies,
        ]));

        $result = null;
        try {
            $result = file_get_contents('http://'.config('app-proto.printer-host').':'.config('app-proto.printer-port').'/?data='.$payload);
        } catch (\Exception $e) {
            return 'Exception while connecting to the printer server: '.$e->getMessage();
        }

        return $result !== false ? $result : 'Something went wrong while connecting to the printer server.';
    }
}
