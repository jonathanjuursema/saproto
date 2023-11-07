type Activity = {
    id: number;
    event_id: number | null;
    price: number | null;
    no_show_fee: number;
    participants: number;
    hide_participants: boolean;
    attendees: number | null;
    registration_start: number;
    registration_end: number;
    deregistration_end: number;
    closed: boolean;
    closed_account: number | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    comment: string | null;
    redirect_url: string | null;
}
