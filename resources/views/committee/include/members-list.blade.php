<div id="committee_collapse">
    <?php $display = true; ?>

    @if (count($members['editions']) > 0)
        @foreach ($members['editions'] as $edition => $memberships)
            @include(
                'committee.include.render-memberships',
                [
                    'committee' => $committee,
                    'title' => sprintf(
                        '%s <strong>%s</strong>',
                        $committee->name,
                        $edition,
                    ),
                    'memberships' => $memberships,
                    'unique' => md5($edition),
                    'display' => $display,
                ]
            )

            <?php $display = false; ?>
        @endforeach
    @endif

    @if (count($members['members']['current']) > 0)
        @include(
            'committee.include.render-memberships',
            [
                'committee' => $committee,
                'memberships' => $members['members']['current'],
                'title' => 'Current members',
                'unique' => 'current',
                'display' => $display,
            ]
        )

        <?php $display = false; ?>
    @endif

    @if (count($members['members']['future']) > 0)
        @include(
            'committee.include.render-memberships',
            [
                'committee' => $committee,
                'memberships' => $members['members']['future'],
                'title' => 'Future members',
                'unique' => 'future',
                'display' => $display,
            ]
        )

        <?php $display = false; ?>
    @endif

    @if (count($members['members']['past']) > 0)
        @include(
            'committee.include.render-memberships',
            [
                'committee' => $committee,
                'memberships' => $members['members']['past'],
                'title' => 'Previous members',
                'unique' => 'previous',
                'display' => $display,
            ]
        )

        <?php $display = false; ?>
    @endif
</div>
