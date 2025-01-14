@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Edit Text
@endsection

@section('container')
    @php
        /** @var \App\Models\CodexText $text */
    @endphp

    <form
        action="{{ ! empty($text) ? route('codexText.update', ['codexText' => $text]) : route('codexText.store') }}"
        method="POST"
    >
        <input
            type="hidden"
            name="_method"
            value="{{ ! empty($text) ? 'PUT' : 'POST' }}"
        />
        {{ csrf_field() }}
        <div class="row gap-3 justify-content-center">
            <div class="col-6">
                <div class="row">
                    <div class="card mb-3">
                        <div class="card-header">Text</div>
                        <div class="card-body">
                            <label for="name">Name:</label>
                            <div class="form-group mb-3">
                                <input
                                    type="text"
                                    value="{{ $text->name ?? '' }}"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                />
                            </div>

                            <label for="category">Text category:</label>
                            <select
                                name="category"
                                id="category"
                                class="form-select mb-3"
                                aria-label="Text categories"
                            >
                                @foreach ($textTypes as $textType)
                                    <option
                                        {{ $selectedTextType?->id === $textType->id ? 'selected' : '' }}
                                        value="{{ $textType->id }}"
                                    >
                                        {{ $textType->type }}
                                    </option>
                                @endforeach
                            </select>

                            <label for="text">Text:</label>
                            <div class="form-group mb-3">
                                @include(
                                    'components.forms.markdownfield',
                                    [
                                        'name' => 'text',
                                        'placeholder' => 'Place your text here...',
                                        'value' => $text->text ?? '',
                                    ]
                                )
                            </div>

                            <button
                                type="submit"
                                class="btn btn-success btn-block"
                            >
                                Save text!
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
