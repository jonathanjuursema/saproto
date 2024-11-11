<div class="card mb-3">
    <div class="card-header bg-dark text-white"><i class="fas fa-images fa-fw me-2"></i> Recent photo albums</div>
    <div class="card-body">

        @foreach($albums as $album)

            @include('website.home.cards.card-bg-image', [
            'url' => route('photo::album::list', ['id' => $album->id]) ,
            'img' => $album->thumb(),
            'html' => sprintf('<sub>%s</sub><br><strong>%s</strong>', date("M j, Y", $album->date_taken), $album->name),
            'leftborder' => 'info'
            ])

        @endforeach

        <a href="{{ route('photo::albums') }}" class="btn btn-info btn-block">All photos</a>

    </div>
</div>
