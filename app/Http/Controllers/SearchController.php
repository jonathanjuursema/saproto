<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as SupportResponse;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;
use App\Models\Achievement;
use App\Models\Committee;
use App\Models\Event;
use App\Models\Page;
use App\Models\PhotoAlbum;
use App\Models\Product;
use App\Models\User;
use Session;

class SearchController extends Controller
{
    /**
     * @return View
     */
    public function search(Request $request)
    {
        $term = $request->input('query');

        $users = [];
        if (Auth::user()?->is_member) {
            $presearch_users = $this->getGenericSearch(
                User::class,
                $term,
                Auth::user()->can('board') ? ['id', 'name', 'calling_name', 'utwente_username', 'email'] : ['id', 'name', 'calling_name', 'email']
            );
            foreach ($presearch_users as $user) {
                if ($user->is_member) {
                    $users[] = $user;
                }
            }
        }

        $pages = [];
        $presearch_pages = $this->getGenericSearch(
            Page::class,
            $term,
            ['slug', 'title', 'content']
        );
        foreach ($presearch_pages as $page) {
            if (! $page->is_member_only || Auth::user()?->is_member) {
                $pages[] = $page;
            }
        }

        $committees = [];
        $presearch_committees = $this->getGenericSearch(
            Committee::class,
            $term,
            ['id', 'name', 'slug']
        );
        foreach ($presearch_committees as $committee) {
            if ($committee->public || Auth::user()?->can('board')) {
                $committees[] = $committee;
            }
        }

        $events = [];
        $presearch_events = $this->getGenericSearch(
            Event::class,
            $term,
            ['id', 'title']
        );
        foreach ($presearch_events as $event) {
            if ($event->mayViewEvent(Auth::user())) {
                $events[] = $event;
            }
        }

        $photoAlbums = [];
        $presearch_photo_albums = $this->getGenericSearch(
            PhotoAlbum::class,
            $term,
            ['id', 'name']
        );
        foreach ($presearch_photo_albums as $album) {
            if (! $album->secret || Auth::user()?->can('protography')) {
                $photoAlbums[] = $album;
            }
        }

        return view('search.search', [
            'term' => $term,
            'users' => $users,
            'pages' => $pages,
            'committees' => $committees,
            'events' => array_reverse($events),
            'photoAlbums' => $photoAlbums,
        ]);
    }

    /**
     * @return View
     */
    public function ldapSearch(Request $request)
    {
        $query = null;
        $data = null;
        if ($request->has('query')) {
            $query = $request->input('query');
            if (preg_match('/^[a-zA-Z0-9\s\-]+$/', $query) !== 1) {
                abort(400, 'You cannot use special characters in your search query.');
            }
            if (strlen($query) >= 3) {
                $terms = explode(' ', $query);
                $search = '&';
                foreach ($terms as $term) {
                    if (Auth::user()->can('board')) {
                        $search .= "(|(sn=*$term*)(middlename=*$term*)(givenName=*$term*)(userPrincipalName=$term@utwente.nl)(telephoneNumber=*$term*)(otherTelephone=*$term*)(physicalDeliveryOfficeName=*$term*))";
                    } else {
                        $search .= "(|(sn=*$term*)(middlename=*$term*)(givenName=*$term*)(telephoneNumber=*$term*)(otherTelephone=*$term*)(physicalDeliveryOfficeName=*$term*))";
                    }
                }
                $data = LdapController::searchUtwente($search, true);
            } else {
                Session::flash('flash_message', 'Please make your search term more than three characters.');
            }
        }

        return view('search.ldapsearch', [
            'term' => $query,
            'data' => (array) $data,
        ]);
    }

    /** @return Response */
    public function openSearch()
    {
        return SupportResponse::make(ViewFacade::make('search.opensearch'))->header('Content-Type', 'text/xml');
    }

    /**
     * @return array
     */
    public function getUserSearch(Request $request)
    {
        $search_attributes = ['id', 'name', 'calling_name', 'utwente_username', 'email'];
        $result = [];
        foreach ($this->getGenericSearch(User::class, $request->get('q'), $search_attributes) as $user) {
            $result[] = (object) [
                'id' => $user->id,
                'name' => $user->name,
                'is_member' => $user->is_member,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getEventSearch(Request $request)
    {
        $search_attributes = ['id', 'title'];

        return $this->getGenericSearch(Event::class, $request->get('q'), $search_attributes);
    }

    /**
     * @return array
     */
    public function getCommitteeSearch(Request $request)
    {
        $search_attributes = ['id', 'name', 'slug'];

        return $this->getGenericSearch(Committee::class, $request->get('q'), $search_attributes);
    }

    /**
     * @return array
     */
    public function getProductSearch(Request $request)
    {
        $search_attributes = ['id', 'name'];

        return $this->getGenericSearch(Product::class, $request->get('q'), $search_attributes);
    }

    /**
     * @return array
     */
    public function getAchievementSearch(Request $request)
    {
        $search_attributes = ['id', 'name'];

        return $this->getGenericSearch(Achievement::class, $request->get('q'), $search_attributes);
    }

    /**
     * @param  class-string|Model  $model
     * @param  string  $query
     * @param  string[]  $attributes
     * @return Collection<Model>|array
     */
    private function getGenericSearch($model, $query, $attributes)
    {
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
            return [];
        }

        foreach ($attributes as $attr) {
            $query = $query->orWhere(function ($query) use ($terms, $attr) {
                foreach ($terms as $term) {
                    if (strlen(str_replace('%', '', $term)) < 3) {
                        continue;
                    }
                    $query = $query->where($attr, 'LIKE', sprintf('%%%s%%', $term));
                }
            });
        }

        return $query->get();
    }
}
