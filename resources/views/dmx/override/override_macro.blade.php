<tr class="{{ $override->active() ? '' : 'opacity-50' }}">

    <td>
        @foreach($override->getFixtures() as $fixture)
            {{ $fixture->name }}<br>
        @endforeach
    </td>
    <td>
        <span class="text-danger">
            <i class="fas fa-tint" aria-hidden="true"></i>
            {{ $override->red() }}
        </span>
    </td>
    <td>
        <span class="text-primary">
            <i class="fas fa-tint" aria-hidden="true"></i>
            {{ $override->green() }}
        </span>
    </td>
    <td>
        <span class="text-info">
            <i class="fas fa-tint" aria-hidden="true"></i>
            {{ $override->blue() }}
        </span>
    </td>
    <td>
        <i class="fas fa-sun" aria-hidden="true"></i>
        {{ $override->brightness() }}
    </td>
    <td>
        Start: {{ date('l F j Y, H:i', $override->start) }}<br>
        End: {{ date('l F j Y, H:i', $override->end) }}
    </td>
    <td>
        <a class="btn btn-xs btn-default me-2"
           href="{{ route('dmx.overrides.edit', ['override' => $override->id]) }}">
            <i class="fas fa-edit"></i>
        </a>
        @include('components.modals.confirm-modal', [
            'action' => route('dmx.overrides.destroy', ['override' => $override]),
            'method'=>'DELETE',
            'classes' => 'fas fa-trash text-danger',
            'text' => '',
            'confirm' => 'Delete',
            'title' => 'Confirm deleting the override',
            'message' => 'Are you sure you want to delete this override?',
        ])
    </td>

</tr>
