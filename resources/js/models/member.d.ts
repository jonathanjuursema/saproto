type Member = {
    id: number;
    user_id: number;
    proto_username: string | null;
    membership_form_id: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    is_pet: boolean;
    is_lifelong: boolean;
    is_honorary: boolean;
    is_donor: boolean;
    is_pending: boolean;
    until: number | null;
    deleted_at: string /* Date */;
    card_printed_on: string /* Date */ | null;
    omnomcom_sound_id: number | null;
    member_type?: string | null;
}
