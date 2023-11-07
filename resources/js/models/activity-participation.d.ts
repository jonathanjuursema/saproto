type ActivityParticipation = {
    id: number;
    activity_id: number;
    user_id: number;
    is_present: boolean;
    committees_activities_id: number | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    backup: boolean;
    deleted_at: string /* Date */;
}
