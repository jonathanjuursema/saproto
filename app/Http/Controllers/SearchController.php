<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Committee;
use App\Models\Event;
use App\Models\Page;
use App\Models\PhotoAlbum;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function search(Request $request): View
    {
        $term = $request->input('query');

        $users = [];
        if (Auth::user()?->is_member) {
            $presearch_users = $this->getGenericSearchQuery(
                User::class,
                $term,
                Auth::user()->can('board') ? ['id', 'name', 'calling_name', 'utwente_username', 'email'] : ['id', 'name', 'calling_name', 'email']
            )?->get();

            if ($presearch_users) {
                foreach ($presearch_users as $user) {
                    /** @var User $user */
                    if ($user->is_member) {
                        $users[] = $user;
                    }
                }
            }
        }

        $pages = [];
        $presearch_pages = $this->getGenericSearchQuery(
            Page::class,
            $term,
            ['slug', 'title', 'content']
        )?->get();
        if ($presearch_pages) {
            foreach ($presearch_pages as $page) {
                /** @var Page $page */
                if (! $page->is_member_only || Auth::user()?->is_member) {
                    $pages[] = $page;
                }
            }
        }

        $committees = [];
        $presearch_committees = $this->getGenericSearchQuery(
            Committee::class,
            $term,
            ['id', 'name', 'slug']
        )?->get();
        if ($presearch_committees) {
            foreach ($presearch_committees as $committee) {
                /** @var Committee $committee */
                if ($committee->public || Auth::user()?->can('board')) {
                    $committees[] = $committee;
                }
            }
        }

        $presearch_event_ids = $this->getGenericSearchQuery(
            Event::class,
            $term,
            ['id', 'title']
        )?->pluck('id');

        $events = collect();
        if ($presearch_event_ids) {
            //load the events with all the correct data to show in the event block
            Event::getEventBlockQuery()->whereIn('id', $presearch_event_ids)->get()->each(static function ($event) use ($events) {
                if ($event->mayViewEvent(Auth::user())) {
                    $events->push($event);
                }
            });
        }

        $photoAlbums = [];
        $presearch_photo_albums = $this->getGenericSearchQuery(
            PhotoAlbum::class,
            $term,
            ['id', 'name']
        )?->get();
        if ($presearch_photo_albums) {
            foreach ($presearch_photo_albums as $album) {
                /** @var PhotoAlbum $album */
                if (! $album->private || Auth::user()?->can('protography')) {
                    $photoAlbums[] = $album;
                }
            }
        }

        return view('search.search', [
            'term' => $term,
            'users' => $users,
            'pages' => $pages,
            'committees' => $committees,
            'events' => $events->reverse(),
            'photoAlbums' => $photoAlbums,
        ]);
    }

    public function ldapSearch(Request $request)
    {
        if (! $request->has('query')) {
            Session::flash('flash_message', 'Please include a search query.');

            return Redirect::back();
        }

        $query = $request->input('query');
        if (preg_match('/^[a-zA-Z0-9\s\-]+$/', $query) !== 1) {
            Session::flash('flash_message', 'You cannot use special characters in your search query.');

            return Redirect::back();
        }

        if (strlen($query) < 3) {
            Session::flash('flash_message', 'Please make your search term more than three characters.');

            return Redirect::back();
        }

        $terms = explode(' ', $query);
        //make the search match all the terms, and is an active account
        $search = '(&(extensionattribute6=actief)';
        foreach ($terms as $term) {
            //or all the individual fields
            $search .= "(|(sn=*{$term}*)(middlename=*{$term}*)(givenName=*{$term}*)(telephoneNumber=*{$term}*)(otherTelephone=*{$term}*)(physicalDeliveryOfficeName=*{$term}*)";
            if (Auth::user()->can('board')) {
                $search .= "(userPrincipalName={$term}@utwente.nl)";
            }

            $search .= ')';
        }

        //close the search
        $search .= ')';

        $result = LdapController::searchUtwente($search);
        //check that we have a valid response
        if (isset($result->error)) {
            Session::flash('flash_message', 'Something went wrong while searching the UT LDAP server.'.($result->error ? ' '.$result->error : ''));

            return Redirect::back();
        }

        return view('search.ldapsearch', [
            'term' => $query,
            'data' => $result->result,
        ]);
    }

    public function openSearch(): Response
    {
        return response()->view('search.opensearch')->header('Content-Type', 'text/xml');
    }

    public function getUserSearch(Request $request): array
    {
        $search_attributes = ['id', 'name', 'calling_name', 'utwente_username', 'email'];
        $result = [];
        foreach ($this->getGenericSearchQuery(User::class, $request->get('q'), $search_attributes)?->get() ?? [] as $user) {
            /** @var User $user */
            $result[] = (object) [
                'id' => $user->id,
                'name' => $user->name,
                'is_member' => $user->is_member,
            ];
        }

        return $result;
    }

    public function getEventSearch(Request $request): ?Collection
    {
        $search_attributes = ['id', 'title'];

        return $this->getGenericSearchQuery(Event::class, $request->get('q'), $search_attributes)?->get();
    }

    public function getCommitteeSearch(Request $request): ?Collection
    {
        $search_attributes = ['id', 'name', 'slug'];

        return $this->getGenericSearchQuery(Committee::class, $request->get('q'), $search_attributes)?->get();
    }

    public function getProductSearch(Request $request): ?Collection
    {
        $search_attributes = ['id', 'name'];

        return $this->getGenericSearchQuery(Product::class, $request->get('q'), $search_attributes)?->get();
    }

    public function getAchievementSearch(Request $request): ?Collection
    {
        $search_attributes = ['id', 'name'];

        return $this->getGenericSearchQuery(Achievement::class, $request->get('q'), $search_attributes)?->get();
    }

    private function getGenericSearchQuery(Model|string $model, ?string $query, array $attributes): ?Builder
    {
        if (empty($query)) {
            return null;
        }

        $terms = explode(' ', str_replace('*', '%', $query));
        $query = $model::query();

        $check_at_least_one_valid_term = false;
        foreach ($terms as $term) {
            if (strlen(str_replace('%', '', $term)) < 3) {
                continue;
            }

            $check_at_least_one_valid_term = true;
        }

        if (! $check_at_least_one_valid_term) {
            return null;
        }

        foreach ($attributes as $attr) {
            $query = $query->orWhere(static function ($query) use ($terms, $attr) {
                foreach ($terms as $term) {
                    if (strlen(str_replace('%', '', $term)) < 3) {
                        continue;
                    }

                    $query = $query->where($attr, 'LIKE', sprintf('%%%s%%', $term));
                }
            });
        }

        return $query;
    }
}
