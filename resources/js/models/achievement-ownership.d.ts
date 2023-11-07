type AchievementOwnership = {
    id: number;
    achievement_id: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    alerted: boolean;
    description: string | null;
    user?: User | null;
    achievement?: Achievement | null;
}
