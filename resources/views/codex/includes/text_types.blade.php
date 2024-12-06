<div class="card mb-3">
    <div class="card-header d-inline-flex justify-content-between">
        Text types
        @if(!isset($edit))
            <div>
                <a href="{{ route('codexText.create') }}" class="btn btn-info badge">
                    New Text
                </a>
                <a href="{{ route('codexTextType.create') }}" class="btn btn-info badge">
                    New Text Type
                </a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @foreach($textTypes as $textType)

            <div class="card border">
                <div class="card-header border-bottom-0">
                            <span class="w-100 d-inline-flex justify-content-between">
                                <span class=" cursor-pointer" data-bs-toggle="collapse"
                                      data-bs-target="#collapse-text{{ $textType->id }}">
                                    <b><i class="fas fa-sm fa-fw fa-caret-down"></i> {{ $textType->type }} ({{$textType->texts_count }})</b>
                                </span>
                                @if(!isset($edit))
                                    <div>
                                        <a href="{{ route('codexTextType.edit', ['codexTextType' => $textType]) }}"
                                           class="btn btn-info badge m-1">Edit</a>
                                         @include('components.modals.confirm-modal', [
                                           'method'=> 'DELETE',
                                            'action' => route('codexTextType.destroy', ['codexTextType' => $textType]),
                                            'classes' => 'btn btn-danger badge m-1',
                                            'text' => 'Delete',
                                            'message' => "Are you sure you want to delete $textType->type? <br> This will also delete <b>all texts</b> in this category",
                                         ])
                                    </div>
                                @endif
                            </span>

                    <div id="collapse-text{{ $textType->id }}" class="collapse">
                        <div class="card-body cursor-default">

                            @foreach($textType->texts as $text)
                                <span class="w-100 d-inline-flex justify-content-between">
                                        @if(isset($edit) && $edit)
                                        <div class="form-check">
                                                 @include('components.forms.checkbox', [
                                                    'input_class_name'=>'',
                                                    'name' => 'textids[]',
                                                    'checked' => in_array($text->id, $myTextTypes),
                                                    'value'=>$text->id,
                                                    'label' => 'Include'
                                                    ])
                                            </div>
                                    @endif

                                    {{ $text->name}}
                                    @if(!isset($edit))
                                        <div>
                                                <a href="{{ route('codexText.edit', ['codexText' => $text]) }}"
                                                   class="btn btn-info badge m-1">Edit</a>
                                                 @include('components.modals.confirm-modal', [
                                                    'method'=> 'DELETE',
                                                    'action' => route('codexText.destroy', ['codexText' => $text]),
                                                    'classes' => 'btn btn-danger badge m-1',
                                                    'text' => 'Delete',
                                                    'message' => "Are you sure you want to delete $text->name?",
                                                 ])
                                             </div>
                                    @endif
                                    </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
