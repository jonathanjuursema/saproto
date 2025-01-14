@if (isset($newAchievements) && count($newAchievements) > 0)
    <div
        class="modal fade"
        id="new-achievement-modal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="newAchievementModalLabel"
    >
        <div
            class="modal-dialog modal-dialog-centered modal-lg"
            role="document"
        >
            <div class="modal-content bg-dark text-white">
                <div class="modal-body text-center">
                    You just got
                    {{ count($newAchievements) > 1 ? 'new achievements' : 'a new achievement' }},
                    check it out:
                    @foreach ($newAchievements as $newAchievement)
                        @include('achievement.includes.achievement_include', ['achievement' => $newAchievement, 'obtained' => $newAchievement->pivot])
                    @endforeach

                    <a
                        class="btn btn-success"
                        href="{{ route('user::profile') }}"
                    >
                        View all my achievements
                    </a>
                </div>
            </div>
        </div>
    </div>
    @push('javascript')
        <script type="text/javascript" nonce="{{ csp_nonce() }}">
            window.addEventListener('load', (_) => {
                modals['new-achievement-modal'].show()
            })
        </script>
    @endpush
@endif
