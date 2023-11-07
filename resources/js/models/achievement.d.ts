type Achievement = {
    id: number;
    name: string;
    desc: string;
    fa_icon: string | null;
    tier: string;
    has_page: boolean;
    page_name: string | null;
    page_content: string | null;
    is_archived: boolean;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    achievement_ownership?: AchievementOwnership[] | null;
}
